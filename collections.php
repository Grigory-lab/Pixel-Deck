<?php
	include "DB.php";

	$authorized = isset($_SESSION['login']);
	$user_id = 0;

	if ($authorized) {
		$login = $_SESSION['login'];

		$stmt = mysqli_prepare($conn, "SELECT User_ID FROM users WHERE Login=?");
		mysqli_stmt_bind_param($stmt, "s", $login);
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);
		$user = mysqli_fetch_assoc($res);

		$user_id = $user['User_ID'];

		$stmt = mysqli_prepare($conn, "SELECT * FROM collections WHERE User_ID=?");
		mysqli_stmt_bind_param($stmt, "i", $user_id);
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);

		$collections = [];
		while ($row = mysqli_fetch_assoc($res)) $collections[] = $row;

		$stmt = mysqli_prepare($conn, "
			SELECT c.*, u.Login
			FROM cards c
			JOIN users u ON c.User_ID=u.User_ID
			WHERE c.User_ID=? OR c.Card_ID IN (
				SELECT Card_ID FROM likes WHERE User_ID=?
			)
		");
		mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
		mysqli_stmt_execute($stmt);
		$res = mysqli_stmt_get_result($stmt);

		$all_cards = [];
		while ($row = mysqli_fetch_assoc($res)) $all_cards[] = $row;
	}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Коллекции</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="collections.css">
	<meta name="description" content="Pixel Deck - большая онлайн-галерея пиксельных картинок. Здесь каждый может поделиться своим творчеством со всем миром.">
    <meta name="keywords" content="картинки, пиксель арт, фотографии, пиксельный стиль, творчество, креатив">
    <meta name="robots" content="index, follow">
</head>

<body class="collections-bg">

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

<main class="collections">
<?php if (!$authorized): ?>
	<div class="not-auth">
		<h5>Войдите в аккаунт, чтобы создавать коллекции</h5>
		<a href="auth.php" class="action-btn">Авторизация</a>
		<a href="register.php" class="action-btn">Регистрация</a>
	</div>
<?php else: ?>
	<h4>Коллекции</h4>
	<p class="subtitle">Здесь хранятся коллекции с сохранёнными вами карточками</p>

	<div class="collections-grid" id="collectionsGrid">

	<?php foreach ($collections as $col): ?>
	<div class="collection" onclick="openCollection(<?= $col['Collection_ID'] ?>,'<?= htmlspecialchars($col['Name']) ?>',<?= $col['Public'] ?>)">
		<img src="img/collection.png">
		<p><?= $col['Name'] ?></p>
	</div>
	<?php endforeach; ?>

	<div class="collection create" onclick="openCreate()">
		<img src="img/create_collection.png">
	</div>
	</div>
	<div id="collectionView" style="display:none;" class="collv">
		<input id="colName" class="modal-input">
		<label class="public-name"><input type="checkbox" id="colPublic"> Публичная</label>

		<h5>Карты в коллекции</h5>
		<div class="cards" id="colCards"></div>

		<hr>

		<h5>Добавить карты</h5>
		<div class="cards">
		<?php foreach ($all_cards as $c): ?>
            <div class="card small"
                data-name="<?= htmlspecialchars($c['Name']) ?>"
                data-author="<?= htmlspecialchars($c['Login']) ?>"
                data-type="<?= $c['Type'] ?>"
                data-desc="<?= htmlspecialchars($c['Description']) ?>"
                onclick="openModal(this)">
				<img class="c-img" src="<?= $c['Image'] ?>">
				<button onclick="event.stopPropagation(); addCard(<?= $c['Card_ID']?>)">Добавить</button>
			</div>
		<?php endforeach; ?>
		</div>

		<div class="modal-btn">
            <button onclick="saveCollection()">Сохранить</button>
        </div>
	</div>
<?php endif; ?>
</main>

<div class="modal" id="modal">
	<div class="modal-box" id="modalBox"></div>
</div>

<script>
let allCardsData = <?php echo json_encode($all_cards); ?>;

let currentCollection = null;
let cardsInCollection = [];

function openModal(card){
	let typeMap = { Pa: "Pixel art", Img: "Image", Pht: "Photo" };
	let iconMap = { Pa: "pixel_icon.png", Img: "painting_icon.png", Pht: "camera_icon.png" };
	let html = `
	<img src="${card.querySelector('.c-img').src}" class="modal-img">
	<h4>${card.dataset.name}</h4>
	<p class="modal-author">от <span>${card.dataset.author}</span></p>
	<p class="type ${card.dataset.type}">
	Тип:<img src="img/${iconMap[card.dataset.type]}">${typeMap[card.dataset.type]}
	</p>
	<p class="modal-desc">${card.dataset.desc}</p>
	`;
	document.getElementById("modalBox").innerHTML = html;
	document.getElementById("modal").style.display="flex";
}

document.getElementById("modal").onclick = e=>{
	if(e.target.id==="modal") e.target.style.display="none";
};

function openCreate(){
	let name = prompt("Название коллекции:");
	if(!name) return;
	fetch("create_collection.php", {
		method:"POST",
		body:"name="+encodeURIComponent(name),
		headers:{"Content-Type":"application/x-www-form-urlencoded"}
	}).then(()=>location.reload());
}

function openCollection(id,name,publicState){
	currentCollection = id;
	document.getElementById("collectionsGrid").style.display="none";
	document.getElementById("collectionView").style.display="block";
	document.getElementById("colName").value = name;
	document.getElementById("colPublic").checked = publicState==1;
	loadCards();
}

function loadCards(){
	fetch("get_collection_cards.php?id="+currentCollection)
	.then(r=>r.json())
	.then(data=>{
		cardsInCollection = data;
		renderCards();
	});
}

function renderCards(){
	let container = document.getElementById("colCards");
	container.innerHTML="";

	cardsInCollection.forEach(c=>{

		let div = document.createElement("div");
		div.className="card small";
		div.dataset.name = c.Name;
		div.dataset.author = c.Login;
		div.dataset.type = c.Type;
		div.dataset.desc = c.Description;
		div.onclick = () => openModal(div);
		div.innerHTML = `
			<img class="c-img" src="${c.Image}">
			<button onclick="event.stopPropagation(); removeCard(${c.Card_ID})">Убрать</button>
		`;
		container.appendChild(div);
	});
}

function addCard(id){
	if(cardsInCollection.find(c=>c.Card_ID==id)) return;

	let card = allCardsData.find(c=>c.Card_ID==id);
	if(!card) return;

	cardsInCollection.push(card);
	renderCards();
}

function removeCard(id){
	cardsInCollection = cardsInCollection.filter(c=>c.Card_ID!=id);
	renderCards();
}

function saveCollection(){

	let data = new URLSearchParams();
	data.append("id", currentCollection);
	data.append("name", document.getElementById("colName").value);
	data.append("public", document.getElementById("colPublic").checked?1:0);
	data.append("cards", JSON.stringify(cardsInCollection));

	fetch("update_collection.php", { method:"POST", body:data })
	.then(()=>location.reload());
}
</script>

</body>
</html>