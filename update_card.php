<?php
	include "DB.php";
	include "rate_limit.php";

	if (!isset($_SESSION['login'])) exit;

	$id = $_POST['id'];
	$name = $_POST['name'];
	$type = $_POST['type'];
	$desc = $_POST['desc'];

	$stmt = mysqli_prepare($conn, "
		UPDATE cards 
		SET Name=?, Type=?, Description=? 
		WHERE Card_ID=?
	");

	mysqli_stmt_bind_param($stmt, "sssi", $name, $type, $desc, $id);
	mysqli_stmt_execute($stmt);

	echo "ok";
?>