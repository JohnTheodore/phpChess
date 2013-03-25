<?php
require 'kint/Kintclass.php';
$db = new SQLite3('master.db');

var_dump($db->querySingle('SELECT * from users'));
print_r($db->querySingle('SELECT * from users', true));
?>

<?php
$dbname = 'db/master.db';
if(!class_exists('SQLite3'))
  die("SQLite 3 NOT supported.");
 
$base = new SQLite3($dbname, 0666);
$users = $base->querySingle("select * from users;", true);
var_dump($users);
?>
<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<title>FacePlace</title>
	<link rel="stylesheet" href="../static/css/style.css" />
	<link rel="stylesheet" href="../static/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" href="../static/bootstrap/js/bootstrap.js" />
</head>
<body>
	<div id="wrapper">
		<div id="search_bar" class="navbar">
			<div class="navbar-inner">
				<a class="brand" href="#">Friends</a>
        <a class="brand" href="register.php">Register</a>
			</div>
		</div>
		<div id="products" class="hero-unit">
			<ul class="thumbnails">
		
			</ul>
		</div>
	</div>
</body>
</html>