<?php
	include "DB.php";
	include "rate_limit.php";

	if (!isset($_SESSION['login'])) exit;

	$oldLogin = $_SESSION['login'];

	$newLogin = trim($_POST['login']);
	$email = trim($_POST['email']);
	$country = trim($_POST['country']);

	$stmt = mysqli_prepare($conn, "
		UPDATE users SET Login=?, Mail=?, Country=? WHERE Login=?
	");

	mysqli_stmt_bind_param($stmt, "ssss", $newLogin, $email, $country, $oldLogin);
	mysqli_stmt_execute($stmt);

	$_SESSION['login'] = $newLogin;

	echo "ok";