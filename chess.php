<?php
  require 'board.php';
  require 'piece.php';
  require 'human_player.php';
  require 'computer_player.php';

  // for debugging
  require './kint/Kint.class.php';

class Game
{

  var $turn;
  var $board;
  var $initial_positions;
  var $captured_pieces;
  var $white;
  var $black;

  public function __construct()
  {
    $this->board = new Board;
    $this->set_initial_positions();
    $this->turn = 0;
    $this->captured_pieces = array();
  }

  public function play()
  {
    $checkmate = FALSE;
    while (!$checkmate)
    {
      // add a line to check for check/checkmate... this method is going to be difficult.
      // I need to write a method for each piece, so I can know that pieces available moves
      // also have a separate method for is_check
      $current_player = (($this->turn % 2 == 0) ? $this->white : $this->black);
      $this->board->cli_display();
      $current_move = $this->get_valid_move($current_player);
      $this->turn++;
      $this->make_move($current_move);
    }
  }

  public function get_valid_move($player)
  {
    while (TRUE)
    {
      $this->prompt_move($player);
      $src_and_dest = $player->get_move();
      if ($this->is_valid_move($src_and_dest, $player))
        { break; }
    }
    return $src_and_dest;
  }

  public function is_valid_move($src_and_dest, $player)
  { // I changed idioms to have it look cleaner since this method is so big.
    if (($this->is_on_board($src_and_dest[0])) && ($this->is_on_board($src_and_dest[1])))
    {
    $src = $src_and_dest[0];
    $dest = $src_and_dest[1];
    $src_piece = $this->board->get($src);
    $dest_piece = $this->board->get($dest);
    }
    else
     { return FALSE; } // need valid src and dest positions

    if ($src_piece->color != $player->color)
      { echo "wrong color\n"; return FALSE; } // Only move your own piece.
    elseif ($src == $dest)
      { echo "different pls\n"; return FALSE; } // src and dest should be different
    elseif ($src_piece == NULL)
      { echo "no piece\n"; return FALSE; } // There is no piece at that src
    elseif (($dest_piece != NULL) && ($src_piece->color == $dest_piece->color))
      { echo "no friendly fire\n"; return FALSE; } // no friendly fire
    else
      { return TRUE; } // allow the move
  }

  public function is_on_board($position)
  {
    return (preg_match('/^[0-7]+$/', $position[0]) &&
    preg_match('/^[0-7]+$/', $position[1]));
  }

  public function make_move($src_and_dest)
  {
    $src = $src_and_dest[0];
    $dest = $src_and_dest[1];
    $mobile_piece = $this->board->get($src);
    $mobile_piece->position = $dest;

    if ($this->board->get($dest) != NULL)
    {
      // do some crap in here when you kill/capture
    }
    $this->board->move($src, $dest);
  }

  public function set_initial_positions()
  {
    $this->initial_positions = array(
    "King"    => array("White" => array(array(7, 4)), "Black" => array(array(0, 4)) ),
    "Queen"   => array("White" => array(array(7, 3)), "Black" => array(array(0, 3)) ),
    "Bishop"  => array("White" => array(array(7, 2), array(7, 5)), "Black" => array(array(0, 2), array(0, 5)) ),
    "Knight"  => array("White" => array(array(7, 1), array(7, 6)), "Black" => array(array(0, 1), array(0, 6)) ),
    "Rook"    => array("White" => array(array(7, 0), array(7, 7)), "Black" => array(array(0, 0), array(0, 7)) ),
    "Pawn"    => array("White" => array(array(6, 0), array(6, 1), array(6, 2), array(6, 3), 
                         array(6, 4), array(6, 5), array(6, 6), array(6, 7) ),
                       "Black" => array(array(1, 0), array(1, 1), array(1, 2), array(1, 3), 
                         array(1, 4), array(1, 5), array(1, 6), array(1, 7) ))
    );
  }

  public function get_chess_set()
  {
    $chess_set = array();
    foreach($this->initial_positions as $chessman => $colors)
    {
      foreach($colors as $color => $positions)
      {
        foreach($positions as $position)
        {
          array_push($chess_set, new $chessman($position, $color));
        }
      }
    }
    return $chess_set;
  }

  public function is_check($player)
  {
    return false; // blah blah, put logic in here later.
  }

  public function setup_game($options = array())
  {
    $default_opts = array("load_game" => FALSE, "player_count" => 2);
    $options = array_merge($default_opts, $options);
    if ($options['load_game'])
    {
      // do some crap here where you unserialize the game object from a previous game.
    }
    // later on there should be an option where the computer can play the computer (hehe).
    else
    {
      $this->board = new Board;
      $this->board->populate($this->get_chess_set());
      $this->instantiate_players();
    }
  }

  public function instantiate_players($player_count = 2)
  {
    // Later on put some crap in here where they can choose to have...
    // computer v computer, human v human, human v computer.
    // use php switch() method
    $this->white = new HumanPlayer("White", "Supsupin");
    $this->black = new HumanPlayer("Black", "Theodore");
  }

  public function prompt_move($player)
  {
    echo("{$player->name}'s move ({$player->color}): ");
  }
}

function load()
{
  $game = new Game;
  $game->setup_game();
  $game->play();
}

load();
  
?>