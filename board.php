<?php
class Board {

  var $board;
  var $cli_bw_visuals;

  public function __construct()
  {
    $this->make_board();
    $this->set_visuals();
  }

  public function make_board()
  {
    $this->board = array();
    for ($i = 0; $i < 8; $i++) {
      array_push($this->board, array_fill(0, 8, NULL));
    }
  }

  public function unichr($u) 
  {
    return mb_convert_encoding('&#' . intval($u) . ';', 'UTF-8', 'HTML-ENTITIES');
  }

  public function set_visuals()
  {
    $this->cli_bw_visuals = array(
      'King'    => array("9812", "9818"),
      'Queen'   => array("9813", "9819"), // 0 == white, 1 == black
      'Knight'  => array("9814", "9820"),
      'Bishop'  => array("9815", "9821"),
      'Rook'    => array("9816", "9822"),
      'Pawn'    => array("9817", "9823")
    );
  }

  public function cli_display()
  {
    echo "  a b c d e f g h\n";
    echo "  ________________ \n";
    foreach($this->board as $row_key => $row)
    {
      $row_num = (8 - $row_key);
      echo "{$row_num}|";
      foreach($row as $col_key => $col)
      {
        if ($col != NULL)
        {
          $piece_class = get_class($col);
          $piece_color = (($col->color == "white") ? 0 : 1);
          $piece_unicode = $this->cli_bw_visuals[$piece_class][$piece_color];
          //var_dump($piece_unicode); die();
          echo($this->unichr($piece_unicode) . " ");
        }
        elseif (($col == NULL) && ((($col_key + $row_key) % 2 == 0)))
        {
          echo($this->unichr("9632") . " "); // print black square
        }
        else
        {
          echo($this->unichr("9633") . " "); // print white square
        }
      }
      echo("|\n");
    }
    echo("  ________________ \n");
    echo("  a b c d e f g h\n");
  }

  public function populate($pieces)
  {
    foreach($pieces as $piece)
    {
      $this->board[$piece->position[0]][$piece->position[1]] = $piece;
    }
  }

}

?>