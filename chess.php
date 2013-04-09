<?php

/**
* chess.php
*
* PHP version 5
*
* @category Chess
* @package  Chess
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/

require 'board.php';
require 'piece.php';
require 'human_player.php';
require 'computer_player.php';
require 'cli_display.php';

/** 
* This board class is instantiated inside Chess->board 
*
* @category Chess
* @package  Chess
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class Game
{

  public $turn = 0;
  public $board;
  public $captured_pieces;
  public $white;
  public $black;
  public $interface;
  public $moves_history;

  /** 
  * Sets $this->board to new Board, which is an empty 2D array
  * Sets $this->turn to 0, $this->turn is later used by play()
  * to determine whose turn it is, W or B
  * Sets $this->captured_pieces to an array which will eventually
  * contain all the captured pieces from both sides.
  * Sets $this->moves_history to an array that will eventually contain
  * the moves history in the form ( (src),(dest) )
  *
  * @return void
  **/
  public function __construct() 
  {
    /* refactor? */
    $this->board = new Board;
    $this->turn = 0;
    $this->captured_pieces = array();
    $this->moves_history = array();
  }

  /** 
  * This is the main play loop, it sets the current player based on $this->turn
  * then gets a move from the player. The move is validated by getValidMove().
  * Finally the move is executed with makeMove();
  * 
  * @return void
  **/
  public function play()
  {
    $current_player = $this->white;
    while (!$this->isCheckMate($current_player->color)) {
      $current_player = (($this->turn % 2 == 0) ? $this->white : $this->black);
      $this->interface->displayBoard($this->board->board);

      $this->getInfo($current_player->color);

      $current_move = $this->getValidMove($current_player);
      $this->turn++;
      $this->makeMove($current_move);
    }
    $names = $this->getColorsNames($this->board->findKing($current_player->color));
    $this->interface->announceCheckMate($names);
  }


  public function getInfo($color)
  {
    var_dump($color);
    var_dump(count($this->board->getAllPossibleMoves($color, $loop = true)));
  }

  /** 
  * continually ask for a move until a valid one is given 
  *
  * @param object $player is either a human or computer
  *
  * @return array as src, dest, eg ( (0, 0), (0, 2) )
  **/
  public function getValidMove(HumanPlayer $player)
  {
    while (true) {
      $this->interface->promptMove($player);
      $src_and_dest = $player->getMove();
      //$this->interface->displayBoard($this->board->board);
      if ($this->isValidMove($src_and_dest, $player)) { 
        break; 
      }
    }
    return $src_and_dest;
  }

  /** 
  * returns a boolean
  *
  * @param array  $src_and_dest contains the positions where a piece will move.
  * @param object $player       is the player attempting the move      
  *
  * @return boolean true move is allowed, false move is not allowed.
  **/
  public function isValidMove(array $src_and_dest, HumanPlayer $player)
  { 
    if (($this->board->isOnBoard($src_and_dest[0])) 
        && ($this->board->isOnBoard($src_and_dest[1]))
    ) {
      $src = $src_and_dest[0];
      $dest = $src_and_dest[1];
      $src_piece = $this->board->get($src);
      $dest_piece = $this->board->get($dest);
    } else {
      return false; // need valid src and dest positions
    }

    if (!is_object($src_piece)) {
      echo "no piece there\n"; return false;
    } elseif ($src_piece->color != $player->color) { 
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

  /**
  * returns boolean, true checkmate gameover, false.. keep going. If the
  * player has no available moves, he is checkmated.
  * 
  * @param string $color is "White" or "Black"
  *
  * @return boolean true is game over, false is keep going.
  **/
  public function isCheckMate($color)
  {
    return (count($this->board->getAllPossibleMoves($color, $loop = true)) == 0);
  }

  /** 
  * helper method to make isValidMove smaller, user input needs to match
  * what positions that piece object returns in getPossibleMoves 
  *
  * @param object $src_piece the chess piece object requesting to be moved
  * @param array  $dest      dest position desired by the player for the piece
  *
  * @return boolean, the piece is allowed to make that move (true) or false
  **/
  public function isPossibleMove(Piece $src_piece, array $dest)
  {
    $possible_moves = $src_piece->getPossibleMoves($this->board);
    $moves = $src_piece->arrayToEnglish($possible_moves);
    $color = $src_piece->color;
    $chessman = get_class($src_piece);
    echo("{$color} {$chessman} moves: {$moves}") . "\n";
    return array_search($dest, $possible_moves) !== false;
  }

  /** 
  * moves the piece object from one position in the board array to another 
  * position. If there is an enemy piece there it will add it to the 
  * captured_pieces array when allowed  
  *
  * @param array $src_and_dest is a nested array with ((src), (dest))
  *
  * @return void
  **/
  public function makeMove(array $src_and_dest)
  {
    $src = $src_and_dest[0];
    $dest = $src_and_dest[1];

    if ($this->board->get($dest) != null) {
      $captured = $this->board->get($dest);
      $this->captured_pieces[] = $captured;
      $this->interface->announceCapture($this->getColorsNames($captured));
    }
    // if pawn position is at the end, upgrade it.
    $this->board->move($src, $dest);
  }

  /**
  * This takes a piece object and returns the potential capturer and capturee
  * of that piece in the form of a hash table/associative array.
  *
  * @param object $piece is the piece being checked or captured
  *
  * @return void
  **/
  public function getColorsNames(Piece $piece)
  {
    $capturer = ($piece->color == "White") ? $this->black : $this->white;
    $capturee = ($piece->color == "White") ? $this->white : $this->black;
    return array(
          "piece" => get_class($piece),
          "capturer" => array( 
                          "name" => $capturer->name,
                          "color" => $capturer->color
                        ),
          "capturee" => array(
                          "name" => $capturee->name,
                          "color" => $capturee->color
            )
      );
  }

  /** 
  * This returns an array that contains all the initial positions for a chess 
  * set, in the form of key as class Piece name and value as an array with two
  * keys which are color parameters for instatiating the object. Each of the 
  * color keys has further arrays that are the position parameter for 
  * instatiating the piece object.
  *
  * @return array all default starting positions for a chess game
  **/
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

  /** 
  * This returns a plain array with all 32 chess piece objects. It will be fed
  * to the populate method in the board class. 
  *
  * @return array contains an array with all the 32 instantiated chess objects
  **/
  public function getChessSet()
  {
    $chess_set = array();
    foreach ($this->getInitialPositions() as $chessman => $colors) {
      foreach ($colors as $color => $positions) {
        foreach ($positions as $position) {
          array_push($chess_set, new $chessman($position, $color));
        }
      }
    }
    return $chess_set;
  }

  /** 
  * This will configure a new game with human v human, human v computer, 
  * computer v human. Saved games can be loaded from this method too 
  * 
  * @param hash_table $options contains different settings for how the game
  * can be loaded.
  *
  * @return void
  **/
  public function setupGame(array $options = array())
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
      $this->board->populateBoard($this->getChessSet());
      $this->setPlayers();
    }
  }

  /**
  * THis sets both players to HumanPlayers since ComputerPlayer is not
  * implemented.
  *
  * @return void
  **/
  public function setPlayers()
  {           // $player_count = 2 as params later on?
    // Later on put some crap in here where they can choose to have...
    // computer v computer, human v human, human v computer.
    // use php switch() method
    $this->white = new HumanPlayer("White", "Supsupin");
    $this->black = new HumanPlayer("Black", "Theodore");
  }

}

/** 
* bootstrapping the game 
*
* @return void
**/
function load()
{
  $game = new Game;
  $game->setupGame();
  $game->play();
}

load();
  
?>