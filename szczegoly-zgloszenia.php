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
	<br />
	<a href="zobacz-zgloszenia.php">[ Powrót do listy Twoich zgłoszeń ]</a><br /><br /><br />
        <?php
		
			$user = $_SESSION['id'];
			$zgloszenie = $_GET['zgloszenie'];
				
			$min = 1;
			$max = 2147483647; 

			if (filter_var($zgloszenie, FILTER_VALIDATE_INT, array("options" => array("min_range"=>$min, "max_range"=>$max))) === false) {
				echo("NIEPOPRAWNY NUMER ZGŁOSZENIA: ");
				echo $zgloszenie;
				exit();
			}		
		
            ini_set("display_errors", 0);
            require_once "setup-connect.php";
			try{
				$polaczenie = oci_connect($db_user,$db_password,$db_host, $db_lang);
				
				if (!$polaczenie){
					$m = oci_error();
					echo $m['message'], "\n";
				}
				else{
			
					$stid = oci_parse($polaczenie, "Select * FROM ZAMOWIENIE_NAPRAWY WHERE ID_KLIENTA='$user' AND ID_NAPRAWY='$zgloszenie'");
					$r = oci_execute($stid);
					
					
					if ($r){
// to zamowienie nie nalezy do tego klienta
						$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
						if($wiersz['ID_KLIENTA'] != $user){
							echo "Zamówienie nr ".$zgloszenie." nie należy do Ciebie";
							exit();
						}
// To zamówienie należy do tego klienta
						else{
							echo "Szczegóły Twojego zamówienia nr ".$zgloszenie." :<br />";
// szczegoly zamowienia - opis usterki
echo '<table width="1000" border="1" bordercolor="#d5d5d5"  cellpadding="0" cellspacing="0">
        <tr>';							
echo<<<END
<td width="100" align="center" bgcolor="e5e5e5">Numer Zlecenia</td>
<td width="100" align="center" bgcolor="e5e5e5">ID Klienta</td>
<td width="100" align="center" bgcolor="e5e5e5">ID Komputera</td>
<td width="100" align="center" bgcolor="e5e5e5">Status</td>
<td width="100" align="center" bgcolor="e5e5e5">Opis Usterki</td>
</tr><tr>
END;

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
</tr><tr>
END;
echo '</tr></table>';
echo<<<END
<br />Opis statusów:<br />
nowy - Zgłoszenie utworzone przez użytkownika, oczekiwanie na dostawę urządzenia<br />
przyjęty - Sprzęt został dostarczony, wkrótce rozpocznie się analiza uszkodzeń<br />
zdiagnozowany - Sprzęt został zdiagnozowany i rozpoczeła się jego naprawa, postepy możesz śledzić w szczegółach<br />
naprawiony - Sprzęt został naprawiony, oczekujemy na wpłatę kwoty podanej w szczegółach<br />
zakończony - Otrzymalismy Twoją wpłatę, komputer został wysłany lub oczekuje na odbiór, jeżeli dostarczyłeś go osobiście<br />					
END;
// jezeli status = nowy, to nalezy wyswietlic info o adresie do wysylki komputeraz
	echo '<span style="color:red"><b><br />Jeżeli twój komputer posiada status nowy, powinieneś go dostarczyć do naszego serwisu, osobiście lub wysłać na adres:</b></span><br /><br />';
	echo 'SBD - Serwis Komputerowy<br />';
	echo 'ul. Warszawska 24<br />';
	echo '31-155 Kraków<br />';


						}
					}
					else{
						echo 'Przepraszamy wystąpił błąd serwera';
						echo '<br />Nie można pobrać informacji o zamówieniu';
						echo '<br />spróbuj ponownie później';
					}
					oci_free_statement($stid);
// szczegoly komputera
					echo "<br /><br />";
					$stid = oci_parse($polaczenie, "Select * FROM KOMPUTERY WHERE ID_KOMPUTERA='$a_komputer'");
					$r = oci_execute($stid);
					
					if ($r){
						$wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC);
						
						echo "Szczegóły komputera nr ".$a_komputer." :<br />";
echo '<table width="1000" border="1" bordercolor="#d5d5d5"  cellpadding="0" cellspacing="0">
        <tr>';							
echo<<<END
<td width="100" align="center" bgcolor="e5e5e5">ID Komputera</td>
<td width="100" align="center" bgcolor="e5e5e5">Producent</td>
<td width="100" align="center" bgcolor="e5e5e5">Rok Produkcji</td>
</tr><tr>
END;
		$a_komp_producent = $wiersz['PRODUCENT'];
		$a_komp_rok = $wiersz['ROK_PRODUKCJI'];
		
echo<<<END
<td width="100" align="center">$a_komputer</td>
<td width="100" align="center">$a_komp_producent</td>
<td width="100" align="center">$a_komp_rok</td>
</tr><tr>
END;
echo '</tr></table>';
						
					}
					else{
						echo 'Przepraszamy wystąpił błąd serwera';
						echo '<br />Nie można pobrać informacji o komputerze';
						echo '<br />spróbuj ponownie później';
					}
					oci_free_statement($stid);
// szczegoly wykonanych prac + cennik
					echo "<br /><br />";
					$stid = oci_parse($polaczenie, "SELECT * FROM PRACE_NAPRAWCZE p LEFT JOIN CENNIK c ON p.ID_USLUGI = c. ID_USLUGI WHERE p.ID_NAPRAWY = '$a_naprawa'");
					$r = oci_execute($stid);
					
					if ($r){
						$suma = 0;
						
						echo "Szczegóły wykonanych prac dla komputera ".$a_komputer." :<br />";
echo '<table width="1000" border="1" bordercolor="#d5d5d5"  cellpadding="0" cellspacing="0">
        <tr>';							
echo<<<END
<td width="100" align="center" bgcolor="e5e5e5">Numer usługi</td>
<td width="100" align="center" bgcolor="e5e5e5">Nazwa usługi</td>
<td width="100" align="center" bgcolor="e5e5e5">Cena</td>
<td width="100" align="center" bgcolor="e5e5e5">Numer pracownika</td>
</tr><tr>
END;
		while($wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)){
		$a_iduslugi = $wiersz['ID_USLUGI'];
		$a_idpracownika = $wiersz['ID_PRACOWNIKA'];
		$a_nazwa_uslugi = $wiersz['NAZWA_USLUGI'];
		$a_cena = $wiersz['CENA'];
		$a_suma = $a_suma + $a_cena;
		
echo<<<END
<td width="100" align="center">$a_iduslugi</td>
<td width="100" align="center">$a_nazwa_uslugi</td>
<td width="100" align="center">$a_cena</td>
<td width="100" align="center">$a_idpracownika</td>
</tr><tr>
END;
		}
echo<<<END
<td width="100" align="center">SUMA</td>
<td width="100" align="center">-</td>
<td width="100" align="center">$a_suma</td>
<td width="100" align="center">-</td>
</tr><tr>
END;
echo '</tr></table><br />';

						
					}
					else{
						echo 'Przepraszamy wystąpił błąd serwera';
						echo '<br />Nie można pobrać informacji o wykonanych usługach';
						echo '<br />spróbuj ponownie później';
					}
					oci_free_statement($stid);
// szczegoly platnosci ile do zaplaty, ile zaplacono, dane do przelewu					
					echo "<br /><br />";
					$stid = oci_parse($polaczenie, "SELECT * FROM PLATNOSCI WHERE ID_NAPRAWY = '$a_naprawa'");
					$r = oci_execute($stid);
					
					if ($r){
												
						echo "Szczegóły Płatności:<br />";
echo '<table width="1000" border="1" bordercolor="#d5d5d5"  cellpadding="0" cellspacing="0">
        <tr>';							
echo<<<END
<td width="100" align="center" bgcolor="e5e5e5">Numer Faktury</td>
<td width="100" align="center" bgcolor="e5e5e5">Do zapłaty</td>
<td width="100" align="center" bgcolor="e5e5e5">Zapłacono</td>
</tr><tr>
END;
		while($wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)){
		$a_faktura = $wiersz['ID_FAKTURY'];
		$a_do_zaplaty = $wiersz['DO_ZAPLATY'];
		$a_zaplacono = $wiersz['ZAPLACONO'];
		
echo<<<END
<td width="100" align="center">$a_faktura</td>
<td width="100" align="center">$a_do_zaplaty</td>
<td width="100" align="center">$a_zaplacono</td>
</tr><tr>
END;
		}
echo '</tr></table>';
						
					}
					else{
						echo 'Przepraszamy wystąpił błąd serwera';
						echo '<br />Nie można pobrać informacji o płatności';
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

	</body>
</html>