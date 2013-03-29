<?php

// this class should be really simple, just take input and validate it.
class HumanPlayer
{

  public $color;
  public $name; // this can be their username on the website later.

  public function __construct($color, $name)
  {
    $this->color = $color;
    $this->name = $name;
  }

  public function convert_move($string)
  {
    $string_split = explode(", ", $string);
    $from = array( (8 - intval(substr($string_split[0], 1, 1))),
            (ord(substr($string_split[0], 0, 1)) - 97) );
    $to =   array( (8 - intval(substr($string_split[1], 1, 1))),
            (ord(substr($string_split[1], 0, 1)) - 97) );
    return array($from, $to);
  }

  public function get_move()
  {
    fwrite(STDOUT, "Where would you like to move? (eg, 'a2, a4')\n");
    $varin = trim(fgets(STDIN));        // this should be a while loop
    return $this->convert_move($varin); // keep asking until it is valid
  }

}

?>