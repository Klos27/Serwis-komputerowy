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
				$polaczenie = oci_connect($db_user, $db_password, $db_host, $db_lang);
				
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
<td width="100" align="center" bgcolor="e5e5e5">Data dodania</td>
<td width="100" align="center" bgcolor="e5e5e5">Data zakończenia</td>
<td width="500" align="center" bgcolor="e5e5e5">Opis Usterki</td>
</tr><tr>
END;

		$a_naprawa = $wiersz['ID_NAPRAWY'];
		$a_klient = $wiersz['ID_KLIENTA'];
		$a_komputer = $wiersz['ID_KOMPUTERA'];
		$a_opis = $wiersz['OPIS_USTERKI'];
		$a_status = $wiersz['STATUS'];
		$a_data_start = $wiersz['DATA_ROZPOCZECIA'];	
		$a_data_koniec = $wiersz['DATA_ZAKONCZENIA'];		
		
echo<<<END
<td width="100" align="center">$a_naprawa</td>
<td width="100" align="center">$a_klient</td>
<td width="100" align="center">$a_komputer</td>
<td width="100" align="center">$a_status</td>
<td width="100" align="center">$a_data_start</td>
<td width="100" align="center">$a_data_koniec</td>
<td width="500" align="center">$a_opis</td>
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
	echo 'Nr. rachunku ING 17 1111 1111 2222 2222 2222 2222';


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
						$a_suma = 0;
						
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
		$a_cena = $wiersz['CENA_PRACY'];
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
					
				// szczegoly zakupionych części					
					$stid = oci_parse($polaczenie, "SELECT * FROM PRACE_NAPRAWCZE p LEFT JOIN PRACE_NAPRAWCZE_CZESCI pc ON p.ID_PRACY = pc.ID_PRACY LEFT JOIN CZESCI_ZAMIENNE c ON pc.ID_CZESCI = c. ID_CZESCI LEFT JOIN PRODUCENCI pr ON c.ID_PRODUCENTA = pr.ID_PRODUCENTA LEFT JOIN KATEGORIE k ON c.ID_KATEGORII = k.ID_KATEGORII WHERE p.ID_NAPRAWY = '$a_naprawa'");
					$r = oci_execute($stid);
					
					if ($r){
						$a_suma_zakupu = 0;
						
						echo "Szczegóły wymienionych części w komputerze nr ".$a_komputer." :<br />";
echo '<table width="1000" border="1" bordercolor="#d5d5d5"  cellpadding="0" cellspacing="0">
        <tr>';							
echo<<<END
<td width="100" align="center" bgcolor="e5e5e5">Numer Części</td>
<td width="100" align="center" bgcolor="e5e5e5">Nazwa modelu części</td>
<td width="100" align="center" bgcolor="e5e5e5">Nazwa kategorii</td>
<td width="100" align="center" bgcolor="e5e5e5">Nazwa producenta</td>
<td width="100" align="center" bgcolor="e5e5e5">Cena zakupu</td>
</tr><tr>
END;
		while($wiersz = oci_fetch_array($stid, OCI_RETURN_NULLS+OCI_ASSOC)){
		$a_idczesci = $wiersz['ID_CZESCI'];
		$a_nazwa_modelu = $wiersz['MODEL'];
		$a_nazwa_kategorii = $wiersz['NAZWA_KATEGORII'];
		$a_nazwa_producenta = $wiersz['NAZWA_PRODUCENTA'];
		$a_cena_zakupu = $wiersz['CENA_ZAKUPU'];
		$a_suma_zakupu = $a_suma_zakupu + $a_cena_zakupu;
		
echo<<<END
<td width="100" align="center">$a_idczesci</td>
<td width="100" align="center">$a_nazwa_modelu</td>
<td width="100" align="center">$a_nazwa_kategorii</td>
<td width="100" align="center">$a_nazwa_producenta</td>
<td width="100" align="center">$a_cena_zakupu</td>
</tr><tr>
END;
		}
echo<<<END
<td width="100" align="center">SUMA</td>
<td width="100" align="center">-</td>
<td width="100" align="center">-</td>
<td width="100" align="center">-</td>
<td width="100" align="center">$a_suma_zakupu</td>
</tr><tr>
END;
echo '</tr></table><br />';

						
					}
					else{
						echo 'Przepraszamy wystąpił błąd serwera';
						echo '<br />Nie można pobrać informacji o wymienionych częściach';
						echo '<br />spróbuj ponownie później';
					}
					oci_free_statement($stid);				

					$a_update_faktura = false;			
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
		$a_do_zaplaty_suma = $a_suma + $a_suma_zakupu;
		if(($a_faktura != 0) && ($a_do_zaplaty != $a_do_zaplaty_suma))	// jeżeli faktura istnieje
			$a_update_faktura = true;	// jeżeli suma w fakturze jest inna od aktualnej to zaktualizuj fakture
		else
			$a_update_faktura = false;
echo<<<END
<td width="100" align="center">$a_faktura</td>
<td width="100" align="center">$a_do_zaplaty_suma</td>
<td width="100" align="center">$a_zaplacono</td>
</tr><tr>
END;
		}
echo '</tr></table>';
	echo '<span style="color:red"><b><br />Jeżeli twój komputer posiada status naprawiony, oczekujemy na Twoją wpłatę na nasze konto:</b></span><br /><br />';
	echo 'SBD - Serwis Komputerowy<br />';
	echo 'ul. Warszawska 24<br />';
	echo '31-155 Kraków<br />';
	echo 'Nr. rachunku ING 17 1111 1111 2222 2222 2222 2222';	
						
					}
					else{
						echo 'Przepraszamy wystąpił błąd serwera';
						echo '<br />Nie można pobrać informacji o płatności';
						echo '<br />spróbuj ponownie później';
					}
					oci_free_statement($stid);					

					// zaktualizuj fakture
					if($a_update_faktura == true){
						$stid = oci_parse($polaczenie, "UPDATE PLATNOSCI SET DO_ZAPLATY = '$a_do_zaplaty_suma' WHERE ID_FAKTURY = '$a_faktura'");
						$r = oci_execute($stid);
						
						if ($r)
							echo "<br /><br /> Dane faktury zostały zaktualizowane<br />";
						else
							echo '<span style="color:red"><b><br /><br />Nie udało zaktualizować się danych faktury</b></span><br /><br />';
						oci_free_statement($stid);
					}
					
					oci_close($polaczenie);
				}
			}
			catch( Exception $e){
				echo $e['message'];
			}
	
			
		?>
<br /><br /><br /><br /><br /><br />
	</body>
</html>