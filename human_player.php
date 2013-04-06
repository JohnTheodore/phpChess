<?php

/* this class should be really simple, just take input and validate it. */
class HumanPlayer
{

  public $color;
  public $name; // this can be their username on the website later.

  public function __construct($color, $name)
  {
    $this->color = $color;
    $this->name = $name;
  }

  /** This takes  **/
  public function engToArray($string)
  {
    $string_split = explode(", ", $string);
    $src = array( (8 - intval(substr($string_split[0], 1, 1))),
            (ord(substr($string_split[0], 0, 1)) - 97) );
    $dest =   array( (8 - intval(substr($string_split[1], 1, 1))),
            (ord(substr($string_split[1], 0, 1)) - 97) );
    return array($src, $dest);
  }

  public function getMove()
  {
    fwrite(STDOUT, "Where would you like to move? (eg, 'a2, a4')\n");
    $varin = trim(fgets(STDIN));        // this should be a while loop
    return $this->engToArray($varin); // keep asking until it is valid
  }

}

?>