<?php
	include "DB.php";
	include "rate_limit.php";

	$error = 0;
	$step = 1;

	if (!isset($_SESSION)) session_start();

	if (isset($_SESSION['reg_pending'])) {
		$step = 2;
	}

	if ($_SERVER["REQUEST_METHOD"] == "POST") {

		if (isset($_POST['exit'])) {

			unset($_SESSION['reg_pending']);
			unset($_SESSION['reg_code']);
			header("Location: register.php");
			exit();

		} elseif (isset($_POST['verify_code'])) {

			$code = trim($_POST['code'] ?? '');

			if ($code == $_SESSION['reg_code']) {

				$data = $_SESSION['reg_pending'];

				$sql = "INSERT INTO users (Login, Password, Mail, Country)
						VALUES (?, ?, ?, ?)";
				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, "ssss",
					$data['login'],
					$data['password'],
					$data['mail'],
					$data['country']
				);
				mysqli_stmt_execute($stmt);

				$_SESSION['login'] = $data['login'];

				unset($_SESSION['reg_pending']);
				unset($_SESSION['reg_code']);

				header("Location: account.php");
				exit();

			} else {
				$error = 4;
				$step = 2;
			}

		} elseif (isset($_POST['resend'])) {
			$code = rand(100000, 999999);
			$_SESSION['reg_code'] = $code;

			mail($_SESSION['reg_pending']['mail'], "Код подтверждения", "Ваш код: $code");

			header("Location: register.php");
			exit();

		} else {
			$login = trim($_POST['login'] ?? '');
			$password = trim($_POST['password'] ?? '');
			$mail = trim($_POST['mail'] ?? '');
			$country = trim($_POST['country'] ?? '');

			if(strlen($login) > 20){
				$error = 1;
			} elseif(strlen($password) > 50){
				$error = 1;
			} elseif(!filter_var($mail, FILTER_VALIDATE_EMAIL)){
				$error = 1;
			} else {

				$sql = "SELECT Login, Mail FROM users WHERE Login = ? OR Mail = ?";
				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, "ss", $login, $mail);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);

				if (mysqli_num_rows($result) == 0) {

					$hashed_password = password_hash($password, PASSWORD_DEFAULT);

					$_SESSION['reg_pending'] = [
						'login' => $login,
						'password' => $hashed_password,
						'mail' => $mail,
						'country' => $country
					];

					$code = rand(100000, 999999);
					$_SESSION['reg_code'] = $code;

					mail($mail, "Код подтверждения", "Ваш код: $code");

					header("Location: register.php");
					exit();

				} else {
					$row = mysqli_fetch_assoc($result);

					if ($row['Login'] === $login) {
						$error = 2;
					} elseif ($row['Mail'] === $mail) {
						$error = 3;
					}
				}
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Регистрация</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="forms.css">
	<meta name="description" content="Pixel Deck - большая онлайн-галерея пиксельных картинок. Здесь каждый может поделиться своим творчеством со всем миром.">
    <meta name="keywords" content="картинки, пиксель арт, фотографии, пиксельный стиль, творчество, креатив">
    <meta name="robots" content="index, follow">
</head>
<body class="auth-bg">
<header class="header">
	<div class="header__content">
		<div class="logo">
			<img src="img/logo.png" height="50px"> Pixel Deck
		</div>
		<nav class="menu">
			<a href="index.php"><img src="img/gallery_icon.png">Галерея</a>
			<a href="upload.php"><img src="img/upload_icon.png">Создать карту</a>
			<a href="collections.php"><img src="img/collection_icon.png">Коллекции</a>
			<a href="account.php"><img src="img/user_icon.png">Аккаунт</a>
		</nav>
	</div>
</header>

<main class="auth-container">
	<h1>Регистрация</h1>
	<div class="form-wrapper">

		<?php if ($step == 1): ?>

		<form method="post" class="auth-form">
			<input type="text" name="login" placeholder="Логин" maxlength="20" required>
			<input type="password" name="password" placeholder="Пароль" maxlength="50" required>
			<input type="email" name="mail" placeholder="Эл. почта" required>

			<div class="select-wrapper">
				<select name="country" required>
					<option value="">Страна проживания</option>
					<option value="Россия">Россия</option>
					<option value="Казахстан">Казахстан</option>
					<option value="Беларусь">Беларусь</option>
					<option value="Германия">Германия</option>
					<option value="Франция">Франция</option>
					<option value="США">США</option>
					<option value="Китай">Китай</option>
					<option value="Другое">Другое</option>
				</select>
			</div>

			<button type="submit">Зарегистрироваться</button>
		</form>

		<?php else: ?>

		<form method="post" class="auth-form">

			<input type="text" name="code" placeholder="Введите код" required>

			<button type="submit" name="verify_code" class="rc">Подтвердить</button>
			<button type="submit" name="resend" formnovalidate class="rc">Отправить код ещё раз</button>
			<button type="submit" name="exit" formnovalidate>Назад</button>

		</form>

		<?php endif; ?>

	</div>
</main>

<script>
	let error = <?php echo json_encode($error); ?>;

	if (error === 1) alert("Некорректные данные");
	if (error === 2) alert("Пользователь с таким логином уже существует");
	if (error === 3) alert("Пользователь с такой почтой уже существует");
	if (error === 4) alert("Неверный код подтверждения");
</script>

</body>
</html>