<?php
	include "DB.php";

	if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'admin') {
		header("Location: index.php");
		exit();
	}

	$table = $_POST['table'] ?? '';
	$primary = $_POST['primary'] ?? '';

	$tables = [];
	$res = mysqli_query($conn, "SHOW TABLES");
	while($row = mysqli_fetch_array($res)){
		$tables[] = $row[0];
	}

	if(!in_array($table, $tables)){
		die("Ошибка таблицы");
	}

	$columns = [];
	$res = mysqli_query($conn, "SHOW COLUMNS FROM `$table`");
	while($row = mysqli_fetch_assoc($res)){
		$columns[] = $row['Field'];
	}

	if(!in_array($primary, $columns)){
		die("Ошибка primary");
	}

	// Удаление
	if(isset($_POST['delete'])){
		foreach($_POST['delete'] as $id){

			$stmt = mysqli_prepare($conn, "DELETE FROM `$table` WHERE `$primary`=?");
			mysqli_stmt_bind_param($stmt, "s", $id);
			mysqli_stmt_execute($stmt);
		}
	}

	// Обновление
	if(isset($_POST['data'])){
		foreach($_POST['data'] as $i => $row){

			$pk = $_POST['pk'][$i] ?? '';

			$sets = [];
			$values = [];

			foreach($row as $col=>$val){

				if(!in_array($col, $columns)) continue;

				$sets[] = "`$col`=?";
				$values[] = $val;
			}

			if(count($sets) === 0) continue;

			$sql = "UPDATE `$table` SET ".implode(",",$sets)." WHERE `$primary`=?";
			$stmt = mysqli_prepare($conn, $sql);

			$types = str_repeat("s", count($values) + 1);
			$values[] = $pk;

			mysqli_stmt_bind_param($stmt, $types, ...$values);
			mysqli_stmt_execute($stmt);
		}
	}

	// Добавление
	if(isset($_POST['new'])){
		foreach($_POST['new'] as $row){

			$cols = [];
			$placeholders = [];
			$values = [];

			foreach($row as $col=>$val){

				if(!in_array($col, $columns)) continue;

				$cols[] = "`$col`";
				$placeholders[] = "?";
				$values[] = $val;
			}

			if(count($cols) === 0) continue;

			$sql = "INSERT INTO `$table` (".implode(",",$cols).") VALUES (".implode(",",$placeholders).")";
			$stmt = mysqli_prepare($conn, $sql);

			$types = str_repeat("s", count($values));
			mysqli_stmt_bind_param($stmt, $types, ...$values);
			mysqli_stmt_execute($stmt);
		}
	}

	header("Location: admin.php?table=".$table);