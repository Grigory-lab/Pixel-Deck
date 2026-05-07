<?php
	include "DB.php";

	if(!isset($_SESSION['login'])) exit;

	$id = $_POST['id'];

	$stmt = mysqli_prepare($conn, "SELECT Image FROM cards WHERE Card_ID=?");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);
	$card = mysqli_fetch_assoc($res);

	if($card){
		if(file_exists($card['Image'])){
			unlink($card['Image']);
		}

		$stmt = mysqli_prepare($conn, "DELETE FROM cards WHERE Card_ID=?");
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
	}