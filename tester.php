<?php
	session_start();
	
	if(isset($_POST['line'])){ 
		
		
		// FORMAT TEST
		$line = $_POST['line'];
		$line = htmlentities($line, ENT_QUOTES, "UTF-8");
		
			$woj = $_POST['wojewodztwo'];
		
			
		
		$_SESSION['wojewodztwo'] = $woj;
		echo $_POST['poletekstowe'];
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
	<a href="index.php"><img src="img/Logo.png" alt="Strona Główna"/></a><br />
	
	<form action="tester.php" method="post" accept-charset="UTF-8">
	Text: <br /> <input type="text" name="line" /> <br />

	<label>Województwo:</label>
		<select name="wojewodztwo" >
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "dolnośląskie")) echo 'selected'; ?> value="dolnośląskie" >dolnośląskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "kujawsko-pomorskie")) echo 'selected'; ?> value="kujawsko-pomorskie" >kujawsko-pomorskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "lubelskie")) echo 'selected'; ?> value="lubelskie" >lubelskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "lubuskie")) echo 'selected'; ?> value="lubuskie">lubuskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "łódzkie")) echo 'selected'; ?> value="łódzkie" >łódzkie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "małopolskie")) echo 'selected'; ?> value="małopolskie" >małopolskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "mazowieckie")) echo 'selected'; ?> value="mazowieckie" >mazowieckie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "opolskie")) echo 'selected'; ?> value="opolskie" >opolskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "podkarpackie")) echo 'selected'; ?> value="podkarpackie" >podkarpackie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "podlaskie")) echo 'selected'; ?> value="podlaskie" >podlaskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "pomorskie")) echo 'selected'; ?> value="pomorskie" >pomorskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "śląskie")) echo 'selected'; ?> value="śląskie" >śląskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "świętokrzyskie")) echo 'selected'; ?> value="świętokrzyskie" >świętokrzyskie
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "warmińsko-mazurskie")) echo 'selected'; ?> value="warmińsko-mazurskie" >warmińsko-mazurskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "wielkopolskie")) echo 'selected'; ?> value="wielkopolskie" >wielkopolskie</option>
			<option <?php if((isset($_SESSION['wojewodztwo'])) && ($_SESSION['wojewodztwo'] == "zachodniopomorskie")) echo 'selected'; ?> value="zachodniopomorskie" >zachodniopomorskie</option>
		</select>
	<br />
	<input type="text" name="pole" style="width: 300px; height: 100px; resize:both;"size="150"/><br />
	<textarea id="text" name="poletekstowe" rows="15" cols="70" class="required" placeholder="Tutaj opisz usterkę max 4000znaków" maxlength="4000"></textarea><br />
	<input type="submit" value="Test" />
	
	</form>
	
	<br /><br />

		

</body>
</html>