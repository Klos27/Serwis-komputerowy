<?php

	session_start();
	
	function filtruj($zmienna){
	$zmienna=trim($zmienna);
	$zmienna=htmlspecialchars($zmienna);
	$zmienna=addslashes($zmienna);
	}
	
	function db_escape_mimic($str) { 
		if(is_array($str)) 
			return array_map(__METHOD__, $str); 

		if(!empty($str) && is_string($str)) { 
			return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $str); 
		} 

		return $str; 
	} 
	
	require_once("setup-recaptcha.php");

	// formularz
	if (isset($_POST['komp_producent']))
	{
		//Udana walidacja
		$wszystko_OK = true;
		
		//Sprawdź producenta
		$komp_producent = $_POST['komp_producent'];
		
		if((strlen($komp_producent)<3) || (strlen($komp_producent)>30)){
			$wszystko_OK = false;
			$_SESSION['e_komp_producent']="Pole producent musi posiadać od 3 do 30 znaków!";
		}
		if(ctype_alnum($komp_producent) == false){
			$wszystko_OK=false;
			$_SESSION['e_komp_producent']="Pole producent może składać się tylko z cyfr i liter( bez polskich znaków)";
		}
		//Sprawdź numer seryjny
		$komp_numer = $_POST['komp_numer'];
		
		if((strlen($komp_numer)<3) || (strlen($komp_numer)>30)){
			$wszystko_OK = false;
			$_SESSION['e_komp_numer']="Numer seryjny musi posiadać od 3 do 50 znaków!";
		}
		if(ctype_alnum($komp_numer) == false){
			$wszystko_OK=false;
			$_SESSION['e_komp_numer']="Numer seryjny może składać się tylko z cyfr i liter( bez polskich znaków)";
		}
		//Sprawdź rok produkcji
		$komp_rok = $_POST['komp_rok'];
		
		if((strlen($komp_rok)<4) || (strlen($komp_rok)>4)){
			$wszystko_OK = false;
			$_SESSION['e_komp_rok']="Rok produkcji musi byś pomiędzy rokiem 1980 a 2999!";
		}
		if(ctype_digit($komp_rok) == false){
			$wszystko_OK=false;
			$_SESSION['e_komp_rok']="Rok produkcji może składać się tylko z cyfr";
		}
		else{
			if(($komp_rok < 1980) || ($komp_rok > 2999)){
				$wszystko_OK=false;
				$_SESSION['e_komp_rok']="Rok produkcji musi byś pomiędzy rokiem 1980 a 2999!";
			}	
		}
		//Sprawdź opis
		$komp_opis = $_POST['komp_opis'];
		
		if((strlen($komp_opis)<10) || (strlen($komp_opis)>4000)){
			$wszystko_OK = false;
			$dlugosc = strlen($komp_opis);
			$_SESSION['e_komp_opis']="Opis usterki musi posiadać od 10 do 4000 znaków! Aktualna ilość = $dlugosc";
		}
	
		// Bot or not?
		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$recaptcha_secret.'&response='.$_POST['g-recaptcha-response']);
		
		$odpowiedz = json_decode($sprawdz);
		
		if($odpowiedz->success == false){
			// $wszystko_OK=false;
			// $_SESSION['e_captcha']="Potwierdź, że nie jesteś botem!";
		}		
		
		// Zapamietaj wprowadzone dane
		
		$_SESSION['form_komp_producent'] = $komp_producent;
		$_SESSION['form_komp_numer'] = $komp_numer;
		$_SESSION['form_komp_rok'] = $komp_rok;
		
				
		// SQL INJECTION
		// $komp_opis = db_escape_mimic($komp_opis);
		$komp_opis = htmlentities($komp_opis, ENT_QUOTES, "UTF-8");
		$_SESSION['form_komp_opis'] = $komp_opis;
		
		require_once("setup-connect.php");
		
		// wpisz do bazy
	
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
					// czy komputer już istnieje?
					$stid = oci_parse($polaczenie, "Select * FROM KOMPUTERY WHERE NR_SERYJNY='$komp_numer'");
					$r = oci_execute($stid);
					
					if(!$r) throw new Exception(oci_error());
					
					$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
					
					if($wiersz == false){
						//dodaj komputer do bazy
						oci_free_statement($stid);
						$stid = oci_parse($polaczenie, "INSERT INTO KOMPUTERY VALUES(NULL, '$komp_producent', '$komp_rok', '$komp_numer')");
						$r = oci_execute($stid);
					
						if($r == true){
							$_SESSION['komp_dane']="Komputer został pomyślnie dodany do bazy";
						}
						else{
							throw new Exception(oci_error());
						}
					}
					else{
						// komputer już istnieje
						oci_free_statement($stid);
						$stid = oci_parse($polaczenie, "UPDATE KOMPUTERY SET PRODUCENT = '$komp_producent', ROK_PRODUKCJI = '$komp_rok' WHERE NR_SERYJNY like '$komp_numer' ");
						$r = oci_execute($stid);
					
						if($r == true){
							$_SESSION['komp_dane']="Istnieje już komputer o padanym numerze seryjnym<br />producent i data produkcji została zaktualizowana<br />";
						}
						else{
							throw new Exception(oci_error());
						}
					}
					oci_free_statement($stid);
					
					// Dodaj zgłoszenie do bazy
					$user = $_SESSION['id'];
					$stid = oci_parse($polaczenie, "INSERT INTO ZAMOWIENIE_NAPRAWY VALUES (NULL, '$user', (SELECT ID_KOMPUTERA from KOMPUTERY where NR_SERYJNY like '$komp_numer') , '$komp_opis', sysdate , NULL , 'nowy')");

					$r = oci_execute($stid);
				
					if($r == true){
						$_SESSION['dodanie_zgloszenia']=true;
						header('Location: potwierdzenie-dodania.php');
					}
					else{
						throw new Exception(oci_error());
					}
					oci_free_statement($stid);
					
					
					
					oci_close($polaczenie);
					// $polaczenie->close();
				}
			}
			catch(Exception $e){
				echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o dodanie zgłoszenia w innym terminie!</span>';
				// echo '<br />Info dev: '.$e;
			}
		}
	
	
	
	
	
	
	
	
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
	<form method="post" accept-charset="UTF-8">
		Wypełnij formularz zgłoszenia usterki:<br /><br />
		Producent komputera: ( dla komputera stacjonarnego wpisz producenta płyty głównej )<br /> <input type="text" value="<?php
		if(isset($_SESSION['form_komp_producent'])){
			echo $_SESSION['form_komp_producent'];
			unset($_SESSION['form_komp_producent']);
		}
		?>" name="komp_producent" maxlength="30" required /> <br />
		<?php
		if(isset($_SESSION['e_komp_producent'])){
			echo '<div class="error">'.$_SESSION['e_komp_producent'].'</div>';
			unset($_SESSION['e_komp_producent']);
		}
		?>
		Numer seryjny: ( dla komputera stacjonarnego wpisz numer seryjny płyty głównej )<br /> <input type="text" value="<?php
		if(isset($_SESSION['form_komp_numer'])){
			echo $_SESSION['form_komp_numer'];
			unset($_SESSION['form_komp_numer']);
		}
		?>" name="komp_numer" maxlength="50" /> <br />
		<?php
		if(isset($_SESSION['e_komp_numer'])){
			echo '<div class="error">'.$_SESSION['e_komp_numer'].'</div>';
			unset($_SESSION['e_komp_numer']);
		}
		?>
		Rok Produkcji: <br /> <input type="number" value="<?php
		if(isset($_SESSION['form_komp_rok'])){
			echo $_SESSION['form_komp_rok'];
			unset($_SESSION['form_komp_rok']);
		}
		?>" name="komp_rok" min="1990" max="2999" required /> <br />
		<?php
		if(isset($_SESSION['e_komp_rok'])){
			echo '<div class="error">'.$_SESSION['e_komp_rok'].'</div>';
			unset($_SESSION['e_komp_rok']);
		}
		?>
		<br />
		Opis usterki: <br />
		<textarea id="text" name="komp_opis" rows="15" cols="70" class="required" placeholder="Tutaj opisz usterkę max 4000znaków" maxlength="4000" ><?php
		if(isset($_SESSION['form_komp_opis'])){
			echo $_SESSION['form_komp_opis'];
			unset($_SESSION['form_komp_opis']);
		}
		?></textarea><br />
		<?php
		if(isset($_SESSION['e_komp_opis'])){
			echo '<div class="error">'.$_SESSION['e_komp_opis'].'</div>';
			unset($_SESSION['e_komp_opis']);
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
		<input type="submit" value="Dodaj zgłoszenie" />
	
	</form>
	


</body>
</html>