<?php
class Piece
{
  var $position;
  var $color;

  public function __construct($position, $color)
  {
    $this->position = $position;
    $this->color = $color;
  }

}

class Pawn extends Piece
{
  //var $moves;
  // this breaks things, i'm not sure why. I think i need parent::blahblah
  //public function __construct()
  //{
  //  $this->moves = 0;
  //}
}

class Rook extends Piece
{

}

class Knight extends Piece
{
  public function get_possible_moves()
  {
    $x = $this->position[0];
    $y = $this->position[1];
    return array(
      array( ($x + 2), ($y + 1) ), array( ($x + 2), ($y - 1) ),
      array( ($x - 2), ($y + 1) ), array( ($x - 2), ($y - 1) ),
      array( ($x + 1), ($y + 2) ), array( ($x + 1), ($y - 2) ),
      array( ($x - 1), ($y + 2) ), array( ($x - 1), ($y - 2) )
    ); // some of these will be off_board, though user input was already
       // filtered by is_valid_move, so it is fine.
       // It is unnecessary processor work to eliminate offboard moves
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