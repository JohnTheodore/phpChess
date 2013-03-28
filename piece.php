<?php
class Piece
{
  var $position;
  var $color;
  var $moves;

  public function __construct($position, $color)
  {
    $this->position = $position;
    $this->color = $color;
  }

  public function filter_on_board_possibles($impossible_positions, $board)
  {
    $onboard_moves = $impossible_positions;
    foreach($onboard_moves as $key => $position)
    {
      if (!$board->is_on_board($position))
      {
        unset($onboard_moves[$key]);
      }
    }
    return $onboard_moves;
  }

  public function filter_no_friendly_fire($positions, $board, $player)
  {
    $no_friendly_fire = $positions;
    foreach($no_friendly_fire as $key => $position)
    {
      if ( (is_object($board->get($position))) && ($board->get($position)->color === $player->color) )
      {
        unset($no_friendly_fire[$key]);
      }
    }
    return $no_friendly_fire;
  }

  public function array_to_english($positions_array)
  {
    $english_positions = "";
    foreach($positions_array as $position)
    {
      $row = (8 - $position[0]);
      $col = ( chr($position[1] + 97) );
      $english_positions .= "{$col}{$row}, ";
    }
    return $english_positions;
  }

  public function crawl_delta($src, $delta, $board)
  {
    // run some recursive function to crawl the delta
  }

  public function get_diagonal_lines($src, $board)
  {
    $deltas = array( array(1, 1), array(1, -1), array(-1, 1), array(-1, -1) ); // the 4 directions diagonals can go
    $possible_moves = array();
    foreach($delta as $delta)
    {
      $possible_moves[] = $this->crawl_delta($src, $delta, $board);
    }
    return $possible_moves;
  }

  public function get_straight_lines($src, $board)
  {
    $deltas = array( array(1, 1), array(1, -1), array(-1, 1), array(-1, -1) ); // the 4 directions diagonals can go
    $possible_moves = array();
    foreach($delta as $delta)
    {
      $possible_moves[] = $this->crawl_delta($src, $delta, $board);
    }
    return $possible_moves;
  }
}

class Pawn extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $x = $this->position[0];
    $y = $this->position[1];
    $moves = $this->moves;
    if ($this->color == "White")bin
    {
      $possible_moves = array();
      if ($board->get(array(($x - 1), $y)) == NULL)
        { $possible_moves[] = array( ($x - 1), $y); } // allowed to go one forward if the space is empty.
      if (($moves == 0) && ($board->get(array(($x - 1), $y)) == NULL) && ($board->get(array(($x - 2), $y)) == NULL))
        { $possible_moves[] = array( ($x - 2), $y); } // allowed to go two forward if both spaces open
      if ( is_object($board->get(array(($x - 1), $y - 1))) && 
      ($board->get(($x - 1), $y - 1)->color != $this->color))
        { $possible_moves[] = array(($x - 1), $y - 1); }  // allowed to attack diagonally
      if ( is_object($board->get(array(($x - 1), $y + 1))) && 
      ($board->get(array(($x - 1), $y + 1))->color != $this->color) )
        { $possible_moves[] = array(($x - 1), $y + 1); }  //  allowed to attack diagonally
      echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
      return $possible_moves;
    }
    elseif ($this->color == "Black")
    {
      $possible_moves = array();
      if ($board->get(array(($x + 1), $y)) == NULL)
        { $possible_moves[] = array( ($x + 1), $y); } // allowed to go one forward if the space is empty.
      if (($moves == 0) && ($board->get(array(($x + 1), $y)) == NULL) && ($board->get(array(($x + 2), $y)) == NULL))
        { $possible_moves[] = array( ($x + 2), $y); } // allowed to go two forward if both spaces open
      if ( is_object($board->get(array(($x + 1), $y - 1))) && 
      ($board->get(($x + 1), $y - 1)->color != $this->color))
        { $possible_moves[] = array(($x + 1), $y - 1); }  // allowed to attack diagonally
      if ( is_object($board->get(array(($x + 1), $y + 1))) && 
      ($board->get(array(($x + 1), $y + 1))->color != $this->color) )
        { $possible_moves[] = array(($x + 1), $y + 1); }  //  allowed to attack diagonally
      echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
      return $possible_moves;
    }
  }
}

class Rook extends Piece
{

}

class Knight extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $x = $this->position[0];
    $y = $this->position[1];
    $impossible_moves = array(
      array( ($x + 2), ($y + 1) ), array( ($x + 2), ($y - 1) ),
      array( ($x - 2), ($y + 1) ), array( ($x - 2), ($y - 1) ),
      array( ($x + 1), ($y + 2) ), array( ($x + 1), ($y - 2) ),
      array( ($x - 1), ($y + 2) ), array( ($x - 1), ($y - 2) )
    );
    $onboard_moves = $this->filter_on_board_possibles($impossible_moves, $board);
    $possible_moves = $this->filter_no_friendly_fire($onboard_moves, $board, $player);
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Bishop extends Piece
{

}

class Queen extends Piece
{

}

class King extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $x = $this->position[0];
    $y = $this->position[1];
    $impossible_moves = array( 
      array($x - 1, $y + 1), array($x, $y + 1), array($x + 1, $y + 1), array($x - 1, $y - 1),
      array($x, $y - 1), array($x + 1, $y - 1), array($x - 1, $y), array($x + 1, $y) 
    );
    $onboard_moves = parent::filter_on_board_possibles($impossible_moves, $board);
    $possible_moves = parent::filter_no_friendly_fire($onboard_moves, $board, $player);
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $onboard_moves;
  }
}
?>