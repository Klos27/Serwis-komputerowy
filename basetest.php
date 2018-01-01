<html>
<head><title>Oracle Database Test</title></head>
<body>
	<a href="index.php"><img src="img/Logo.png" alt="Strona Główna"/></a><br />
	<?php 
		require_once "connect.php";
		
		$conn=oci_connect($db_user,$db_password,$host);
		If (!$conn)
			echo 'Failed to connect to Oracle';
		else
			echo 'Succesfully connected with Oracle DB';
		// echo '<br />';
		// echo 'DB name: '.$db_name;
		// echo '<br />';
		// echo 'DB user: '.$db_user;
		// echo '<br />';
		// echo 'DB host: '.$host;
		// echo '<br />';
		// echo 'DB pass: '.$db_password;
		
	 
		oci_close($conn);
	?>
 
</body>
</html>