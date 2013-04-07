<?php

/**
* piece.php
*
* PHP version 5
*
* @category Chess
* @package  Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/

/** 
* All of these pieces are stored in the board object, all pieces are also
* inherited through the Piece class.
*
* @category Chess
* @package  Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class Piece
{
  public $position;
  public $color;
  public $moves;
  public $straight_deltas = array( array(-1, 0), array(1, 0), array(0, -1), 
    array(0, 1) );
  public $diagonal_deltas = array( array(1, 1), array(1, -1),  array(-1, 1), 
    array(-1, -1) );

  /**
  * Instantiates a piece class with a default position and color.
  *
  * @param array  $position which will be the default position on setup().
  * @param string $color    is "White" or "Black"
  *
  * @return void
  **/
  public function __construct($position, $color)
  {
    $this->position = $position;
    $this->color = $color;
  }

  /**
  * This takes an array, copies it.. throws out offboard positions and returns.
  *
  * @param array  $all_positions are possible moves, some offboard perhaps.
  * @param object $board         the board object with all the pieces.
  *
  * @return array with all the offboard positions removed
  **/
  public function filterOnBoardPossibles($all_positions, $board)
  {
    $onboard_moves = $all_positions;
    foreach ($onboard_moves as $key => $position) {
      if (!$board->IsOnBoard($position)) {
        unset($onboard_moves[$key]);
      }
    }
    return $onboard_moves;
  }

  /**
  * Takes an array, copies it.. throws out positions where your own color
  * is already there, then returns that potentially smaller array.
  *
  * @param array  $positions available positions, including same team spots.
  * @param object $board     contains all the pieces/positions.
  * @param string $color     is "White" or "Black"
  *
  * @return array with all possible moves for $this piece.
  **/
  public function filterNoFriendlyFire($positions, $board, $color)
  {
    $no_friendly_fire = $positions;
    foreach ($no_friendly_fire as $key => $position) {
      if ((is_object($board->Get($position))) 
          && ($board->Get($position)->color === $color)
      ) {
        unset($no_friendly_fire[$key]);
      }
    }
    return $no_friendly_fire;
  }

  /**
  * desc
  *
  * @param array $positions_array contains something like (0, 0) and outputs
  * something like a2
  *
  * @return string eg. a2
  **/
  public function arrayToEnglish($positions_array)
  {
    $english_position = "";
    foreach ($positions_array as $position) {
      $row = (8 - $position[0]);
      $col = ( chr($position[1] + 97) );
      $english_position .= "{$col}{$row}, ";
    }
    return $english_position;
  }

  /**
  * For any given direction, diagonal up/right, down, right, etc this method
  * will push into an array each time it finds an open position or an enemy
  * piece. Otherwise if it finds a friendly piece, the end of the board it
  * will break and not add those positions into the array.
  *
  * @param array  $delta eg. (1,1), which would be diagonal down/left.
  * @param array  $src   is the source piece which might travel the delta
  * @param object $board contains all the pieces/positions
  *
  * @return array with available moves for a given delta direction.
  **/
  public function crawlDelta($delta, $src, $board)
  {  // there is some code smell here
    $possible_moves = array();
    $current_square = array( ($delta[0] + $src[0]), ($delta[1] + $src[1]) );
    $src_piece = $board->Get($src);
    while (($board->IsOnBoard($current_square)) && 
            ( ($board->Get($current_square) == null) || 
              (is_object($board->Get($current_square))
            ) && 
             ($src_piece->color != $board->Get($current_square)->color) ) ) {
      $possible_moves[] = $current_square;
      if ((is_object($board->Get($current_square))) 
          && ($src_piece->color != $board->Get($current_square)->color)
      ) { 
        break; 
      }
      $current_square = array( ($current_square[0] + $delta[0]), 
                               ($current_square[1] + $delta[1]) 
                        );
    }
    return $possible_moves;
  }

  /**
  * Takes a set of deltas, say diag or straight then it processes them one by
  * one passing them to crawlDelta to get available position from crawlDelta
  * For any given quantity of deltas, it returns the available positions
  * aggregated for all the deltas.
  *
  * @param array  $deltas a set of deltas ($this->straight_deltas)
  * @param array  $src    is the source piece which might travel the delta
  * @param object $board  contains all the pieces/positions
  *
  * @return array with available moves for a given delta direction.
  **/
  public function getDeltaLines($deltas, $src, $board)
  {
    $possible_moves = array();
    foreach ($deltas as $delta) {
      foreach ($this->crawlDelta($delta, $src, $board) as $possible_move ) {
        $possible_moves[] = $possible_move;
      }
    }
    return $possible_moves;
  }
}

/** 
* Pawn piece class which has one method returning available moves.
*
* @category Chess
* @package  Pawn_Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class Pawn extends Piece
{
  /**
  * desc
  *
  * @param object $board which contains all the positions of all pieces.
  *
  * @return array with all possible moves for $this piece.
  **/
  public function getPossibleMoves($board)
  {
    $row = $this->position[0]; // there is code smell here
    $col = $this->position[1]; // the cyclomatic complexity is too high
    $moves = $this->moves;
    $direction = (($this->color == "White") ? -1 : 1);
    $possible_moves = array();
    if ($board->Get(array(($row + $direction), $col)) == null) {
      // allowed to go one forward if the space is empty.
      $possible_moves[] = array( ($row + $direction), $col);
    }
    if (($moves == 0) && ($board->get(array(($row + $direction), $col)) == null) 
        && ($board->Get(array(($row + $direction*2), $col)) == null)
    ) { // allowed to go two forward if both spaces open
      $possible_moves[] = array( ($row + $direction*2), $col);
    }
    foreach (array(-1, 1) as $diagonal) {
      $attackee = array(($row + $direction), $col + $diagonal);
      if (is_object($board->Get($attackee))
          && ($board->Get($attackee)->color != $this->color)
      ) { // allowed to attack diagonally
        $possible_moves[] = array(($row + $direction), $col + $diagonal);
      }
    }  
    echo("Available moves: " . $this->arrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

/** 
* Rook piece class which has one method returning available moves.
*
* @category Chess
* @package  Rook_Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class Rook extends Piece
{
  /**
  * desc
  *
  * @param object $board which contains all the positions of all pieces.
  *
  * @return array with all possible moves for $this piece.
  **/
  public function getPossibleMoves($board)
  {
    $deltas = $this->straight_deltas;
    $possible_moves = $this->getDeltaLines($deltas, $this->position, $board);
    echo("Available moves: " . $this->arrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

/** 
* Knight piece class which has one method returning available moves.
*
* @category Chess
* @package  Knight_Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class Knight extends Piece
{
  /**
  * desc
  *
  * @param object $board which contains all the positions of all pieces.
  *
  * @return array with all possible moves for $this piece.
  **/
  public function getPossibleMoves($board)
  {
    $row = $this->position[0];
    $col = $this->position[1];
    $color = $this->color;
    $all_moves = array(
      array( ($row + 2), ($col + 1) ), array( ($row + 2), ($col - 1) ),
      array( ($row - 2), ($col + 1) ), array( ($row - 2), ($col - 1) ),
      array( ($row + 1), ($col + 2) ), array( ($row + 1), ($col - 2) ),
      array( ($row - 1), ($col + 2) ), array( ($row - 1), ($col - 2) )
    );
    $onboard_moves = $this->filterOnBoardPossibles($all_moves, $board);
    $possible_moves = $this->filterNoFriendlyFire($onboard_moves, $board, $color);
    echo("Available moves: " . $this->arrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

/** 
* Bishop piece class which has one method returning available moves.
*
* @category Chess
* @package  Bishop_Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class Bishop extends Piece
{
  /**
  * desc
  *
  * @param object $board which contains all the positions of all pieces.
  *
  * @return array with all possible moves for $this piece.
  **/
  public function getPossibleMoves($board)
  {
    $deltas = $this->diagonal_deltas;
    $possible_moves = $this->getDeltaLines($deltas, $this->position, $board);
    echo("Available moves: " . $this->arrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

/** 
* Queen piece class which has one method returning available moves.
*
* @category Chess
* @package  Queen_Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class Queen extends Piece
{
  /**
  * desc
  *
  * @param object $board which contains all the positions of all pieces.
  *
  * @return array with all possible moves for $this piece.
  **/
  public function getPossibleMoves($board)
  {
    $deltas = array_merge($this->diagonal_deltas, $this->straight_deltas);
    $possible_moves = $this->getDeltaLines($deltas, $this->position, $board);
    echo("Available moves: " . $this->arrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

/** 
* King piece class which has one method returning available moves.
*
* @category Chess
* @package  King_Piece
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class King extends Piece
{

  /**
  * desc
  *
  * @param object $board which contains all the positions of all pieces.
  *
  * @return array with all possible moves for $this piece.
  **/
  public function getPossibleMoves($board)
  {
    $row = $this->position[0];
    $col = $this->position[1];
    $color = $this->color;
    $all_moves = array( 
      array($row - 1, $col + 1), array($row, $col + 1), 
      array($row + 1, $col + 1), array($row - 1, $col - 1),
      array($row, $col - 1), array($row + 1, $col - 1), 
      array($row - 1, $col), array($row + 1, $col) 
    );
    $onboard_moves = $this->filterOnBoardPossibles($all_moves, $board);
    $possible_moves = $this->filterNoFriendlyFire($onboard_moves, $board, $color);
    echo("Available moves: " . $this->arrayToEnglish($possible_moves) . "\n");
    return $onboard_moves;
  }
}
?>