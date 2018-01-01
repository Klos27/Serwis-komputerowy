<?php
	require_once "setup-connect.php";
	require_once "setup-mail.php";
	$polaczenie = oci_connect($db_user,$db_password,$host);

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
		$tytul = "Przypomnienie hasla dla uzytkownika ".$login;
		
		// Pobierz hasło użytkownika
		$query = "SELECT * FROM klienci WHERE login='$login' AND email='$email'";
		
		$stid = oci_parse($polaczenie, $query);
		$r = oci_execute($stid);
		$wyslac_email = false;
		if($stid ){	
			$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
			if($wiersz>0){
				$wyslac_email = true;
				$haslo = $wiersz['HASLO'];
				oci_free_statement($stid);
				
			}			
		}
		
		oci_close($polaczenie);
		
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
			header('Location: potwierdzenie.php');
			//print "<meta http-equiv=\"refresh\" content=\"0;URL=potwierdzenie.php\">";
		}
		else{
			header('Location: error.php');
		  // print "<meta http-equiv=\"refresh\" content=\"0;URL=error.php\">";
		}
	}
?>
