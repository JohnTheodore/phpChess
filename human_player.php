<?php

/**
* HumanPlayer is stored as $white or $black instance variable inside Chess
*
* PHP version 5
*
* @category Chess
* @package  HumanPlayer
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/

/** 
* This class basically takes user input and converts it to array form eg (0, 0)
*
* @category Chess
* @package  HumanPlayer
* @author   John Theodore <JohnTheodore@github.com>
* @license  MIT License
* @link     www.github.com/JOhnTheodore/phpChess
**/
class HumanPlayer
{

  public $color;
  public $name;

  /** 
  * set the $color and $name instance variables 
  *
  * @param string $color is "White" or "Black"
  * @param string $name  is "Alice" or "Bob"
  *
  * @return void
  **/
  public function __construct($color, $name)
  {
    $this->color = $color;
    $this->name = $name;
  }

  /** 
  * Converts user input a8, a7, to ( (0, 0), (0, 1) )  
  *
  * @param string $string the user input
  *
  * @return nested array in the form of ( (0, 0), (0, 1) ) 
  **/
  public function engToArray($string)
  {
    $string_split = explode(", ", $string);
    $src = array( (8 - intval(substr($string_split[0], 1, 1))),
            (ord(substr($string_split[0], 0, 1)) - 97) );
    $dest =   array( (8 - intval(substr($string_split[1], 1, 1))),
            (ord(substr($string_split[1], 0, 1)) - 97) );
    return array($src, $dest);
  }

  /** 
  * gets user input from STDIN and trims it  
  *
  * @return gives a nested array to the chess class.
  **/
  public function getMove()
  {
    fwrite(STDOUT, "Where would you like to move? (eg, 'a2, a4')\n");
    $varin = trim(fgets(STDIN));        // this should be a while loop
    return $this->engToArray($varin); // keep asking until it is valid
  }

}

?>