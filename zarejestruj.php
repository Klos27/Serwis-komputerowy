<?php

	session_start();
	
	function filtruj($zmienna){
	$zmienna=trim($zmienna);
	$zmienna=htmlspecialchars($zmienna);
	$zmienna=addslashes($zmienna);
	}
	
	require_once("setup-recaptcha.php");
	
	
	// formularz
	if (isset($_POST['email']))
	{
		//Udana walidacja
		$wszystko_OK = true;
		
		//Sprawdź login
		$login = $_POST['login'];	//3 do 20znaków
		
		//Sprawdzenie długości loginu
		if((strlen($login)<3) || (strlen($login)>20)){
			$wszystko_OK = false;
			$_SESSION['e_login']="login musi posiadać od 3 do 20 znaków!";
		}
		if(ctype_alnum($login)==false){
			$wszystko_OK=false;
			$_SESSION['e_login']="login może składać się tylko z cyfr i liter( bez polskich znaków)";
		}
		
		// Sprawdź email
		
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if((filter_var($emailB, FILTER_VALIDATE_EMAIL) == false) || ($emailB != $email)){
			$wszystko_OK=false;
			$_SESSION['e_email']="Podaj poprawny adres email!";
			
		}
			
		// Sprawdź hasła
		
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
			
		if((strlen($haslo1)<5) || (strlen($haslo1)>20)){
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Hasło musi posiadać od 5 do 20 znaków!";
		}		
		if($haslo1 != $haslo2){
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Podane hasła nie są identyczne!";
		}
			
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		// czy zaakceptowano regulamin
		if(!isset($_POST['regulamin'])){
			$wszystko_OK=false;
			$_SESSION['e_regulamin']="Przeczytaj i zaakceptuj regulamin!";
		}		
		// dane osobowe
		
		// sprawdź imie
		$imie = $_POST['imie'];
		if((strlen($imie)<3) || (strlen($imie)>20)){
			$wszystko_OK = false;
			$_SESSION['e_imie']="Imie musi posiadać od 3 do 20 znaków!";
		}
		if(ctype_alnum($imie)==false){
			$wszystko_OK=false;
			$_SESSION['e_imie']="Imie może składać się tylko z cyfr i liter( bez polskich znaków)";
		}
			
			
		$nazwisko = $_POST['nazwisko'];
		$adres = $_POST['adres'];
		$kodp = $_POST['kodp'];
		$miasto = $_POST['miasto'];
		$wojewodztwo = $_POST['wojewodztwo'];
			
		//TODO dodać weryfikację danych z formularza

		
		
		
		// Bot or not?
		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$recaptcha_secret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if($odpowiedz->success == false){
			$wszystko_OK=false;
			$_SESSION['e_captcha']="Potwierdź, że nie jesteś botem!";
		}		
		
		// Zapamietaj wprowadzone dane
		
		$_SESSION['form_login'] = $login;
		$_SESSION['form_email'] = $email;
		$_SESSION['form_haslo1'] = $haslo1;
		$_SESSION['form_haslo2'] = $haslo2;
		
		$_SESSION['form_imie'] = $imie;
		$_SESSION['form_nazwisko'] = $nazwisko;
		$_SESSION['form_adres'] = $adres;
		$_SESSION['form_kodp'] = $kodp;
		$_SESSION['form_miasto'] = $miasto;
		$_SESSION['form_wojewodztwo'] = $wojewodztwo;
		
		if(isset($_POST['regulamin'])) $_SESSION['form_regulamin'] = true;	
			
	
			
		require_once("setup-connect.php");
		
		mysqli_report(MYSQLI_REPORT_STRICT);	// nie wyświetla błędów serwera, chroni nasze dane bazy przed użytkownikami
		
		// if($wszystko_OK ==true){
			try{
				$polaczenie = oci_connect($db_user,$db_password,$host);
				
				if (!$polaczenie){
					// throw new Exception(mysqli_connect_errno());
					$m = oci_error();
					echo $m['message'], "\n";
					echo"Error: ".$m['message'] . " Opis: ". $polaczenie->connect_error;
				}
				else{
					// czy email już istnieje?
					$stid = oci_parse($polaczenie, "Select ID_KLIENTA FROM klienci WHERE email='$email'");
					$r = oci_execute($stid);
					
					if(!$r) throw new Exception(oci_error());
					
					$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
					
					if($wiersz != false){
						$wszystko_OK=false;
						$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu email!";
					}
					oci_free_statement($stid);
					
					// czy login już istnieje?
					$stid = oci_parse($polaczenie, "Select ID_KLIENTA FROM klienci WHERE login='$login'");
					$r = oci_execute($stid);
					
					if(!$r) throw new Exception(oci_error());
					
					if($wiersz != false){
						$wszystko_OK=false;
						$_SESSION['e_login']="Istnieje już konto z takim loginem! Wybierz inny";
					}
					oci_free_statement($stid);
					
					if($wszystko_OK ==true){
					// wszystko ok
					// insert
					// przekierowanie do potwierdzenia rejestracji
						$stid = oci_parse($polaczenie, "INSERT INTO klienci VALUES(NULL, '$imie', '$nazwisko', '$adres', '$kodp', '$miasto', '$wojewodztwo', '$haslo_hash', '$login', '$email')");
						$r = oci_execute($stid);
					
						if($r == true){
							$_SESSION['rejestracja']=true;
							header('Location: witamy.php');
						}
						else{
							throw new Exception(oci_error());
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
		// }
		
		
	}
	
	
	
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>SBD - Serwis Komputerowy</title>
	<script src='https://www.google.com/recaptcha/api.js'></script>
	
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
	<a href="index.php"><img src="img/Logo.png" alt="Strona Główna"/></a><br />
	<form method="post">
		Wypełnij formularz rejestracyjny:<br /><br />
		Login: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_login'])){
			echo $_SESSION['form_login'];
			unset($_SESSION['form_login']);
		}
		?>" name="login" required /> <br />
		<?php
		if(isset($_SESSION['e_login'])){
			echo '<div class="error">'.$_SESSION['e_login'].'</div>';
			unset($_SESSION['e_login']);
		}
		?>
		E-mail: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_email'])){
			echo $_SESSION['form_email'];
			unset($_SESSION['form_email']);
		}
		?>" name="email" required /> <br />
		<?php
		if(isset($_SESSION['e_email'])){
			echo '<div class="error">'.$_SESSION['e_email'].'</div>';
			unset($_SESSION['e_email']);
		}
		?>
		Twoje hasło: <br /> <input type="password" value="<?php
		if(isset($_SESSION['form_haslo1'])){
			echo $_SESSION['form_haslo1'];
			unset($_SESSION['form_haslo1']);
		}
		?>" name="haslo1" required /> <br />
		<?php
		if(isset($_SESSION['e_haslo'])){
			echo '<div class="error">'.$_SESSION['e_haslo'].'</div>';
			unset($_SESSION['e_haslo']);
		}
		?>
		Powtórz hasło: <br /> <input type="password" value="<?php
		if(isset($_SESSION['form_haslo2'])){
			echo $_SESSION['form_haslo2'];
			unset($_SESSION['form_haslo2']);
		}
		?>"  name="haslo2" required /> <br />
		<!--Dane osobowe     -->
		Imię: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_imie'])){
			echo $_SESSION['form_imie'];
			unset($_SESSION['form_imie']);
		}
		?>" name="imie" required /> <br />
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
		?>" name="nazwisko" required /> <br />
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
		?>" name="adres" required /> <br />
		<?php
		if(isset($_SESSION['e_adres'])){
			echo '<div class="error">'.$_SESSION['e_adres'].'</div>';
			unset($_SESSION['e_adres']);
		}
		?>
		Kod pocztowy: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_kodp'])){
			echo $_SESSION['form_kodp'];
			unset($_SESSION['form_kodp']);
		}
		?>" name="kodp" required /> <br />
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
		?>" name="miasto" required /> <br />
		<?php
		if(isset($_SESSION['e_miasto'])){
			echo '<div class="error">'.$_SESSION['e_miasto'].'</div>';
			unset($_SESSION['e_miasto']);
		}
		?>
		Województwo: <br /> <input type="text" value="<?php
		if(isset($_SESSION['form_wojewodztwo'])){
			echo $_SESSION['form_wojewodztwo'];
			unset($_SESSION['form_wojewodztwo']);
		}
		?>" name="wojewodztwo" required /> <br />
		<?php
		if(isset($_SESSION['e_wojewodztwo'])){
			echo '<div class="error">'.$_SESSION['e_wojewodztwo'].'</div>';
			unset($_SESSION['e_wojewodztwo']);
		}
		?>
		<label>Województwo:</label>
		<input type="text" list="wojewodztwa">
		<datalist id="wojewodztwa">
			<option value="dolnośląskie">
			<option value="kujawsko-pomorskie">
			<option value="lubelskie">
			<option value="lubuskie">
			<option value="łódzkie">
			<option value="małopolskie">
			<option value="mazowieckie">
			<option value="opolskie">
			<option value="podkarpackie">
			<option value="podlaskie">
			<option value="pomorskie">
			<option value="śląskie">
			<option value="świętokrzyskie">
			<option value="warmińsko-mazurskie">
			<option value="wielkopolskie">
			<option value="Zachodniopomorskie">
		</datalist>
		
		
		
		
		<label>
		<input type="checkbox" name="regulamin" <?php  
		if(isset($_SESSION['form_regulamin'])){
			echo "checked";
			unset($_SESSION['form_regulamin']);
		}
		
		?>/> Akceptuje <a href="regulamin.php" target="_blank">regulamin</a>
		</label>
		<br />
		<?php
		if(isset($_SESSION['e_regulamin'])){
			echo '<div class="error">'.$_SESSION['e_regulamin'].'</div>';
			unset($_SESSION['e_regulamin']);
		}
		?>
		<br />
		<div class="g-recaptcha" data-sitekey="<?php echo $recaptha_public; ?>"></div>
		<br />
		<?php
		if(isset($_SESSION['e_captcha'])){
			echo '<div class="error">'.$_SESSION['e_captcha'].'</div>';
			unset($_SESSION['e_captcha']);
		}
		?><br />
		<input type="submit" value="Zarejestruj się" />
	
	</form>
	


</body>
</html>