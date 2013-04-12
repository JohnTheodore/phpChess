php chess.php; // to run the game

* Yes I know php is for server side scripting and doesn't make a good language for a chess engine. This isn't a chess engine, I am just implementing the rules for chess. The point is to learn and get better at php/OOP concepts.

* Also FYI, the idioms I'm following are codified here: https://github.com/php-fig/fig-standards/tree/master/accepted

++TODO

* Put the HumanPlayer prompt/input stuff into interface when logical.

* Use type hinting on $color, make sure everything has type hinting. Find out the problem with type hinting/strings.

* Find out if type hinting handles nested arrays

* Change the $loop true/false to separate wrapper methods as a minimum. WithKingCheck or WithoutKingCheck.

* Setup an errors array/instance variable in the HumanPlayer class, throw errors from isValidMove to that instance variable, then have an interface method run only if the errors array is > 0

* on start, ask for the name as set HumanPlayer->name to that.

* Inspect crawlDelta for code smell, refactor

* php units tests look crappy compared to rspec.

* castling

* en passant, this is tricky since the current logic assumes capturing a piece means moving to where that enemy piece is. For en passant, you move behind the enemy piece, then capture it. Perhaps have a default value for move/dest.. and only change that default value for an en passant move? This would require significant changes.

* filter user input more so they can throw in garbage

* load game functionality

* allow pawns to get promoted when they reach the end.


I pass the tests for (Linter (php -l)), (php code sniffer), (PHP Mess Detector (phpmd)), (scheck, part of Facebook’s pfff toolchain))
