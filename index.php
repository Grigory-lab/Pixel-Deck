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
	}

	$sql = "
	SELECT c.*, u.Login,
	(SELECT COUNT(*) FROM likes WHERE Card_ID=c.Card_ID) likes_count,
	(SELECT COUNT(*) FROM likes WHERE Card_ID=c.Card_ID AND User_ID=?) liked
	FROM cards c
	JOIN users u ON c.User_ID=u.User_ID
	";

	$stmt = mysqli_prepare($conn, $sql);
	mysqli_stmt_bind_param($stmt, "i", $user_id);
	mysqli_stmt_execute($stmt);
	$res = mysqli_stmt_get_result($stmt);

	$cards = [];
	
	while ($row = mysqli_fetch_assoc($res)) {
		$cards[] = $row;
	}

	$res = mysqli_query($conn, "
		SELECT c.Collection_ID, c.Name, u.Login
		FROM collections c
		JOIN users u ON u.User_ID = c.User_ID
		WHERE c.Public = 1
	");

	$public_collections = [];
	while($row = mysqli_fetch_assoc($res)) $public_collections[] = $row;
?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Галерея</title>
	<link rel="stylesheet" href="main.css">
	<link rel="stylesheet" href="index.css">
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
	<h4>Pixel Deck Gallery</h4>
	<p class="subtitle">Находите красивые пиксель арты и другие картинки от пользователей со всего мира.</p>
	<p class="subtitle">
		Также посмотрите картинки нашего сервиса-партнёра:
		<a href="API.php" style="color:#ff4d4d; text-decoration: underline;">Открыть галерею</a>
	</p>

	<div class="top-bar">
		<div class="search">
			<img src="img/search_icon.png">
			<input type="text" id="search" placeholder="Поиск по названию, автору...">
		</div>

		<div class="filters">
			<img src="img/filter_icon.png" class="filter-icon">
			<div class="dropdown" id="typeDropdown">
				<div class="dropdown-selected">
					Типы <img src="img/arrow_down.png">
				</div>
				<div class="dropdown-options">
					<div data-type="all" class="active"><img src="img/all_icon.png">Все</div>
					<div data-type="Pa"><img src="img/pixel_icon.png">Pa</div>
					<div data-type="Img"><img src="img/painting_icon.png">Img</div>
					<div data-type="Pht"><img src="img/camera_icon.png">Pht</div>
				</div>
			</div>

			<div class="dropdown" id="sortDropdown">
				<div class="dropdown-selected">
					Сортировка <img src="img/arrow_down.png">
				</div>
				<div class="dropdown-options">
					<div data-sort="new">Новые</div>
					<div data-sort="old">Старые</div>
					<div data-sort="like">Популярные</div>
					<div data-sort="dislike">Непопулярные</div>
				</div>
			</div>
		</div>
		<a href="#collectionsBlock" class="collections-link">Коллекции ↓</a>
	</div>

	<div class="cards" id="cards">

	<?php foreach ($cards as $c): ?>
	<div class="card"
	data-name="<?= htmlspecialchars($c['Name']) ?>"
	data-author="<?= htmlspecialchars($c['Login']) ?>"
	data-type="<?= $c['Type'] ?>"
	data-date="<?= $c['Upload_date'] ?>"
	data-likes="<?= $c['likes_count'] ?>"
	data-desc="<?= htmlspecialchars($c['Description']) ?>"
	onclick="openModal(this)">

	<img class="c-img" src="<?= htmlspecialchars($c['Image']) ?>">

	<h6 class="title"><?= htmlspecialchars($c['Name']) ?></h6>

	<p class="author">от <?= htmlspecialchars($c['Login']) ?></p>

	<div class="bottom">

	<div class="type <?= $c['Type'] ?>">
	<img src="img/<?=
	$c['Type']=="Pa"?"pixel_icon.png":
	($c['Type']=="Img"?"painting_icon.png":"camera_icon.png")
	?>">
	<?= $c['Type'] ?>
	</div>

	<div class="likes <?= $c['liked']?'active':'' ?>"
	onclick="event.stopPropagation(); like(this,<?= $c['Card_ID']?>)">
	<img src="img/like_icon.png">
	<span><?= $c['likes_count']?></span>
	</div>

	</div>

	</div>
	<?php endforeach; ?>

	</div>

	<h5 id="collectionsBlock" style="margin-top:40px; font-weight: 5;">Публичные коллекции</h5>

	<div class="cards" id="collections">

	<?php foreach ($public_collections as $col): ?>
	<a href="view_collection.php?id=<?= $col['Collection_ID'] ?>" style="text-decoration:none;">
		<div class="card" 
			data-name="<?= htmlspecialchars($col['Name']) ?>"
			data-author="<?= htmlspecialchars($col['Login']) ?>">
			<img class="c-img" src="img/collection.png">

			<h6 class="title"><?= htmlspecialchars($col['Name']) ?></h6>

			<p class="author">от <?= htmlspecialchars($col['Login']) ?></p>
		</div>
	</a>
	<?php endforeach; ?>

	</div>

</main>

<div class="modal" id="modal">
	<div class="modal-box" id="modalBox"></div>
</div>

<script>

let cards = Array.from(document.querySelectorAll("#cards .card"));
let collections = Array.from(document.querySelectorAll("#collections .card"));

function openModal(card){

	let typeMap = { Pa: "Pixel art", Img: "Image", Pht: "Photo" };
	let iconMap = { Pa: "pixel_icon.png", Img: "painting_icon.png", Pht: "camera_icon.png" };

	let html = `
	<img src="${card.querySelector('.c-img').src}" class="modal-img">
	<h4>${card.querySelector('.title').innerText}</h4>
	<p class="modal-author">от <span>${card.dataset.author}</span></p>
	<p class="type ${card.dataset.type}">
	Тип картинки:
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

function like(el,id){
	<?php if(!$authorized): ?>
	toast("Войдите в аккаунт");
	return;
	<?php endif; ?>

	fetch("like.php",{method:"POST",body:"id="+id,
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

function toast(t){
	let d=document.createElement("div");
	d.className="toast";
	d.innerText=t;
	document.body.appendChild(d);
	setTimeout(()=>d.remove(),2000);
}

document.getElementById("search").oninput=function(){
	let val = this.value.toLowerCase();

	cards.forEach(c=>{
		let name = (c.dataset.name || "").toLowerCase();
		let author = (c.dataset.author || "").toLowerCase();

		let matchSearch = name.includes(val) || author.includes(val);

		let matchType = activeTypes.length === 0 || activeTypes.includes(c.dataset.type);

		c.style.display = (matchSearch && matchType) ? "block" : "none";
	});

	collections.forEach(c=>{
		let name = (c.dataset.name || "").toLowerCase();
		let author = (c.dataset.author || "").toLowerCase();

		let matchSearch = name.includes(val) || author.includes(val);

		c.style.display = matchSearch ? "block" : "none";
	});

};

let activeTypes = [];
let activeSort = null;

window.onload = ()=>{
	document.querySelectorAll(".dropdown-options").forEach(o=>{
		o.style.display = "none";
	});
};

document.querySelectorAll("#typeDropdown .dropdown-options div").forEach(btn=>{
btn.onclick=()=>{
	let type = btn.dataset.type;

	if(type==="all"){
		activeTypes = [];
		document.querySelectorAll("#typeDropdown .dropdown-options div").forEach(b=>b.classList.remove("active"));
		btn.classList.add("active");
	}else{
		document.querySelector('[data-type="all"]').classList.remove("active");

		if(activeTypes.includes(type)){
			activeTypes = activeTypes.filter(t=>t!==type);
			btn.classList.remove("active");
		}else{
			activeTypes.push(type);
			btn.classList.add("active");
		}

		if(activeTypes.length===0){
			document.querySelector('[data-type="all"]').classList.add("active");
		}
	}
	filterCards();
};
});

document.querySelectorAll("#sortDropdown .dropdown-options div").forEach(btn=>{
btn.onclick=()=>{
	let sort = btn.dataset.sort;

	document.querySelectorAll("#sortDropdown .dropdown-options div").forEach(b=>b.classList.remove("active"));

	if(activeSort===sort){
		activeSort=null;
	}else{
		activeSort=sort;
		btn.classList.add("active");
	}
	filterCards();
};
});

function filterCards(){
	let filtered = cards.filter(c=>{
		if(activeTypes.length>0){
			return activeTypes.includes(c.dataset.type);
		}
		return true;
	});

	if(activeSort==="new"){
		filtered.sort((a,b)=> new Date(b.dataset.date)-new Date(a.dataset.date));
	}
	if(activeSort==="old"){
		filtered.sort((a,b)=> new Date(a.dataset.date)-new Date(b.dataset.date));
	}
	if(activeSort==="like"){
		filtered.sort((a,b)=> b.dataset.likes-a.dataset.likes);
	}
	if(activeSort==="dislike"){
		filtered.sort((a,b)=> a.dataset.likes-b.dataset.likes);
	}

	let container = document.getElementById("cards");
	container.innerHTML="";
	filtered.forEach(c=>container.appendChild(c));
	toggleCollectionsVisibility();
}

document.querySelectorAll(".dropdown-selected").forEach(d=>{
d.onclick=(e)=>{
	e.stopPropagation();
	let opt = d.nextElementSibling;
	opt.style.display = opt.style.display==="block"?"none":"block";
};
});

document.addEventListener("click",()=>{
document.querySelectorAll(".dropdown-options").forEach(o=>o.style.display="none");
});

function toggleCollectionsVisibility(){
	let block = document.getElementById("collections");
	if(activeTypes.length > 0 || activeSort !== null){
		block.style.display = "none";
	}else{
		block.style.display = "flex";
	}
}
</script>

</body>
</html>