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

}

class Rook extends Piece
{

}

class Knight extends Piece
{

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