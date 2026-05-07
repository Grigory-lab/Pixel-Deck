<?php
	include "DB.php";

	if (!isset($_SESSION['login'])) {
		header("Location: account.php");
		exit;
	}

	$login = $_SESSION['login'];

	$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE Login=?");
	mysqli_stmt_bind_param($stmt, "s", $login);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);
	$user = mysqli_fetch_assoc($res);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Изменение данных аккаунта</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="account.css">
</head>
<body class="account-bg">
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

<main class="account">
	<h4 class="title-main">Изменение аккаунта</h4>
	<div class="account-form">
		<label>Логин</label>
		<input id="login" value="<?= htmlspecialchars($user['Login'], ENT_QUOTES) ?>">

		<label>Email</label>
		<input id="email" type="email" required value="<?= htmlspecialchars($user['Mail'], ENT_QUOTES) ?>">

		<label>Страна</label>
		<div class="select-wrapper">
            <select id="country" required>
                <option value="Россия" <?= $user['Country']=="Россия"?'selected':'' ?>>Россия</option>
                <option value="Казахстан" <?= $user['Country']=="Казахстан"?'selected':'' ?>>Казахстан</option>
                <option value="Беларусь" <?= $user['Country']=="Беларусь"?'selected':'' ?>>Беларусь</option>
                <option value="Германия" <?= $user['Country']=="Германия"?'selected':'' ?>>Германия</option>
                <option value="Франция" <?= $user['Country']=="Франция"?'selected':'' ?>>Франция</option>
                <option value="США" <?= $user['Country']=="США"?'selected':'' ?>>США</option>
                <option value="Китай" <?= $user['Country']=="Китай"?'selected':'' ?>>Китай</option>
                <option value="Другое" <?= $user['Country']=="Другое"?'selected':'' ?>>Другое</option>
            </select>
        </div>
		<button class="modal-btn" onclick="save()">Сохранить</button>
		<a href="account.php" class="back-btn">Назад</a>
	</div>
</main>

<script>
function validate(login,email){
	let loginReg = /^[a-zA-Z0-9_]{1,20}$/;
	let emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

	if(!loginReg.test(login)){
		alert("Неверный логин");
		return false;
	}

	if(!emailReg.test(email)){
		alert("Неверный email");
		return false;
	}
	return true;
}

function save(){
	let login = document.getElementById("login").value.trim();
	let email = document.getElementById("email").value.trim();
	let country = document.getElementById("country").value;

	if(!validate(login,email)) return;

	let data = new URLSearchParams();
	data.append("login", login);
	data.append("email", email);
	data.append("country", country);

	fetch("update_user.php", { method: "POST", body: data })
	.then(() => {
		alert("Данные сохранены");
		window.location.href = "account.php";
	});
}
</script>

</body>
</html>