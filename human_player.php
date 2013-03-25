<?php

// this class should be really simple, just take input and validate it.
class HumanPlayer
{

  var $color;
  var $name; // this can be their username on the website later.

  public function __construct($color, $name)
  {
    $this->color = $color;
    $this->name = $name;
  }

  public function get_move()
  {
    fwrite(STDOUT, "Where would you like to move? (eg, a4 to a5)\n");
    $varin = trim(fgets(STDIN)); // this should be a while loop
    return $varin;               // keep asking until it is valid
  }

}

?>