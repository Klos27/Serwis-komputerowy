<?php
	require_once "setup-connect.php";
	require_once "setup-mail.php";
	$polaczenie = oci_connect($db_user,$db_password,$db_host);
	
	
	function randomPassword() {
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
		$pass = array(); //remember to declare $pass as an array
		$alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
		for ($i = 0; $i < 8; $i++) {
			$n = rand(0, $alphaLength);
			$pass[] = $alphabet[$n];
		}
		return implode($pass); //turn the array into a string
	}

	if(!$polaczenie){
		$m = oci_error();
		echo $m['message'], "\n";
		echo"Error: ".$m['message'] . " Opis: ". $polaczenie->connect_error;
	}
	else {
		$login = $_POST['login'];
		$email = $_POST['email'];
		
		// zabezpieczenie przed wstrzykiwaniem SQL
		$login = htmlentities($login, ENT_QUOTES, "UTF-8");
		$email = htmlentities($email, ENT_QUOTES, "UTF-8");
				
		// $odkogo = "viperowski@gmail.com";
		$dokogo = $email;
		$tytul = "Nowe hasło dla użytkownika ".$login;
		
		// Pobierz hasło użytkownika
		$query = "SELECT * FROM klienci WHERE login='$login' AND email='$email'";
		
		$stid = oci_parse($polaczenie, $query);
		$r = oci_execute($stid);
		$wyslac_email = false;
		
		if($stid ){	
			$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
			if($wiersz>0){
				$wyslac_email = true;
				$user = $wiersz['ID_KLIENTA'];
				oci_free_statement($stid);
				
			}			
		}
		
		
		// wygeneruj nowe haslo
		$haslo = randomPassword();
		$haslo_hash = password_hash($haslo, PASSWORD_DEFAULT);
		$wiadomosc = "";
		$wiadomosc .= "Login: " . $login . "\n";
		$wiadomosc .= "Twoje hasło: " . $haslo . "\n";
		$wiadomosc .= "Pozdrawiamy zespół SBD-Serwis";
		
		
		// Wysyłamy wiadomość

		// Dodajemy UTF-8 do naglowka naszej wiadomości
		$naglowek = "";
		$naglowek .= "Od:" . $odkogo . " \n";
		$naglowek .= "Content-Type:text/plain;charset=utf-8";
		if($wyslac_email)
			$sukces = mail($dokogo, $tytul, $wiadomosc, $naglowek);
		else
			$sukces = true;
		// Przekierowywujemy na potwierdzenie
		if ($sukces){
			// zmiana hasła w bazie:
			$query = "UPDATE klienci SET HASLO = '$haslo_hash' where ID_KLIENTA = '$user'";
			$stid = oci_parse($polaczenie, $query);
			$r = oci_execute($stid);
			
			if($r)
				header('Location: potwierdzenie-wyslania-email.php');
			else
				header('Location: error-zmiany-hasla.php');
		}
		else{
			header('Location: error-wyslania-email.php');
		  // print "<meta http-equiv=\"refresh\" content=\"0;URL=error.php\">";
		}
		
		oci_close($polaczenie);
	}
?>
