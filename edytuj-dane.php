<?php

	session_start();
	
	function filtruj($zmienna){
		$zmienna=trim($zmienna);
		$zmienna=htmlspecialchars($zmienna);
		$zmienna=addslashes($zmienna);
	}	
	// pobierz dane użytkownika
	
	require_once("setup-connect.php");
					
	try{
				$polaczenie = oci_connect($db_user, $db_password, $db_host, $db_lang);
				
				if (!$polaczenie){
					// throw new Exception(mysqli_connect_errno());
					$m = oci_error();
					echo $m['message'], "\n";
					echo"Error: ".$m['message'] . " Opis: ". $polaczenie->connect_error;
				}
				else{
					// pobierz dane z bazy
					$user = $_SESSION['id'];
					$stid = oci_parse($polaczenie, "Select * FROM klienci WHERE ID_KLIENTA='$user'");
					$r = oci_execute($stid);
					
					if(!$r) throw new Exception(oci_error());
					
					$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
					
					if($wiersz != false){
						$dane_imie = $wiersz['IMIE'];
						$dane_nazwisko = $wiersz['NAZWISKO'];
						$dane_adres = $wiersz['ADRES'];
						$dane_kodp = $wiersz['KOD_POCZTOWY'];
						$dane_miasto = $wiersz['MIASTO'];
						$dane_wojewodztwo = $wiersz['WOJEWODZTWO'];
						$dane_email = $wiersz['EMAIL'];
					}
					oci_free_statement($stid);					
				}
				oci_close($polaczenie);
			}
	catch(Exception $e){
		echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy spróbowac ponownie później!</span>';
		// echo '<br />Info dev: '.$e;
	}

	// formularz
	if (isset($_POST['email']))
	{
		//Udana walidacja
		$wszystko_OK = true;
		
		// Sprawdź email
		
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)){
			$wszystko_OK=false;
			$_SESSION['e_email']="Podaj poprawny adres email!";
			
		}
		
		// dane osobowe
		
		// sprawdź imie
		$imie = $_POST['imie'];
		if((strlen($imie)<3) || (strlen($imie)>100)){
			$wszystko_OK = false;
			$_SESSION['e_imie']="Imie musi posiadać od 3 do 100 znaków!";
		}
			
		// sprawdz nazwisko
		$nazwisko = $_POST['nazwisko'];
		if((strlen($nazwisko)<3) || (strlen($nazwisko)>100)){
			$wszystko_OK = false;
			$_SESSION['e_nazwisko']="Nazwisko musi posiadać od 3 do 100 znaków!";
		}
		// sprawdz adres
		$adres = $_POST['adres'];
		if((strlen($adres)<3) || (strlen($adres)>200)){
			$wszystko_OK = false;
			$_SESSION['e_adres']="Adres musi posiadać od 3 do 100 znaków!";
		}
		// sprawdz kod pocztowy
		$kodp = $_POST['kodp'];
		if((strlen($kodp) != 5)){
			$wszystko_OK = false;
			$_SESSION['e_kodp']="Kod pocztowy musi posiadać 5 cyfr!";
		}
		if(ctype_digit($kodp) == false){
			$wszystko_OK=false;
			$_SESSION['e_kodp']="Kod pocztowy musi składać się z samych cyfr";
		}
		// sprawdz miasto
		$miasto = $_POST['miasto'];
		if((strlen($miasto)<3) || (strlen($miasto)>200)){
			$wszystko_OK = false;
			$_SESSION['e_miasto']="Miasto musi posiadać od 3 do 50 znaków!";
		}
	
		$wojewodztwo = $_POST['wojewodztwo'];
			
		// Zapamietaj wprowadzone dane
		
		$_SESSION['form_email'] = $email;		
		$_SESSION['form_imie'] = $imie;
		$_SESSION['form_nazwisko'] = $nazwisko;
		$_SESSION['form_adres'] = $adres;
		$_SESSION['form_kodp'] = $kodp;
		$_SESSION['form_miasto'] = $miasto;
		$_SESSION['form_wojewodztwo'] = $wojewodztwo;
		
		
		// SQL INJECTION
		// $email = htmlspecialchars($email);
		// $imie = htmlspecialchars($imie);
		// $nazwisko = htmlspecialchars($nazwisko);
		// $adres = htmlspecialchars($adres);
		// $miasto = htmlspecialchars($miasto);
			
		$email = htmlentities($email, ENT_QUOTES, "UTF-8");
		$imie = htmlentities($imie, ENT_QUOTES, "UTF-8");
		$nazwisko = htmlentities($nazwisko, ENT_QUOTES, "UTF-8");
		$adres = htmlentities($adres, ENT_QUOTES, "UTF-8");
		$miasto = htmlentities($miasto, ENT_QUOTES, "UTF-8");	
			
		require_once("setup-connect.php");
		
		mysqli_report(MYSQLI_REPORT_STRICT);	// nie wyświetla błędów serwera, chroni nasze dane bazy przed użytkownikami
		
		if($wszystko_OK ==true){
			try{
				$polaczenie = oci_connect($db_user, $db_password, $db_host, $db_lang);
				
				if (!$polaczenie){
					// throw new Exception(mysqli_connect_errno());
					$m = oci_error();
					echo $m['message'], "\n";
					echo"Error: ".$m['message'] . " Opis: ". $polaczenie->connect_error;
				}
				else{
					// czy email już istnieje u innego uzytkownika
					$stid = oci_parse($polaczenie, "Select ID_KLIENTA FROM klienci WHERE email='$email' and ID_KLIENTA != '$user'");
					$r = oci_execute($stid);
					
					if(!$r) throw new Exception(oci_error());
					
					$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
					
					if($wiersz != false){
						$wszystko_OK=false;
						$_SESSION['e_email']="Inny użytkownik już używa tego adresu email!";
					}
					oci_free_statement($stid);
					
					if($wszystko_OK ==true){
						
						$stid = oci_parse($polaczenie, "UPDATE klienci SET IMIE = '$imie', NAZWISKO = '$nazwisko', ADRES = '$adres', KOD_POCZTOWY = '$kodp', MIASTO ='$miasto', WOJEWODZTWO = '$wojewodztwo', EMAIL = '$email' where ID_KLIENTA = '$user'");
						
						
						
						$r = oci_execute($stid);
					
						if($r == true){
							$_SESSION['zmiana_wynik']= "DANE W BAZIE ZOSTAŁY ZMIENIONE<br />";
							$dane_imie = $imie;
							$dane_nazwisko = $nazwisko;
							$dane_adres = $adres;
							$dane_kodp = $kodp;
							$dane_miasto = $miasto;
							$dane_wojewodztwo = $wojewodztwo;
							$dane_email = $email;
						}
						else{
							$_SESSION['zmiana_wynik']= "NIE UDAŁO SIĘ ZMIENIĆ DANYCH, SPRÓBUJ PONOWNIE PÓŹNIEJ<br />";
							
						}
						oci_free_statement($stid);
					}
					
					
					oci_close($polaczenie);
					// $polaczenie->close();
				}
			}
			catch(Exception $e){
				echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
				echo '<br />Info dev: '.$e;
			}
		}
	}
	else {
		$_SESSION['form_email'] = $dane_email;
		
		$_SESSION['form_imie'] = $dane_imie;
		$_SESSION['form_nazwisko'] = $dane_nazwisko;
		$_SESSION['form_adres'] = $dane_adres;
		$_SESSION['form_kodp'] = $dane_kodp;
		$_SESSION['form_miasto'] = $dane_miasto;
		$_SESSION['form_wojewodztwo'] = $dane_wojewodztwo;
	}
	
	
	
	
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>SBD - Serwis Komputerowy</title>
	
	<style>
	.error
	{
		color:red;
		margin-top: 5px;
		margin-bottom: 5px;
	}
	</style>
	
</head>

<body>
	<a href="index.php"><img src="img/Logo.png" alt="Strona Główna"/></a><br /><br />
	<a href="zmiana-hasla.php">[ Zmiana hasła ]</a><br /><br />
	
		
<?php



echo<<<END
Twoje aktualne dane:<br />
Imię: $dane_imie<br />
Nazwisko: $dane_nazwisko<br />
Email: $dane_email<br />
Adres: $dane_adres<br />
Kod pocztowy: $dane_kodp<br />
Miasto: $dane_miasto<br />
Województwo: $dane_wojewodztwo<br />
END;
?>	
	<br />Zmień swoje dane:<br />
	<form method="post" accept-charset="UTF-8">
	
		E-mail: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_email'])){
			echo $_SESSION['form_email'];
			unset($_SESSION['form_email']);
		}
		?>" name="email" maxlength="50" required /> <br />
		<?php
		if(isset($_SESSION['e_email'])){
			echo '<div class="error">'.$_SESSION['e_email'].'</div>';
			unset($_SESSION['e_email']);
		}
		?>
		<!--Dane osobowe     -->
		Imię: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_imie'])){
			echo $_SESSION['form_imie'];
			unset($_SESSION['form_imie']);
		}
		?>" name="imie" maxlength="100" required /> <br />
		<?php
		if(isset($_SESSION['e_imie'])){
			echo '<div class="error">'.$_SESSION['e_imie'].'</div>';
			unset($_SESSION['e_imie']);
		}
		?>
		Nazwisko: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_nazwisko'])){
			echo $_SESSION['form_nazwisko'];
			unset($_SESSION['form_nazwisko']);
		}
		?>" name="nazwisko" maxlength="100" required /> <br />
		<?php
		if(isset($_SESSION['e_nazwisko'])){
			echo '<div class="error">'.$_SESSION['e_nazwisko'].'</div>';
			unset($_SESSION['e_nazwisko']);
		}
		?>
		Adres: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_adres'])){
			echo $_SESSION['form_adres'];
			unset($_SESSION['form_adres']);
		}
		?>" name="adres" maxlength="200" required /> <br />
		<?php
		if(isset($_SESSION['e_adres'])){
			echo '<div class="error">'.$_SESSION['e_adres'].'</div>';
			unset($_SESSION['e_adres']);
		}
		?>
		Kod pocztowy ( bez - ): <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_kodp'])){
			echo $_SESSION['form_kodp'];
			unset($_SESSION['form_kodp']);
		}
		?>" name="kodp" maxlength="5" required /> <br />
		<?php
		if(isset($_SESSION['e_kodp'])){
			echo '<div class="error">'.$_SESSION['e_kodp'].'</div>';
			unset($_SESSION['e_kodp']);
		}
		?>
		Miasto: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_miasto'])){
			echo $_SESSION['form_miasto'];
			unset($_SESSION['form_miasto']);
		}
		?>" name="miasto" maxlength="50" required /> <br />
		<?php
		if(isset($_SESSION['e_miasto'])){
			echo '<div class="error">'.$_SESSION['e_miasto'].'</div>';
			unset($_SESSION['e_miasto']);
		}
		?>
		<br />
		<label>Województwo:</label>
		<select name="wojewodztwo" >
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "dolnośląskie")) echo 'selected'; ?> value="dolnośląskie" >dolnośląskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "kujawsko-pomorskie")) echo 'selected'; ?> value="kujawsko-pomorskie" >kujawsko-pomorskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "lubelskie")) echo 'selected'; ?> value="lubelskie" >lubelskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "lubuskie")) echo 'selected'; ?> value="lubuskie">lubuskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "łódzkie")) echo 'selected'; ?> value="łódzkie" >łódzkie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "małopolskie")) echo 'selected'; ?> value="małopolskie" >małopolskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "mazowieckie")) echo 'selected'; ?> value="mazowieckie" >mazowieckie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "opolskie")) echo 'selected'; ?> value="opolskie" >opolskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "podkarpackie")) echo 'selected'; ?> value="podkarpackie" >podkarpackie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "podlaskie")) echo 'selected'; ?> value="podlaskie" >podlaskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "pomorskie")) echo 'selected'; ?> value="pomorskie" >pomorskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "śląskie")) echo 'selected'; ?> value="śląskie" >śląskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "świętokrzyskie")) echo 'selected'; ?> value="świętokrzyskie" >świętokrzyskie
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "warmińsko-mazurskie")) echo 'selected'; ?> value="warmińsko-mazurskie" >warmińsko-mazurskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "wielkopolskie")) echo 'selected'; ?> value="wielkopolskie" >wielkopolskie</option>
			<option <?php if((isset($_SESSION['form_wojewodztwo'])) && ($_SESSION['form_wojewodztwo'] == "zachodniopomorskie")) echo 'selected'; ?> value="zachodniopomorskie" >zachodniopomorskie</option>
		</select>
		<br />
		<input type="submit" value="Zapisz dane" />
	
	</form>
	<br /><br />
	<?php if(isset($_SESSION['zmiana_wynik'])) echo $_SESSION['zmiana_wynik']; unset($_SESSION['zmiana_wynik']);?>
	


</body>
</html>