<?php
    include "DB.php";
    include "rate_limit.php";

    $error = 0;

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if(strlen($login) > 20){
            $error = 1;
        } elseif(strlen($password) > 50){
            $error = 1;
        } else {

            $sql = "SELECT * FROM users WHERE Login = ?";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "s", $login);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) == 1) {
                $user = mysqli_fetch_assoc($result);

                if (password_verify($password, $user['Password'])) {
                    $_SESSION['login'] = $user['Login'];

                    header("Location: account.php");
                    exit();
                } else {
                    $error = 1;
                }
            } else {
                $error = 1;
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Авторизация</title>
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
                <img src="img/logo.png" alt="" height="50px"> Pixel Deck
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
        <h1>Авторизация</h1>
        <div class="form-wrapper">
            <form method="post" class="auth-form">
                <input type="text" name="login" placeholder="Логин" maxlength="20" required>
                <input type="password" name="password" placeholder="Пароль" maxlength="50" required>

                <button type="submit">Войти</button>
            </form>
        </div>
    </main>

    <script>
    let error = <?php echo json_encode($error); ?>;

    if (error === 1) {
        alert("Введите корректные логин и пароль");
    }
    </script>
</body>
</html>