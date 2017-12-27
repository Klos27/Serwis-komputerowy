<?php

	session_start();

	 if((!isset($_POST['login'])) || (!isset($_POST['haslo']))){
		 header('Location: index.php');
		 exit();
	 }
		
	require_once "connect.php";
	
	$polaczenie = @new mysqli($host,$db_user,$db_password,$db_name);
	
	if($polaczenie->connect_errno != 0){
		echo"Error: ".$polaczenie->connect_errno . " Opis: ". $polaczenie->connect_error;
	}
	else {
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];
		
		// zabezpieczenie przed wstrzykiwaniem SQL
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		$haslo = htmlentities($haslo, ENT_QUOTES, "UTF-8");
		
		
		echo $login;
		echo "<br />";
		echo $haslo;
		
		$sql = "SELECT * FROM uzytkownicy WHERE user='$login' AND pass='$haslo'";
		
		if($rezultat = @$polaczenie->query(sprintf("SELECT * FROM uzytkownicy WHERE user='%s' AND pass='%s'", mysqli_real_escape_string($polaczenie,$login), mysqli_real_escape_string($polaczenie,$haslo)))){	// jeśli zapytanie nie będzie błędne, mysqli broni prze dwstzrykwianiem sql
			$ilu_userow = $rezultat->num_rows;
			if($ilu_userow>0){
				$wiersz = $rezultat->fetch_assoc();	// zrobi nam tablice o indexach takich jak w tabeli sql
				
				$_SESSION['user'] = $wiersz['user'];
				$_SESSION['drewno'] = $wiersz['drewno'];
				$_SESSION['kamien'] = $wiersz['kamien'];
				$_SESSION['zboze'] = $wiersz['zboze'];
				$_SESSION['email'] = $wiersz['email'];
				$_SESSION['dnipremium'] = $wiersz['dnipremium'];
				
				$_SESSION['zalogowany'] = true;
				$_SESSION['id'] = $wiersz['id'];
				
				unset($_SESSION['blad']);
				
				
				$rezultat->close();	//$rezultat->free(); , $rezultat->free_result();
				header('Location: serwis.php');
				
			} else {
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
				header('Location: index.php');
				
				
			}
			
		}
		
		$polaczenie->close();
	}

	
	
	
?>