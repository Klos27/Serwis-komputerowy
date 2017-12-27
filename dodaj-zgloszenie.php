<?php
	session_start();
	if(!isset($_SESSION['zalogowany'])){
		header('Location: index.php');
		exit();
	}
		
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>SBD - Serwis Komputerowy</title>
</head>
<body>
	
	<?php
	
	echo "<p>Witaj ".$_SESSION['user']."!</p>";
	echo '<a href="wyloguj.php">[ Wyloguj się ]</a>';
	
	echo "<p><b>Drewno:</b>:".$_SESSION['drewno'];
	echo " | <b>Kamień:</b>:".$_SESSION['kamien'];
	echo " | <b>Zboże:</b>:".$_SESSION['zboze']."</p>";
	
	echo "<p><b>Email:</b>:".$_SESSION['email'];
	echo "<br /><b>Dni Premium:</b>:".$_SESSION['dnipremium']."</p>";

	
	?>

</body>
</html>