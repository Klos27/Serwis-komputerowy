<?php

	session_start();
	
	require_once("setup-connect.php");
		
	if (isset($_POST['haslo1']))
	{
		//Udana walidacja
		$wszystko_OK = true;

		// Sprawdź hasła
		
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
			
		if((strlen($haslo1)<5) || (strlen($haslo1)>20)){
			$wszystko_OK=false;
			$_SESSION['e_haslo1']='<span style="color:red">Hasło musi posiadać od 5 do 20 znaków!</span>';
		}	
		if($haslo1 != $haslo2){
			$wszystko_OK=false;
			$_SESSION['e_haslo1']='<span style="color:red">Podane hasła nie są identyczne!</span>';
		}
			
		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		// Zapamietaj wprowadzone dane
		$_SESSION['form_haslo1'] = $haslo1;
		$_SESSION['form_haslo2'] = $haslo2;
	
		if($wszystko_OK ==true){
		
			//sprawdz poprzednie hasło
			$polaczenie = oci_connect($db_user, $db_password, $db_host, $db_lang);
		
			if(!$polaczenie){
				$m = oci_error();
				echo $m['message'], "\n";
			}
			else {
				$aktualne_haslo = $_POST['aktualne_haslo'];
				$user = $_SESSION['id'];
				$query = "SELECT * FROM klienci WHERE ID_KLIENTA = '$user'";
				
				$stid = oci_parse($polaczenie, $query);
				$r = oci_execute($stid);

				if($r ){	
					$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
					if($wiersz != false){
						if(password_verify($aktualne_haslo, $wiersz['HASLO'])){
							unset($_SESSION['e_aktualne_haslo']);
						}else {
						$wszystko_OK = false;	
						$_SESSION['e_aktualne_haslo'] = '<span style="color:red">Nieprawne poprzednie hasło!</span>';

						}
						
					} else {
						$_SESSION['e_aktualne_haslo'] = '<span style="color:red">Błąd połączenia z bazą danych</span>';
					}
					
				}else {
					$_SESSION['blad'] = '<span style="color:red">Przepraszamy, nie udało się połączyć z bazą danych, spróbuj ponownie później</span>';
					$wszystko_OK = false;	
				}
				// zaktualizuj haslo
				if($wszystko_OK ==true){
					$query = "UPDATE klienci SET HASLO = '$haslo_hash' WHERE ID_KLIENTA = '$user'";
					
					$stid = oci_parse($polaczenie, $query);
					$r = oci_execute($stid);
					if($r ){
						$_SESSION['wynik_zmiana'] = '<span style="color:red">Hasło zostało zmienione</span>';						
						if(isset($_SESSION['e_haslo1'])) unset($_SESSION['e_haslo1']);
						if(isset($_SESSION['e_haslo2'])) unset($_SESSION['e_haslo2']);
						if(isset($_SESSION['e_aktualne_haslo'])) unset($_SESSION['e_aktualne_haslo']);
						if(isset($_SESSION['form_haslo1'])) unset($_SESSION['form_haslo1']);
						if(isset($_SESSION['form_haslo2'])) unset($_SESSION['form_haslo2']);
						if(isset($_SESSION['form_aktualne_haslo'])) unset($_SESSION['form_aktualne_haslo']);
					} else {
						$_SESSION['wynik_zmiana'] = '<span style="color:red">Nie udało się zmienić hasła, spróbuj ponownie później</span>';
					}
					
				}else {
					$_SESSION['blad'] = '<span style="color:red">Przepraszamy, nie udało się połączyć z bazą danych, spróbuj ponownie później</span>';
					$wszystko_OK = false;	
				}
					
					
			}				
			
			oci_close($polaczenie);
		}
	}
		
		
		
		
		
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>SBD - Serwis Komputerowy</title>
</head>

<body>
	<a href="index.php"><img src="img/Logo.png" alt="Strona Główna"/></a><br /><br />
	<a href="edytuj-dane.php">[ POWRÓT ]</a><br /><br />
	
	
	<form method="post" accept-charset="UTF-8">
		Zmień swoje hasło:<br /><br />
		Aktualne hasło: <br /> <input type="password" name="aktualne_haslo" maxlength="20" required /> <br />
		<?php
		if(isset($_SESSION['e_aktualne_haslo'])){
			echo '<div class="error">'.$_SESSION['e_aktualne_haslo'].'</div>';
			unset($_SESSION['e_aktualne_haslo']);
		}
		?>
		Nowe hasło: <br /> <input type="password" value="<?php
		if(isset($_SESSION['form_haslo1'])){
			echo $_SESSION['form_haslo1'];
			unset($_SESSION['form_haslo1']);
		}
		?>" name="haslo1" maxlength="20" required /> <br />
		<?php
		if(isset($_SESSION['e_haslo1'])){
			echo '<div class="error">'.$_SESSION['e_haslo1'].'</div>';
			unset($_SESSION['e_haslo1']);
		}
		?>
		Powtórz nowe hasło: <br /> <input type="password" value="<?php
		if(isset($_SESSION['form_haslo2'])){
			echo $_SESSION['form_haslo2'];
			unset($_SESSION['form_haslo2']);
		}
		?>"  name="haslo2" maxlength="20" required /> <br />
	
		<input type="submit" value="Zmień hasło" />
	
	</form>
	<?php
	if(isset($_SESSION['wynik_zmiana'])){
			echo $_SESSION['wynik_zmiana'];
			unset($_SESSION['wynik_zmiana']);
		}
	?>
	<br />
	<br />
	
	
	
</body>
</html>