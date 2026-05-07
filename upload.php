<?php
    include "DB.php";
    include "rate_limit.php";
    

    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $error = 0;
    $success = 0;

    $authorized = isset($_SESSION['login']);

    if ($authorized && $_SERVER["REQUEST_METHOD"] == "POST") {

	$name = trim($_POST['name']);
	$type = trim($_POST['type']);
	$description = trim($_POST['description']);
	$date = date("Y-m-d H:i:s");
	$login = $_SESSION['login'];

	if (mb_strlen($name) < 1 || mb_strlen($name) > 100) $error = 3;

	if (!in_array($type, ['Pa','Img','Pht'])) $error = 3;

	if (mb_strlen($description) > 1000) $error = 3;

	$stmtUser = mysqli_prepare($conn, "SELECT User_ID FROM users WHERE Login=?");
	mysqli_stmt_bind_param($stmtUser, "s", $login);
	mysqli_stmt_execute($stmtUser);
	$resUser = mysqli_stmt_get_result($stmtUser);
	$user = mysqli_fetch_assoc($resUser);

	$user_id = $user['User_ID'];

	if (isset($_FILES['image']) && $_FILES['image']['error'] === 0 && $error === 0) {

		if ($_FILES['image']['size'] > 60 * 1024 * 1024) {
			$error = 4;
		} else {

			$allowed = ['image/png','image/jpeg','image/jpg','image/webp'];

			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $_FILES['image']['tmp_name']);

			if (in_array($mime, $allowed)) {

				$ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
				$ext = strtolower($ext);

				if (!in_array($ext, ['png','jpg','jpeg','webp'])) exit();

				$filename = uniqid() . "." . $ext;
				$path = "uploads/" . $filename;

				move_uploaded_file($_FILES['image']['tmp_name'], $path);

				$sql = "INSERT INTO cards (Name, Image, Type, Description, Upload_date, User_ID)
						VALUES (?, ?, ?, ?, ?, ?)";

				$stmt = mysqli_prepare($conn, $sql);
				mysqli_stmt_bind_param($stmt, "sssssi", $name, $path, $type, $description, $date, $user_id);

				if (mysqli_stmt_execute($stmt)) {
					$success = 1;
				} else {
					$error = 2;
				}

			} else {
				$error = 1;
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
    <title>Создание карточки</title>
    <link rel="stylesheet" href="main.css">
    <link rel="stylesheet" href="upload.css">
    <meta name="description" content="Pixel Deck - большая онлайн-галерея пиксельных картинок. Здесь каждый может поделиться своим творчеством со всем миром.">
    <meta name="keywords" content="картинки, пиксель арт, фотографии, пиксельный стиль, творчество, креатив">
    <meta name="robots" content="index, follow">
</head>
<body class="upload-bg">
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

    <main class="upload-container">
        <?php if (!$authorized): ?>
            <div class="not-auth">
                <h5>Чтобы создавать карточки, войдите в аккаунт</h5>
                <a href="auth.php" class="action-btn">Авторизироваться</a>
                <a href="register.php" class="action-btn">Зарегистрироваться</a>
            </div>

        <?php else: ?>
            <h1>Создать новую карту</h1>
            <p class="subtitle">Поделитесь своим творчеством с другими.</p>
            <form method="post" enctype="multipart/form-data" class="upload-form">
                <label class="image-box">
                    <input type="file" name="image" id="imageInput" accept=".png,.jpg,.jpeg,.webp" required>
                    <img src="img/image.png" class="upload-icon" id="uploadIcon">
                    <span id="uploadText">Выберите файл картинки (PNG, JPG, JPEG, WEBP)</span>
                    <img id="previewImage" style="display:none; max-width:100%; max-height:100%;">
                </label>

                <div class="form-right">
                    <input type="text" name="name" placeholder="Введите название карточки" required>
                    <div class="custom-select">
                        <div class="select-selected">
                            <img src="img/pixel_icon.png">
                            <span>Пиксель арт (Pa)</span>
                        </div>

                        <div class="select-options">
                            <div data-value="Pa">
                                <img src="img/pixel_icon.png"> Пиксель арт (Pa)
                            </div>
                            <div data-value="Img">
                                <img src="img/painting_icon.png"> Рисунок (Img)
                            </div>
                            <div data-value="Pht">
                                <img src="img/camera_icon.png"> Фото (Pht)
                            </div>
                        </div>

                        <input type="hidden" name="type" value="Pa">
                    </div>

                    <textarea name="description" placeholder="Введите описание карточки..."></textarea>
                    <button type="submit">Опубликовать карточку</button>
                </div>
            </form>

        <?php endif; ?>
    </main>

    <script>
        let error = <?php echo json_encode($error); ?>;
        let success = <?php echo json_encode($success); ?>;

        if (error === 1) alert("Допустимы только PNG, JPG, JPEG, WEBP");
        if (error === 2) alert("Ошибка при сохранении в БД");
        if (error === 3) alert("Некорректные данные (название, тип или описание)");
        if (error === 4) alert("Файл слишком большой (макс. 60MB)");

        if (success === 1) {
            alert("Карточка успешно опубликована");
            window.location.href = "upload.php";
        }
    </script>

    <script>
        const selected = document.querySelector(".select-selected");
        const options = document.querySelector(".select-options");
        const hiddenInput = document.querySelector("input[name='type']");

        selected.addEventListener("click", () => {
            options.style.display = options.style.display === "block" ? "none" : "block";
        });

        document.querySelectorAll(".select-options div").forEach(option => {
            option.addEventListener("click", () => {
                const img = option.querySelector("img").src;
                const text = option.textContent;

                selected.innerHTML = `<img src="${img}"> <span>${text}</span>`;
                hiddenInput.value = option.dataset.value;

                options.style.display = "none";
            });
        });

        document.addEventListener("click", (e) => {
            if (!e.target.closest(".custom-select")) {
                options.style.display = "none";
            }
        });
    </script>

    <script>
        const input = document.getElementById("imageInput");
        const preview = document.getElementById("previewImage");
        const icon = document.getElementById("uploadIcon");
        const text = document.getElementById("uploadText");

        input.addEventListener("change", function() {
            const file = this.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = "block";

                    icon.style.display = "none";
                    text.style.display = "none";
                }
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>
</html>