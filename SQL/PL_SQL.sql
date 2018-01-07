#=========================================================================================================
# procedura tworz¹ca fakturê       		 ========= utworz_fakture
#=========================================================================================================
DELIMITER //
CREATE PROCEDURE utworz_fakture(nID_NAPRAWY int(10))
begin
SET @n1 = (SELECT max(ID_FAKTURY) from PLATNOSCI) + 1; #Nadaj nowy numer fakturze
SET @n2 = (SELECT ID_KLIENTA from ZAMOWIENIA_NAPRAWY where ID_NAPRAWY = nID_NAPRAWY);
if (@n2 is NULL) then
	SELECT "ERROR" as "Status" , "0" as "ErrType";
else
	# Zsumuj wszystkie uslugi naprawy
	# Zsumuj wszystkie czesci zamienne
	# wstaw do bazy dane do faktury
	
end if;
end
//
DELIMITER ;

#====================
CALL utworz_fakture("61");
#====================

#=========================================================================================================
# procedura odœwierzaj¹ca dane w fakturze, je¿eli coœ zmieniliœmy i chcielibyœmy poprawiæ wartoœci	 ========= odswierz_fakture
#=========================================================================================================
DELIMITER //
CREATE PROCEDURE odswierz_fakture(nID_NAPRAWY int(10))
begin
SET @n1 = (SELECT max(ID_FAKTURY) from PLATNOSCI) + 1; #Nadaj nowy numer fakturze
SET @n2 = (SELECT ID_KLIENTA from ZAMOWIENIA_NAPRAWY where ID_NAPRAWY = nID_NAPRAWY);
if (@n2 is NULL) then
	SELECT "ERROR" as "Status" , "0" as "ErrType";
else
	# Zsumuj wszystkie uslugi naprawy
	# Zsumuj wszystkie czesci zamienne
	# wstaw do bazy dane do faktury
	
end if;
end
//
DELIMITER ;

#====================
CALL odswierz_fakture("61");
#====================



