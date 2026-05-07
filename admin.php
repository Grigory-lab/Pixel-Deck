<?php
	include "DB.php";
	include "rate_limit.php";

	if (!isset($_SESSION['login']) || $_SESSION['login'] !== 'admin') {
		header("Location: index.php");
		exit();
	}

	$tables = [];
	$res = mysqli_query($conn, "SHOW TABLES");
	while($row = mysqli_fetch_array($res)){
		$tables[] = $row[0];
	}

	$current_table = $_GET['table'] ?? '';

	if($current_table && !in_array($current_table, $tables)){
		$current_table = '';
	}

	$data = [];
	$columns = [];
	$primary = null;

	if ($current_table) {

		$res = mysqli_query($conn, "SHOW COLUMNS FROM `$current_table`");
		while($row = mysqli_fetch_assoc($res)){
			$columns[] = $row['Field'];
			if($row['Key'] === 'PRI'){
				$primary = $row['Field'];
			}
		}

		$res = mysqli_query($conn, "SELECT * FROM `$current_table`");
		while($row = mysqli_fetch_assoc($res)){
			$data[] = $row;
		}
	}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Админ панель</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="admin.css">
</head>
<body>
<header class="header">
	<div class="header__content">
		<div class="logo">
			<img src="img/logo.png" height="50"> Pixel Deck
		</div>
		<nav class="menu">
			<a href="index.php"><img src="img/gallery_icon.png">Галерея</a>
			<a href="upload.php"><img src="img/upload_icon.png">Создать карту</a>
			<a href="collections.php"><img src="img/collection_icon.png">Коллекции</a>
			<a href="account.php"><img src="img/user_icon.png">Аккаунт</a>
		</nav>
	</div>
</header>
<main class="admin">
	<h4>Панель администратора</h4>
	<select onchange="location='admin.php?table='+this.value" required>
		<option value="">Выберите таблицу</option>
		<?php foreach($tables as $t): ?>
		<option value="<?= htmlspecialchars($t) ?>" <?= $t==$current_table?'selected':'' ?>>
			<?= htmlspecialchars($t) ?>
		</option>
		<?php endforeach; ?>
	</select>

	<?php if($current_table): ?>
	<p class="table-name">Таблица: <?= htmlspecialchars($current_table) ?></p>
	<form method="POST" action="admin_save.php">
		<input type="hidden" name="table" value="<?= htmlspecialchars($current_table) ?>">
		<input type="hidden" name="primary" value="<?= htmlspecialchars($primary) ?>">

		<div class="table">
			<div class="row upper">
				<?php foreach($columns as $col): ?>
					<div><?= htmlspecialchars($col) ?></div>
				<?php endforeach; ?>
			</div>

			<?php foreach($data as $i => $row): ?>
			<div class="row">
				<?php foreach($columns as $col): ?>
					<input name="data[<?= $i ?>][<?= htmlspecialchars($col) ?>]" value="<?= htmlspecialchars($row[$col]) ?>">
				<?php endforeach; ?>

				<button type="button" onclick="deleteRow(this)">
					<img src="img/Delete.png">
				</button>
				<input type="hidden" name="pk[<?= $i ?>]" value="<?= htmlspecialchars($row[$primary]) ?>">
			</div>
			<?php endforeach; ?>
			<div class="add" onclick="addRow()">+</div>
		</div>
		<button class="save">Сохранить</button>
	</form>
	<?php endif; ?>
</main>

<script>
let deleted = [];

function deleteRow(btn){
	let row = btn.parentElement;
	let pk = row.querySelector("input[type=hidden]");

	if(pk){
		deleted.push(pk.value);
	}
	row.remove();
}

document.querySelector("form")?.addEventListener("submit", function(){
	let form = this;

	deleted.forEach(id=>{
		let input = document.createElement("input");
		input.type = "hidden";
		input.name = "delete[]";
		input.value = id;
		form.appendChild(input);
	});
});

function addRow(){
	let table = document.querySelector(".table");
	let header = document.querySelectorAll(".row.upper div");

	let row = document.createElement("div");
	row.className = "row";

	let index = Date.now();

	header.forEach((h,i)=>{
		let input = document.createElement("input");
		input.name = `new[${index}][${h.innerText}]`;
		row.appendChild(input);
	});

	let btn = document.createElement("button");
	btn.type="button";
	btn.onclick=()=>row.remove();
	btn.innerHTML = '<img src="img/Delete.png">';
	row.appendChild(btn);

	table.insertBefore(row, document.querySelector(".add"));
}
</script>

</body>
</html>