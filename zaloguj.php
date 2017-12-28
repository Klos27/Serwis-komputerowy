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
		
		$sql = "SELECT * FROM klienci WHERE login='$login' AND haslo='$haslo'";
		
		if($rezultat = @$polaczenie->query(sprintf($sql, mysqli_real_escape_string($polaczenie,$login), mysqli_real_escape_string($polaczenie,$haslo)))){	// jeśli zapytanie nie będzie błędne, mysqli broni prze wstzrykwianiem sql
			$ilu_userow = $rezultat->num_rows;
			if($ilu_userow>0){
				$wiersz = $rezultat->fetch_assoc();	// zrobi nam tablice o indexach takich jak w tabeli sql
				
				$_SESSION['user'] = $wiersz['login'];
				$_SESSION['email'] = $wiersz['email'];
				
				$_SESSION['zalogowany'] = true;
				$_SESSION['id'] = $wiersz['id_klienta'];
				
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