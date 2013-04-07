<?php
/**
* chess.php
*
* PHP version 5
*
* @category Chess
* @package  Interface
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/

/** 
* This board class is instantiated inside Chess->board 
*
* @category Chess
* @package  Interface
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class CliDisplay
{

  public $cli_bw_visuals;

  /** Sets the unicode instance variables for the chess piece symbols **/
  public function __construct()
  {
    $this->setVisuals();
  }

  /** 
  * pass in a player object and the other player object will return
  * 
  * @param object $player was originally instantiated from *_player.php 
  *
  * @return object that is the opposing $otherplayer of $player 
  **/
  public function otherPlayer($player)
  {
    return ( ($player->color == "White") ? $this->black : $this->white );
  }

  /**
  * Simple string output to CLI
  *
  * @param object $player is a human or a computer player
  *
  * @return string as an echo
  **/
  public function promptMove($player)
  {
    echo("{$player->name}'s move ({$player->color}): ");
  }

  /**
  * Simple string output to CLI annoucing a piece capture.
  *
  * @param object $piece is the captured object.
  *
  * @return string as an echo
  **/
  public function announceCapture($piece)
  {
    $capturer = ($piece->color == "White") ? $this->black : $this->white;
    $capturee = $this->otherPlayer($capturer);
    $piece_class = get_class($piece);
    echo("\n\n{$capturer->name} captured {$capturee->name}'s 
              {$capturee->color} {$piece_class}!!! \n\n");
  }

  /**
  * Simple string output to CLI annoucing a check has occurred.
  *
  * @param object $player is a player
  *
  * @return
  **/
  public function announceCheck($player)
  {

  }

  /**
  * This will convert the character encoding from an integer to unicode
  *
  * @param integer $unicode is going to be a chess piece symbol
  *
  * @return a unicode chess symbol
  **/
  public function uniChr($unicode) 
  {
    return mb_convert_encoding(
        '&#' . intval($unicode) . ';', 'UTF-8', 'HTML-ENTITIES'
    );
  }

  /**
  * This sets the $cli_bw_visuals instance variable to the associative
  * array of unicode numbers for the chess pieces which is used by
  * $this->displayBoard();
  *
  * @return void
  **/
  public function setVisuals()
  {
    $this->cli_bw_visuals = array(
      'King'    => array("9812", "9818"),
      'Queen'   => array("9813", "9819"), // 0 == black, 1 == white
      'Knight'  => array("9816", "9822"),
      'Bishop'  => array("9815", "9821"),
      'Rook'    => array("9814", "9820"),
      'Pawn'    => array("9817", "9823")
    );
  }

  /**
  * This uses unicode characters to print nice chess piece characters
  * to the terminal screen in a clean format (clean for CLI)
  *
  * @param object $board instance variable from Board class
  *
  * @return string in the form of an echo to the terminal
  **/
  public function displayBoard($board)
  {
    echo "  a b c d e f g h\n";
    echo "  ________________ \n";
    foreach ($board as $row_key => $row) {
      $row_num = (8 - $row_key);
      echo "{$row_num}|";
      foreach ($row as $col_key => $col) {
        if ($col != null) {
          $piece_class = get_class($col);
          $piece_color = (($col->color == "White") ? 1 : 0);
          $piece_unicode = $this->cli_bw_visuals[$piece_class][$piece_color];
          echo($this->UniChr($piece_unicode) . " ");
        } elseif (($col == null) && ((($col_key + $row_key) % 2 == 0))) {
          echo($this->UniChr("9632") . " "); // print black square
        } else {
          echo($this->UniChr("9633") . " "); // print white square
        }
      }
      echo("|{$row_num}\n"); 
    }
    echo("  ________________ \n");
    echo("  a b c d e f g h\n\n");
  }

}

?>