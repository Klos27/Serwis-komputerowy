<?php

	session_start();

	 if((!isset($_POST['login'])) || (!isset($_POST['haslo']))){
		 header('Location: index.php');
		 exit();
	 }
		
	require_once "setup-connect.php";
	
	$polaczenie = oci_connect($db_user,$db_password,$db_host, $db_lang);
	
	if(!$polaczenie){
		$m = oci_error();
		echo $m['message'], "\n";
		echo"Error: ".$m['message'] . " Opis: ". $polaczenie->connect_error;
	}
	else {
		$login = $_POST['login'];
		$haslo = $_POST['haslo'];
		
		// zabezpieczenie przed wstrzykiwaniem SQL
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		// $haslo = htmlentities($haslo, ENT_QUOTES, "UTF-8");
		
		
		// echo $login;
		// echo "<br />";
		// echo $haslo;
		
		$query = "SELECT * FROM klienci WHERE login='$login'";
		
		
		$stid = oci_parse($polaczenie, $query);
		$r = oci_execute($stid);
		
		
		
		if($stid ){	// jeśli zapytanie nie będzie błędne, mysqli broni prze wstzrykwianiem sql
			//$ilu_userow = $rezultat->num_rows;
			$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
			if($wiersz != false){
				//$wiersz = $rezultat->fetch_assoc();	// zrobi nam tablice o indexach takich jak w tabeli sql
				if(password_verify($haslo, $wiersz['HASLO'])){
					$_SESSION['user'] = $wiersz['LOGIN'];
					$_SESSION['email'] = $wiersz['EMAIL'];
					$_SESSION['id'] = $wiersz['ID_KLIENTA'];
					$_SESSION['zalogowany'] = true;
					
					
					unset($_SESSION['blad']);
					
					
					// $stid->oci_close();	//$rezultat->free(); , $rezultat->free_result();
					oci_free_statement($stid);
					header('Location: serwis.php');
				}else {
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
				header('Location: index.php');
				
				
				}
				
			} else {
				$_SESSION['blad'] = '<span style="color:red">Nieprawidłowy login lub hasło!</span>';
				header('Location: index.php');
				
				
			}
			
		}else {
			$_SESSION['blad'] = '<span style="color:red">Przepraszamy, nie udało się połączyć z bazą danych, spróbuj ponownie później</span>';
				header('Location: index.php');
				
				
		}
		
		oci_close($polaczenie);
	}

	
	
	
?>