<?php
	include "DB.php";

	$authorized = isset($_SESSION['login']);
	$user_id = 0;

	if ($authorized) {
		$login = $_SESSION['login'];

		$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE Login=?");
		mysqli_stmt_bind_param($stmt, "s", $login);
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);
		$user = mysqli_fetch_assoc($res);

		$user_id = $user['User_ID'];

		$stmt = mysqli_prepare($conn, "
			SELECT c.*, u.Login,
			(SELECT COUNT(*) FROM likes WHERE Card_ID=c.Card_ID) likes_count,
			(SELECT COUNT(*) FROM likes WHERE Card_ID=c.Card_ID AND User_ID=?) liked
			FROM cards c
			JOIN users u ON c.User_ID=u.User_ID
			WHERE c.User_ID=?
		");
		mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);

		$my_cards = [];
		while ($row = mysqli_fetch_assoc($res)) $my_cards[] = $row;

		$stmt = mysqli_prepare($conn, "
			SELECT c.*, u.Login,
			(SELECT COUNT(*) FROM likes WHERE Card_ID=c.Card_ID) likes_count,
			1 liked
			FROM cards c
			JOIN users u ON c.User_ID=u.User_ID
			JOIN likes l ON l.Card_ID=c.Card_ID
			WHERE l.User_ID=?
		");
		mysqli_stmt_bind_param($stmt, "i", $user_id);
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);

		$liked_cards = [];
		while ($row = mysqli_fetch_assoc($res)) $liked_cards[] = $row;
	}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Аккаунт</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="account.css">
	<meta name="description" content="Pixel Deck - большая онлайн-галерея пиксельных картинок. Здесь каждый может поделиться своим творчеством со всем миром.">
    <meta name="keywords" content="картинки, пиксель арт, фотографии, пиксельный стиль, творчество, креатив">
    <meta name="robots" content="index, follow">
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

<?php if (!$authorized): ?>
	<div class="not-auth">
		<h5>Войдите в аккаунт</h5>
		<a href="auth.php" class="action-btn">Авторизация</a>
		<a href="register.php" class="action-btn">Регистрация</a>
	</div>
<?php else: ?>

	<h4 class="title-main">Аккаунт</h4>

	<div class="left-block">
		<p class="user-info">
			Пользователь: <?= htmlspecialchars($user['Login'], ENT_QUOTES) ?> [<?= htmlspecialchars($user['Country'], ENT_QUOTES) ?>]

			<?php if($user['Login'] === 'admin'): ?>
				<a href="admin.php" class="admin-btn">Админ панель</a>
			<?php endif; ?>
			<a href="logout.php" class="logout-btn">Выйти</a>
		</p>
		<a href="account_details.php" class="edit-btn">✏ Изменить данные аккаунта</a>
	</div>

	<hr>

	<h5 class="left-block">Ваши карты</h5>

	<div class="cards">
	<?php foreach ($my_cards as $c): ?>
	<div class="card"
	data-id="<?= (int)$c['Card_ID'] ?>"
	data-name="<?= htmlspecialchars($c['Name'], ENT_QUOTES) ?>"
	data-type="<?= htmlspecialchars($c['Type'], ENT_QUOTES) ?>"
	data-desc="<?= htmlspecialchars($c['Description'], ENT_QUOTES) ?>"
	onclick="openEdit(this)">

	<img class="c-img" src="<?= htmlspecialchars($c['Image'], ENT_QUOTES) ?>">
	<h6 class="title"><?= htmlspecialchars($c['Name'], ENT_QUOTES) ?></h6>
	<p class="author">от <?= htmlspecialchars($c['Login'], ENT_QUOTES) ?></p>

	<div class="bottom">
	<div class="type <?= htmlspecialchars($c['Type'], ENT_QUOTES) ?>">
	<img src="img/<?=
	$c['Type']=="Pa"?"pixel_icon.png":
	($c['Type']=="Img"?"painting_icon.png":"camera_icon.png")
	?>">
	<?= htmlspecialchars($c['Type'], ENT_QUOTES) ?>
	</div>

	<div class="likes <?= $c['liked']?'active':'' ?>"
	onclick="event.stopPropagation(); like(this,<?= (int)$c['Card_ID']?>)">
	<img src="img/like_icon.png">
	<span><?= (int)$c['likes_count']?></span>
	</div>
	</div>

	</div>
	<?php endforeach; ?>
	</div>

	<hr>

	<h5 class="left-block">Понравившиеся</h5>

	<div class="cards">
	<?php foreach ($liked_cards as $c): ?>
		<div class="card"
		data-name="<?= htmlspecialchars(strtolower($c['Name']), ENT_QUOTES) ?>"
		data-author="<?= htmlspecialchars(strtolower($c['Login']), ENT_QUOTES) ?>"
		data-type="<?= htmlspecialchars($c['Type'], ENT_QUOTES) ?>"
		data-desc="<?= htmlspecialchars($c['Description'], ENT_QUOTES) ?>"
		onclick="openView(this)">
			<img class="c-img" src="<?= htmlspecialchars($c['Image'], ENT_QUOTES) ?>">
			<h6 class="title"><?= htmlspecialchars($c['Name'], ENT_QUOTES) ?></h6>
			<p class="author">от <?= htmlspecialchars($c['Login'], ENT_QUOTES) ?></p>

			<div class="bottom">
				<div class="type <?= htmlspecialchars($c['Type'], ENT_QUOTES) ?>">
					<img src="img/<?=
					$c['Type']=="Pa"?"pixel_icon.png":
					($c['Type']=="Img"?"painting_icon.png":"camera_icon.png")
					?>">
					<?= htmlspecialchars($c['Type'], ENT_QUOTES) ?>
				</div>

				<div class="likes active"
				onclick="event.stopPropagation(); like(this,<?= (int)$c['Card_ID']?>)">
					<img src="img/like_icon.png">
					<span><?= (int)$c['likes_count']?></span>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>

<?php endif; ?>

</main>

<div class="modal" id="modal">
	<div class="modal-box" id="modalBox"></div>
</div>

<script>

function openView(card){
	let typeMap = { Pa: "Pixel art", Img: "Image", Pht: "Photo" };
	let iconMap = { Pa: "pixel_icon.png", Img: "painting_icon.png", Pht: "camera_icon.png" };

	let html = `
	<img src="${card.querySelector('.c-img').src}" class="modal-img">
	<h4>${card.querySelector('.title').innerText}</h4>
	<p class="modal-author">от <span>${card.dataset.author}</span></p>
	<p class="type ${card.dataset.type}">
	Тип:
	<img src="img/${iconMap[card.dataset.type]}">
	${typeMap[card.dataset.type]}
	</p>
	<p class="modal-desc">${card.dataset.desc}</p>
	`;

	document.getElementById("modalBox").innerHTML = html;
	document.getElementById("modal").style.display="flex";
}

function openEdit(card){
	let iconMap = { Pa: "pixel_icon.png", Img: "painting_icon.png", Pht: "camera_icon.png" };

	let html = `
	<img src="${card.querySelector('.c-img').src}" class="modal-img">

	<input id="name" value="${card.dataset.name}" class="modal-input">

	<div class="modal-type">
		<img id="typeIcon" src="img/${iconMap[card.dataset.type]}">
		<select id="type" onchange="changeIcon(this)">
			<option value="Pa">Pa</option>
			<option value="Img">Img</option>
			<option value="Pht">Pht</option>
		</select>
	</div>

	<textarea id="desc" class="modal-textarea">${card.dataset.desc}</textarea>

	<button class="modal-btn" onclick="save(${card.dataset.id})">Сохранить</button>
    <button class="modal-btn delete-btn" onclick="del(${card.dataset.id})">Удалить</button>
	`;

	document.getElementById("modalBox").innerHTML = html;
	document.getElementById("modal").style.display="flex";
}

function del(id){
	if(!confirm("Удалить карточку?")) return;

	let data = new URLSearchParams();
	data.append("id", parseInt(id));

	fetch("delete_card.php", { method: "POST", body: data })
	.then(()=> location.reload());
}

function changeIcon(sel){
	let map = { Pa: "pixel_icon.png", Img: "painting_icon.png", Pht: "camera_icon.png" };
	document.getElementById("typeIcon").src = "img/" + map[sel.value];
}

function save(id){
	let name = document.getElementById("name").value.trim();
	let desc = document.getElementById("desc").value.trim();
	let type = document.getElementById("type").value;

	if(name.length < 1 || name.length > 100){
		alert("Неверное название");
		return;
	}

	let data = new URLSearchParams();
	data.append("id", parseInt(id));
	data.append("name", name);
	data.append("type", type);
	data.append("desc", desc);

	fetch("update_card.php", { method: "POST", body: data })
	.then(()=> location.reload());
}

function like(el,id){
	fetch("like.php",{method:"POST",body:"id="+parseInt(id),
	headers:{"Content-Type":"application/x-www-form-urlencoded"}})
	.then(r=>r.text())
	.then(res=>{
	let span = el.querySelector("span");

	if(res==="liked"){
		el.classList.add("active");
		span.textContent++;
	}else{
		el.classList.remove("active");
		span.textContent--;
	}
	});
}

document.getElementById("modal").onclick = e=>{
	if(e.target.id==="modal") e.target.style.display="none";
}

</script>

</body>
</html>