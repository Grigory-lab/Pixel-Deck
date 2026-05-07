<?php
	include "DB.php";

	if(!isset($_SESSION['login'])) exit;

	$login = $_SESSION['login'];
	$name = $_POST['name'];

	$stmt = mysqli_prepare($conn, "SELECT User_ID FROM users WHERE Login=?");
	mysqli_stmt_bind_param($stmt,"s",$login);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);
	$user = mysqli_fetch_assoc($res);

	$user_id = $user['User_ID'];

	$stmt = mysqli_prepare($conn, "
		INSERT INTO collections (Name, Public, User_ID)
		VALUES (?, 0, ?)
	");
	mysqli_stmt_bind_param($stmt,"si",$name,$user_id);
	mysqli_stmt_execute($stmt);