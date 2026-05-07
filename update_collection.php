<?php
	include "DB.php";
	include "rate_limit.php";

	if (!isset($_SESSION['login'])) {
		exit();
	}

	$id = intval($_POST['id']);
	$name = trim($_POST['name']);
	$public = intval($_POST['public']);
	$cards = json_decode($_POST['cards'], true);

	if ($id <= 0) exit();

	if (mb_strlen($name) < 1 || mb_strlen($name) > 100) exit();

	if (!in_array($public, [0,1])) exit();

	$stmt = mysqli_prepare($conn, "UPDATE collections SET Name=?, Public=? WHERE Collection_ID=?");
	mysqli_stmt_bind_param($stmt, "sii", $name, $public, $id);
	mysqli_stmt_execute($stmt);

	$stmt = mysqli_prepare($conn, "DELETE FROM collection_cards WHERE Collection_ID=?");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);

	if (is_array($cards)) {
		$stmt = mysqli_prepare($conn, "INSERT INTO collection_cards (Collection_ID, Card_ID) VALUES (?, ?)");

		foreach($cards as $c){
			$cid = intval($c['Card_ID']);
			if ($cid > 0) {
				mysqli_stmt_bind_param($stmt, "ii", $id, $cid);
				mysqli_stmt_execute($stmt);
			}
		}
	}
?>