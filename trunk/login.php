<?php
	$username = $_POST['username'];
	$password = $_POST['pwd'];

	$rslt = "./iudis login $username $password";
/*	if ( shell_exec($rslt) == -1 ) {
		header('Location:login.html?err');die();
	}

	session_start();
	$_SESSION['username'] = $username;

	echo "Welcome " . $_SESSION['username'];
*/	exec($rslt,$rslt);
	if ( $rslt[0] == -1 ) {
		header('Location:login.html?err');die();
	}
	session_start();
	$_SESSION['username'] = $username;

	echo "Welcome " . $_SESSION['username'];

?>
