<?php

  /* This board class is instantiated inside Chess->board */

class Board
{

  public $board;

  public function __construct()
  {
    $this->MakeBoard();
  }

  public function MakeBoard()
  {
    $this->board = array();
    for ($i = 0; $i < 8; $i++) {
      array_push($this->board, array_fill(0, 8, null));
    }
  }

  public function Populate($pieces)
  {
    foreach($pieces as $piece) {
      $this->board[$piece->position[0]][$piece->position[1]] = $piece;
    }
  }

  public function IsOnBoard($position)
  {
    return (preg_match('/^[0-7]+$/', $position[0]) &&
    preg_match('/^[0-7]+$/', $position[1]));
  }

  public function Get($position)
  {
    if ($this->IsOnBoard($position)) { 
      return $this->board[$position[0]][$position[1]]; 
    }
  }

  public function Move($src, $dest)
  {
    $mobile_piece = ($this->board[$src[0]][$src[1]]);
    $this->board[$src[0]][$src[1]] = null;
    $this->board[$dest[0]][$dest[1]] = $mobile_piece;
  }
}

?>