<?php
	session_start();
	if((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany'] == true)){
		header('Location: serwis.php');
		exit(); // od razu przenosi, nie przetwarza strony do końca
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
	<a href="index.php"><img src="img/Logo.png" alt="Strona Główna"/></a><br />
	
	<form action="php-zaloguj.php" method="post" accept-charset="UTF-8">
	Login: <br /> <input type="text" name="login" /> <br />
	Hasło: <br /> <input type="password" name="haslo" /> <br />
	<input type="submit" value="Zaloguj się" />
	</form>
	
	
<?php
	if(isset($_SESSION['blad'])) 
		echo $_SESSION['blad'];
?>
	<br />
	<a href="zarejestruj.php">Zarejestruj Się!</a>
	<br />
	<a href="przypomnij-haslo.php">Nie pamiętam hasła</a>
	
	
	
	<br /><br />

</body>
</html>