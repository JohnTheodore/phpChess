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
      if ($board->get($position)->color === $player->color)
      {
        unset($no_friendly_fire[$key]);
      }
    }
    return $no_friendly_fire;
  }

}

class Pawn extends Piece
{

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
    $onboard_moves = parent::filter_on_board_possibles($impossible_moves, $board);
    $no_friendly_fire = parent::filter_no_friendly_fire($onboard_moves, $board, $player);
    var_dump($no_friendly_fire);
    return $onboard_moves;
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

}
?>