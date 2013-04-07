php chess.php; // to run the game

* Yes I know php is for server side scripting and doesn't make a good language for a chess engine. This isn't a chess engine, I am just implementing the rules for chess. The point is to learn and get better at php/OOP concepts.

* Also FYI, the idioms I'm following are codified here: https://github.com/php-fig/fig-standards/tree/master/accepted

++TODO
* I'll need unit tests, php units tests look crappy compared to rspec.
* castling/en passant
* check
* checkmate
* filter user input more so they can throw in garbage
* unmake move on check?
* load game functionality
* allow pawns to get promoted when they reach the end.


I pass the tests for (Linter (php -l)), (php code sniffer), (PHP Mess Detector (phpmd)), (scheck, part of Facebook’s pfff toolchain))
