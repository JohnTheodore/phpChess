<?php
  require 'board.php';
  require 'piece.php';
  require 'human_player.php';
  require 'computer_player.php';

class Game
{

  var $turn;
  var $board;
  var $initial_positions;

  public function __construct()
  {
    $this->board = new Board;
    $this->set_initial_positions();
  }

  public function play()
  {
    $checkmate = FALSE;
    while (!$checkmate)
    {
      $this->board->cli_display();
      echo($this->get_valid_move() . "\n");
    }
  }

  public function get_valid_move()
  {
    fwrite(STDOUT, "Where would you like to move? (eg, a4 to a5)\n");
    $varin = trim(fgets(STDIN)); // this should be a while loop
    return $varin;               // keep asking until it is valid
  }

  public function set_initial_positions()
  {
    $this->initial_positions = array(
    "King"    => array("white" => array(array(7, 4)), "black" => array(array(0, 4)) ),
    "Queen"   => array("white" => array(array(7, 3)), "black" => array(array(0, 3)) ),
    "Bishop"  => array("white" => array(array(7, 2), array(7, 5)), "black" => array(array(0, 2), array(0, 5)) ),
    "Knight"  => array("white" => array(array(7, 1), array(7, 6)), "black" => array(array(0, 1), array(0, 6)) ),
    "Rook"    => array("white" => array(array(7, 0), array(7, 7)), "black" => array(array(0, 0), array(0, 7)) ),
    "Pawn"    => array("white" => array(array(6, 0), array(6, 1), array(6, 2), array(6, 3), 
                         array(6, 4), array(6, 5), array(6, 6), array(6, 7) ),
                 "black" => array(array(1, 0), array(1, 1), array(1, 2), array(1, 3), 
                         array(1, 4), array(1, 5), array(1, 6), array(1, 7) ))
    );
  }

  public function return_chess_set()
  {
    $chess_set = array();
    foreach($this->initial_positions as $chessman => $colors)
    {
      foreach($colors as $color => $positions)
      {
        foreach($positions as $position)
        {
          array_push($chess_set, new $chessman($position, $color));
          //echo "Color: {$color} \n";
          //echo "Chessman: {$chessman} \n";
          //echo "positions: ";
          //var_dump($position);
          //echo "\n";
        }
      }
    }
    return $chess_set;
  }

  public function setup_game()
  {
    $this->board = new Board;
    $this->board->populate($this->return_chess_set());
  }

}

function load()
{
  $game = new Game;
  $game->setup_game();
  $game->play();
}

load();

//$game = new Game;
//$game->setup_game();
//echo($game->return_chess_set() . "\n");
  
?>