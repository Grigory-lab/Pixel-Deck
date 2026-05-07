<?php
	include "DB.php";

	$id = $_GET['id'];

	$stmt = mysqli_prepare($conn, "
		SELECT c.Card_ID, c.Name, c.Description, c.Type, c.Image, u.Login
		FROM collection_cards cc
		JOIN cards c ON c.Card_ID = cc.Card_ID
		JOIN users u ON c.User_ID = u.User_ID
		WHERE cc.Collection_ID=?
	");

	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);

	$data = [];
	while($row = mysqli_fetch_assoc($res)) $data[] = $row;

	echo json_encode($data);