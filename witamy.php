<?php

	session_start();
	
	if ((!isset($_SESSION['rejestracja'])))
	{
		header('Location: index.php');
		exit();
	}
	else{
		unset($_SESSION['rejestracja']);
	}
	// Usuwanie zmiennych z formularza rejestracji
	if(isset($_SESSION['form_login'])) unset($_SESSION['form_login']);
	if(isset($_SESSION['form_email'])) unset($_SESSION['form_email']);
	if(isset($_SESSION['form_haslo1'])) unset($_SESSION['form_haslo1']);
	if(isset($_SESSION['form_haslo2'])) unset($_SESSION['form_haslo2']);
	if(isset($_SESSION['form_imie'])) unset($_SESSION['form_imie']);
	if(isset($_SESSION['form_nazwisko'])) unset($_SESSION['form_nazwisko']);
	if(isset($_SESSION['form_adres'])) unset($_SESSION['form_adres']);
	if(isset($_SESSION['form_kodp'])) unset($_SESSION['form_kodp']);
	if(isset($_SESSION['form_miasto'])) unset($_SESSION['form_miasto']);
	if(isset($_SESSION['form_wojewodztwo'])) unset($_SESSION['form_wojewodztwo']);
	if(isset($_SESSION['form_regulamin'])) unset($_SESSION['form_regulamin']);
	
	//Usuwanie błędów
	if(isset($_SESSION['e_login'])) unset($_SESSION['e_login']);
	if(isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if(isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
	if(isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
	if(isset($_SESSION['e_imie'])) unset($_SESSION['e_imie']);
	if(isset($_SESSION['e_nazwisko'])) unset($_SESSION['e_nazwisko']);
	if(isset($_SESSION['e_adres'])) unset($_SESSION['e_adres']);
	if(isset($_SESSION['e_kodp'])) unset($_SESSION['e_kodp']);
	if(isset($_SESSION['e_miasto'])) unset($_SESSION['e_miasto']);
	if(isset($_SESSION['e_wojewodztwo'])) unset($_SESSION['e_wojewodztwo']);
	if(isset($_SESSION['e_regulamin'])) unset($_SESSION['e_regulamin']);
	
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
	
	Dziękujemy za rejestrację. Możesz się teraz zalogować<br /><br />
	
	<a href="index.php">Zaloguj się na swoje konto!</a>
	

</body>
</html>