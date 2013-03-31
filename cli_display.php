<?php

class CliDisplay {

  public $cli_bw_visuals;

  public function __construct()
  {
    $this->SetVisuals();
  }

  public function promptMove($player)
  {
    echo("{$player->name}'s move ({$player->color}): ");
  }

  public function announceCapture($piece)
  {
    $capturer = ($piece->color == "White") ? $this->black : $this->white;
    $capturee = $this->otherPlayer($capturer);
    $piece_class = get_class($piece);
    echo("\n\n{$capturer->name} captured {$capturee->name}'s 
              {$capturee->color} {$piece_class}!!! \n\n");
  }

    public function UniChr($unicode) 
  {
    return mb_convert_encoding('&#' . intval($unicode) . 
      ';', 'UTF-8', 'HTML-ENTITIES');
  }

  public function SetVisuals()
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

  public function displayBoard($board)
  {
    echo "  a b c d e f g h\n";
    echo "  ________________ \n";
    foreach($board as $row_key => $row) {
      $row_num = (8 - $row_key);
      echo "{$row_num}|";
      foreach($row as $col_key => $col) {
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