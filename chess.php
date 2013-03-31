<?php

  /** This is the main class for Chess **/
  require 'board.php';
  require 'piece.php';
  require 'human_player.php';
  require 'computer_player.php';
  require 'cli_display.php';

class Game
{

  public $turn;
  public $board;
  public $captured_pieces;
  public $white;
  public $black;
  public $interface;

  public function __construct() 
  {
    /* refactor? */
    $this->board = new Board;
    $this->setInitialPositions();
    $this->turn = 0;
    $this->captured_pieces = array();
  }

  /** main play loop **/
  public function play()
  {
    $checkmate = false;
    while (!$checkmate) {
      // add a line to check for check/checkmate... this method is going to 
      // be difficult. I need to write a method for each piece, so I can know 
      // that pieces available moves also have a separate method for isCheck
      $current_player = (($this->turn % 2 == 0) ? $this->white : $this->black);
      $this->interface->displayBoard($this->board->board);
      $current_move = $this->getValidMove($current_player);
      $this->turn++;
      $this->makeMove($current_move);
    }
  }

  /** continually ask for a move until a valid one is given **/
  public function getValidMove($player)
  {
    while (true) {
      $this->interface->promptMove($player);
      $src_and_dest = $player->GetMove();
      if ($this->isValidMove($src_and_dest, $player)) { 
        break; 
      }
    }
    return $src_and_dest;
  }

  /** returns a boolean,  **/
  public function isValidMove($src_and_dest, $player)
  { 
    if ( ($this->board->IsOnBoard($src_and_dest[0])) && 
         ($this->board->IsOnBoard($src_and_dest[1]))
       )
    {
      $src = $src_and_dest[0];
      $dest = $src_and_dest[1];
      $src_piece = $this->board->Get($src);
      $dest_piece = $this->board->Get($dest);
    } else {
      return false; // need valid src and dest positions
    }

    if ($src_piece->color != $player->color) { 
      echo "wrong color\n"; return false; 
    } elseif ($src == $dest) {
      // Only move your own piece. 
      echo "different pls\n"; return false; 
    } elseif ($src_piece == null) { 
      // src and dest should be different
      echo "no piece\n"; return false; 
    } elseif (($dest_piece != null) && ($src_piece->color == $dest_piece->color)) {
      // There is no piece at that src 
      echo "no friendly fire\n"; return false; 
    } elseif ( $this->isPossibleMove($src_piece, $dest) ) { 
      // no friendly fire
      return true; 
    } else { // allow the move only if the piece getPossibleMoves
      // contains the dest position
      return false;
    }
  }

  /** helper method to make isValidMove smaller, user input needs to match
      what positions that piece object returns in getPossibleMoves **/
  public function isPossibleMove($src_piece, $dest)
  {
    $possible_moves = $src_piece->getPossibleMoves($this->board);
    return array_search($dest, $possible_moves) !== false;
  }

  public function makeMove($src_and_dest)
  {
    $src = $src_and_dest[0];
    $dest = $src_and_dest[1];
    $mobile_piece = $this->board->Get($src);
    $mobile_piece->position = $dest;
    $mobile_piece->moves++;

    if ($this->board->Get($dest) != null) {
      $this->captured_pieces[] = $this->board->Get($dest);
      $this->interface->announceCapture($this->board->Get($dest));
    }
    $this->board->move($src, $dest);
  }

  public function unmakeMove()
  {
    // If the move causes the acting player to be in check, it should 
    // unmake the move before it displays the board.
  }

  /** This returns an array that contains all the initial positions for a
      chess set, in the form of key as class Piece name and value as an
      array with two keys which are color parameters for instatiating the 
      object. Each of the color keys has further arrays that are the
      position parameter for instatiating the piece object. **/
  public function getInitialPositions()
  {
    return array(
    "King"    => array("White" => array(array(7, 4)), 
                       "Black" => array(array(0, 4)) ),
    "Queen"   => array("White" => array(array(7, 3)), 
                       "Black" => array(array(0, 3)) ),
    "Bishop"  => array("White" => array(array(7, 2), array(7, 5)), 
                       "Black" => array(array(0, 2), array(0, 5)) ),
    "Knight"  => array("White" => array(array(7, 1), array(7, 6)), 
                       "Black" => array(array(0, 1), array(0, 6)) ),
    "Rook"    => array("White" => array(array(7, 0), array(7, 7)), 
                       "Black" => array(array(0, 0), array(0, 7)) ),
    "Pawn"    => array("White" => array(
                                    array(6, 0), array(6, 1), array(6, 2), 
                                    array(6, 3), array(6, 4), array(6, 5), 
                                    array(6, 6), array(6, 7) 
                                  ),
                       "Black" => array(
                                    array(1, 0), array(1, 1), array(1, 2), 
                                    array(1, 3), array(1, 4), array(1, 5), 
                                    array(1, 6), array(1, 7)
                                  )
                 )
    );
  }

  /** This returns a plain array with all 32 chess piece objects. It will be
      fed to the populate method in the board class. **/
  public function getChessSet()
  {
    $chess_set = array();
    foreach($this->getInitialPositions() as $chessman => $colors) {
      foreach($colors as $color => $positions) {
        foreach($positions as $position) {
          array_push($chess_set, new $chessman($position, $color));
        }
      }
    }
    return $chess_set;
  }

  public function isCheck()
  { // $player param later on
    return false; // blah blah, put logic in here later.
  }

  /** This will configure a new game with human v human, human v computer, 
      computer v human. Saved games can be loaded from this method too **/
  public function setupGame($options = array())
  {
    $default_opts = array("load_game" => false, "player_count" => 2, 
      "interface" => "CLI");
    $options = array_merge($default_opts, $options);
    if ($options['load_game']) {
      // do some crap here where you unserialize 
      // the game object from a previous game.
    } else {
      $this->interface = new CliDisplay;
      $this->board = new Board;
      $this->board->Populate($this->getChessSet());
      $this->setPlayers();
    }
  }

  /** pass in a player object and the other player object will return **/
  public function otherPlayer($player)
  {
    return ( ($player->color == "White") ? $this->black : $this->white );
  }

  public function setPlayers()
  {           // $player_count = 2 as params later on?
    // Later on put some crap in here where they can choose to have...
    // computer v computer, human v human, human v computer.
    // use php switch() method
    $this->white = new HumanPlayer("White", "Supsupin");
    $this->black = new HumanPlayer("Black", "Theodore");
  }

}

/** bootstrapping the game **/
function load()
{
  $game = new Game;
  $game->setupGame();
  $game->play();
}

load();
  
?>