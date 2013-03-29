<?php
class Piece
{
  public $position;
  public $color;
  public $moves;
  public $straight_deltas = array( 
                                    array(-1, 0), array(1, 0), 
                                    array(0, -1), array(0, 1) 
                            );
  public $diagonal_deltas = array( 
                                    array(1, 1), array(1, -1), 
                                    array(-1, 1), array(-1, -1)
                            );

  public function __construct($position, $color)
  {
    $this->position = $position;
    $this->color = $color;
  }

  public function filter_on_board_possibles($impossible_positions, $board)
  {
    $onboard_moves = $impossible_positions;
    foreach($onboard_moves as $key => $position)
    {
      if (!$board->is_on_board($position))
      {
        unset($onboard_moves[$key]);
      }
    }
    return $onboard_moves;
  }

  public function filter_no_friendly_fire($positions, $board, $player)
  {
    $no_friendly_fire = $positions;
    foreach($no_friendly_fire as $key => $position)
    {
      if ( (is_object($board->get($position))) && 
           ($board->get($position)->color === $player->color)
         )
      {
        unset($no_friendly_fire[$key]);
      }
    }
    return $no_friendly_fire;
  }

  public function array_to_english($positions_array)
  {
    $english_positions = "";
    foreach($positions_array as $position)
    {
      $row = (8 - $position[0]);
      $col = ( chr($position[1] + 97) );
      $english_positions .= "{$col}{$row}, ";
    }
    return $english_positions;
  }

  public function crawl_delta($src, $delta, $board)
  {
    $possible_moves = array();
    $current_square = array( ($delta[0] + $src[0]), ($delta[1] + $src[1]) );
    $src_piece = $board->get($src);
    while ( ($board->is_on_board($current_square)) && 
            ( ($board->get($current_square) == NULL) || 
              (is_object($board->get($current_square))
            ) && 
            ($src_piece->color != $board->get($current_square)->color) ) )
    {
      $possible_moves[] = $current_square;
      if ( (is_object($board->get($current_square))) && 
           ($src_piece->color != $board->get($current_square)->color)
         )
        { break; }
      $current_square = array( ($current_square[0] + $delta[0]), 
                               ($current_square[1] + $delta[1]) 
                        );
    }
    return $possible_moves;
  }

  public function get_delta_lines($deltas, $src, $board)
  {
    $possible_moves = array();
    foreach($deltas as $delta)
    {
      foreach($this->crawl_delta($src, $delta, $board) as $possible_move )
      {
        $possible_moves[] = $possible_move;
      }
    }
    return $possible_moves;
  }
}

class Pawn extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $row = $this->position[0];
    $col = $this->position[1];
    $moves = $this->moves;
    $direction = (($this->color == "White") ? -1 : 1);
    $possible_moves = array();
    if ($board->get(array(($row + $direction), $col)) == NULL)
      // allowed to go one forward if the space is empty.
      $possible_moves[] = array( ($row + $direction), $col);
    if (($moves == 0) && ($board->get(array(($row + $direction), $col)) == NULL) && 
        ($board->get(array(($row + $direction*2), $col)) == NULL))
      // allowed to go two forward if both spaces open
      $possible_moves[] = array( ($row + $direction*2), $col);
    if ( is_object($board->get(array(($row + $direction), $col - 1))) && 
    ($board->get(($row + $direction), $col - 1)->color != $this->color))
      // allowed to attack diagonally
      $possible_moves[] = array(($row + $direction), $col - 1); 
    if ( is_object($board->get(array(($row + $direction), $col + 1))) && 
    ($board->get(array(($direction + 1), $col + 1))->color != $this->color) )
      //  allowed to attack diagonally
      $possible_moves[] = array(($row + $direction), $col + 1); 
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Rook extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $deltas = $this->straight_deltas;
    $possible_moves = $this->get_delta_lines($deltas, $this->position, $board);
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Knight extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $row = $this->position[0];
    $col = $this->position[1];
    $impossible_moves = array(
      array( ($row + 2), ($col + 1) ), array( ($row + 2), ($col - 1) ),
      array( ($row - 2), ($col + 1) ), array( ($row - 2), ($col - 1) ),
      array( ($row + 1), ($col + 2) ), array( ($row + 1), ($col - 2) ),
      array( ($row - 1), ($col + 2) ), array( ($row - 1), ($col - 2) )
    );
    $onboard_moves = $this->filter_on_board_possibles($impossible_moves, $board);
    $possible_moves = $this->filter_no_friendly_fire($onboard_moves, $board, $player);
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Bishop extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $possible_moves = $this->get_delta_lines($this->diagonal_deltas, $this->position, $board);
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $possible_moves;
  }
}

class Queen extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $deltas = array_merge($this->diagonal_deltas, $this->straight_deltas);
    $possible_moves = $this->get_delta_lines($deltas, $this->position, $board);
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $possible_moves;
  }
}

class King extends Piece
{
  public function get_possible_moves($board, $player)
  {
    $row = $this->position[0];
    $col = $this->position[1];
    $impossible_moves = array( 
      array($row - 1, $col + 1), array($row, $col + 1), array($row + 1, $col + 1), array($row - 1, $col - 1),
      array($row, $col - 1), array($row + 1, $col - 1), array($row - 1, $col), array($row + 1, $col) 
    );
    $onboard_moves = parent::filter_on_board_possibles($impossible_moves, $board);
    $possible_moves = parent::filter_no_friendly_fire($onboard_moves, $board, $player);
    echo("Available moves for piece: " . $this->array_to_english($possible_moves) . "\n");
    return $onboard_moves;
  }
}
?>