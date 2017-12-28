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
	echo "<p><b>Email:</b>  ".$_SESSION['email'];
	echo "     ";
	echo '<a href="wyloguj.php">[ Wyloguj się ]</a>';
	echo "<br /><br />";
	
	//PODSTRONA
	echo '<a href="dodaj-zgloszenie.php">[ Dodaj nowe zgłoszenie ]</a>';
	echo "<br /><br />";
	echo '<a href="zobacz-zgloszenia.php">[ Zobacz twoje zgłoszenia ]</a>';
	echo "<br /><br />";
	echo '<a href="edytuj-dane.php">[ Edytuj dane osobowe ]</a>';
	echo "<br /><br />";
		
	?>

</body>
</html>