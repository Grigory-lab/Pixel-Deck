<?php
	include "DB.php";

	$id = $_GET['id'] ?? 0;

	$stmt = mysqli_prepare($conn, "
		SELECT c.*, u.Login 
		FROM collections c
		JOIN users u ON u.User_ID=c.User_ID
		WHERE c.Collection_ID=? AND c.Public=1
	");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);
	$collection = mysqli_fetch_assoc($res);

	if(!$collection){
		echo "Коллекция не найдена";
		exit;
	}

	$stmt = mysqli_prepare($conn, "
		SELECT c.*, u.Login
		FROM collection_cards cc
		JOIN cards c ON c.Card_ID=cc.Card_ID
		JOIN users u ON u.User_ID=c.User_ID
		WHERE cc.Collection_ID=?
	");
	mysqli_stmt_bind_param($stmt, "i", $id);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);

	$cards = [];
	while($row = mysqli_fetch_assoc($res)) $cards[] = $row;
?>

<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title><?= htmlspecialchars($collection['Name']) ?></title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="view_collection.css">
	<meta name="description" content="Pixel Deck - большая онлайн-галерея пиксельных картинок. Здесь каждый может поделиться своим творчеством со всем миром.">
    <meta name="keywords" content="картинки, пиксель арт, фотографии, пиксельный стиль, творчество, креатив">
    <meta name="robots" content="index, follow">
</head>

<body class="gallery-bg">

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

<main class="gallery">
	<h4><?= htmlspecialchars($collection['Name']) ?></h4>
	<p class="subtitle">Автор: <?= htmlspecialchars($collection['Login']) ?></p>

	<div class="cards">

	<?php foreach ($cards as $c): ?>
	<div class="card"
		data-name="<?= htmlspecialchars($c['Name']) ?>"
		data-author="<?= htmlspecialchars($c['Login']) ?>"
		data-type="<?= $c['Type'] ?>"
		data-desc="<?= htmlspecialchars($c['Description']) ?>"
		onclick="openModal(this)">

		<img class="c-img" src="<?= $c['Image'] ?>">

		<h6 class="title"><?= $c['Name'] ?></h6>
		<p class="author">от <?= $c['Login'] ?></p>

		<div class="bottom">
		<div class="type <?= $c['Type'] ?>">
		<img src="img/<?=
		$c['Type']=="Pa"?"pixel_icon.png":
		($c['Type']=="Img"?"painting_icon.png":"camera_icon.png")
		?>">
		<?= $c['Type'] ?>
		</div>
		</div>

	</div>
	<?php endforeach; ?>
	</div>
</main>

<div class="modal" id="modal">
	<div class="modal-box" id="modalBox"></div>
</div>

<script>

function openModal(card){
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

document.getElementById("modal").onclick = e=>{
	if(e.target.id==="modal") e.target.style.display="none";
}

</script>

</body>
</html>