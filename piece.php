<?php

/* All of these pieces are stored in the board object */

class Piece
{
  public $position;
  public $color;
  public $moves;
  public $straight_deltas = array( array(-1, 0), array(1, 0), array(0, -1), 
    array(0, 1) );
  public $diagonal_deltas = array( array(1, 1), array(1, -1),  array(-1, 1), 
    array(-1, -1) );

  public function __construct($position, $color)
  {
    $this->position = $position;
    $this->color = $color;
  }

  public function FilterOnBoardPossibles($impossible_positions, $board)
  {
    $onboard_moves = $impossible_positions;
    foreach($onboard_moves as $key => $position) {
      if (!$board->IsOnBoard($position)) {
        unset($onboard_moves[$key]);
      }
    }
    return $onboard_moves;
  }

  public function FilterNoFriendlyFire($positions, $board, $color)
  {
    $no_friendly_fire = $positions;
    foreach($no_friendly_fire as $key => $position) {
      if ( (is_object($board->Get($position))) && 
           ($board->Get($position)->color === $color)
         )
      {
        unset($no_friendly_fire[$key]);
      }
    }
    return $no_friendly_fire;
  }

  public function ArrayToEnglish($positions_array)
  {
    $english_positions = "";
    foreach($positions_array as $position) {
      $row = (8 - $position[0]);
      $col = ( chr($position[1] + 97) );
      $english_positions .= "{$col}{$row}, ";
    }
    return $english_positions;
  }

  public function CrawlDelta($src, $delta, $board)
  {  // there is some code smell here
    $possible_moves = array();
    $current_square = array( ($delta[0] + $src[0]), ($delta[1] + $src[1]) );
    $src_piece = $board->Get($src);
    while ( ($board->IsOnBoard($current_square)) && 
            ( ($board->Get($current_square) == null) || 
              (is_object($board->Get($current_square))
            ) && 
            ($src_piece->color != $board->Get($current_square)->color) ) )
    {
      $possible_moves[] = $current_square;
      if ( (is_object($board->Get($current_square))) && 
           ($src_piece->color != $board->Get($current_square)->color)
         ) { 
        break; 
      }
      $current_square = array( ($current_square[0] + $delta[0]), 
                               ($current_square[1] + $delta[1]) 
                        );
    }
    return $possible_moves;
  }

  public function GetDeltaLines($deltas, $src, $board)
  {
    $possible_moves = array();
    foreach($deltas as $delta)
    {
      foreach($this->CrawlDelta($src, $delta, $board) as $possible_move )
      {
        $possible_moves[] = $possible_move;
      }
    }
    return $possible_moves;
  }
}

class Pawn extends Piece
{
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
    if (($moves == 0) && ($board->Get(array(($row + $direction), $col)) == null) && 
        ($board->Get(array(($row + $direction*2), $col)) == null)) {
      // allowed to go two forward if both spaces open
      $possible_moves[] = array( ($row + $direction*2), $col);
    }
    foreach(array(-1, 1) as $diagonal)
    {
      if ( is_object($board->Get(array(($row + $direction), $col + $diagonal))) && 
        ($board->Get(array(($row + $direction), $col + $diagonal))->color != $this->color)) {
        // allowed to attack diagonally
        $possible_moves[] = array(($row + $direction), $col + $diagonal);
      }
    }  
    echo("Available moves: " . $this->ArrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Rook extends Piece
{
  public function getPossibleMoves($board)
  {
    $deltas = $this->straight_deltas;
    $possible_moves = $this->GetDeltaLines($deltas, $this->position, $board);
    echo("Available moves: " . $this->ArrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Knight extends Piece
{
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
    $onboard_moves = $this->FilterOnBoardPossibles($all_moves, $board);
    $possible_moves = $this->FilterNoFriendlyFire($onboard_moves, $board, $color);
    echo("Available moves: " . $this->ArrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Bishop extends Piece
{
  public function getPossibleMoves($board)
  {
    $possible_moves = $this->GetDeltaLines($this->diagonal_deltas, $this->position, $board);
    echo("Available moves: " . $this->ArrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Queen extends Piece
{
  public function getPossibleMoves($board)
  {
    $deltas = array_merge($this->diagonal_deltas, $this->straight_deltas);
    $possible_moves = $this->GetDeltaLines($deltas, $this->position, $board);
    echo("Available moves for piece: " . $this->ArrayToEnglish($possible_moves) . "\n");
    return $possible_moves;
  }
}

class King extends Piece
{
  public function getPossibleMoves($board)
  {
    $row = $this->position[0];
    $col = $this->position[1];
    $color = $this->color;
    $all_moves = array( 
      array($row - 1, $col + 1), array($row, $col + 1), array($row + 1, $col + 1), array($row - 1, $col - 1),
      array($row, $col - 1), array($row + 1, $col - 1), array($row - 1, $col), array($row + 1, $col) 
    );
    $onboard_moves = $this->FilterOnBoardPossibles($all_moves, $board);
    $possible_moves = $this->FilterNoFriendlyFire($onboard_moves, $board, $color);
    echo("Available moves for piece: " . $this->ArrayToEnglish($possible_moves) . "\n");
    return $onboard_moves;
  }
}
?>