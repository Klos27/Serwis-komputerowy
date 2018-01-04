<?php

	session_start();
	
	if ((!isset($_SESSION['dodanie_zgloszenia'])))
	{
		header('Location: serwis.php');
		exit();
	}
	else{
		unset($_SESSION['dodanie_zgloszenia']);
	}
	// Usuwanie zmiennych z formularza dodawania zgloszenia	
	if(isset($_SESSION['form_komp_producent'])) unset($_SESSION['form_komp_producent']);
	if(isset($_SESSION['form_komp_numer'])) unset($_SESSION['form_komp_numer']);
	if(isset($_SESSION['form_komp_rok'])) unset($_SESSION['form_komp_rok']);
	if(isset($_SESSION['form_komp_opis'])) unset($_SESSION['form_komp_opis']);
	
	//Usuwanie błędów
	if(isset($_SESSION['e_komp_producent'])) unset($_SESSION['e_komp_producent']);
	if(isset($_SESSION['e_komp_numer'])) unset($_SESSION['e_komp_numer']);
	if(isset($_SESSION['e_komp_rok'])) unset($_SESSION['e_komp_rok']);
	if(isset($_SESSION['e_komp_opis'])) unset($_SESSION['e_komp_opis']);
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
	
	Dziękujemy za dodanie zgłoszenia.<br />
	
	
	<?php if(isset($_SESSION['komp_dane'])){
		echo $_SESSION['komp_dane']; 
		unset($_SESSION['komp_dane']);
	}
	?>	
	<br />
	Twoje zgłoszenie możesz zobaczyć na Twoim koncie.
	
</body>
</html>