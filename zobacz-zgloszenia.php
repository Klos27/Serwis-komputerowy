<?php
	session_start();
	if(!isset($_SESSION['zalogowany'])){
		header('Location: index.php');
		exit();
	}
		
?>
<html>
	<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>SBD - Serwis Komputerowy</title>
	</head>
	<body>
	<a href="index.php"><img src="img/Logo.png" alt="Strona Główna"/></a><br />
	Twoje aktualne zgłoszenia:
	
	<table width="1000" align="center" border="1" bordercolor="#d5d5d5"  cellpadding="0" cellspacing="0">
        <tr>
        <?php
		
			$user = $_SESSION['id'];
		
            // ini_set("display_errors", 0);
            require_once "setup-connect.php";
			try{
				$polaczenie = oci_connect($db_user,$db_password,$db_host, $db_lang);
				
				if (!$polaczenie){
					// throw new Exception(mysqli_connect_errno());
					$m = oci_error();
					echo $m['message'], "\n";
					echo"Error: ".$m['message'];
				}
				else{
			
										
					// $zapytanietxt = file_get_contents("zapytanie.txt");
					
					// $rezultat = mysqli_query($polaczenie, $zapytanietxt);
					
					
					$stid = oci_parse($polaczenie, "Select * FROM ZAMOWIENIE_NAPRAWY WHERE ID_KLIENTA='$user'");
					$r = oci_execute($stid);
					
					
					// $wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
					
					// echo "znaleziono: ".$ile;
					if ($r){
						// $ile_wierszy = oci_num_rows($stid);
						
echo<<<END
<td width="100" align="center" bgcolor="e5e5e5">Numer Zlecenia</td>
<td width="100" align="center" bgcolor="e5e5e5">ID Klienta</td>
<td width="100" align="center" bgcolor="e5e5e5">ID Komputera</td>
<td width="100" align="center" bgcolor="e5e5e5">Status</td>
<td width="100" align="center" bgcolor="e5e5e5">Opis Usterki</td>
<td width="100" align="center" bgcolor="e5e5e5">Zobacz szczegóły</td>
</tr><tr>
END;

	// for ($i = 1; $i <= $ile_wierszy; $i++){ 
		while($wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)){
		
		$a_naprawa = $wiersz['ID_NAPRAWY'];
		$a_klient = $wiersz['ID_KLIENTA'];
		$a_komputer = $wiersz['ID_KOMPUTERA'];
		$a_opis = $wiersz['OPIS_USTERKI'];
		$a_status = $wiersz['STATUS'];	
		
echo<<<END
<td width="100" align="center">$a_naprawa</td>
<td width="100" align="center">$a_klient</td>
<td width="100" align="center">$a_komputer</td>
<td width="100" align="center">$a_status</td>
<td width="100" align="center">$a_opis</td>
<td width="100" align="center"><a href="szczegoly-zgloszenia.php?zgloszenie=$a_naprawa">Zobacz</a></td>
</tr><tr>
END;
			
	}
						}
						else{
						echo 'Przepraszamy wystąpił błąd serwera';
						echo '<br />spróbuj ponownie później';
						
						}
				
				oci_free_statement($stid);
				oci_close($polaczenie);

				}
			}
			catch( Exception $e){
				echo $e['message'];
			}
						
			
		?>
</tr></table>

	</body>
</html>