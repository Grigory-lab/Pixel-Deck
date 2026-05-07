<?php ?>
<!DOCTYPE html>
<html lang="ru">
<head>
	<meta charset="UTF-8">
	<title>Галерея партнёра</title>
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
			<a href="index.php">Назад</a>
		</nav>
	</div>
</header>
<main class="gallery">
	<h4>Галерея партнёра</h4>
	<button onclick="loadImages()" class="reload-btn">
		Обновить картинки
	</button>
	<div class="cards" id="apiCards"></div>
</main>

<div class="modal api-modal" id="modal">
	<div class="modal-box api-modal-box" id="modalBox"></div>
</div>

<script>
const API_KEY = "Pjayci7zRnUmYjXP6Xg99oIIeaPMMiHz45KnSw64DELbO9dJFbY4MQJD"; 

function loadImages(){
	let container = document.getElementById("apiCards");
	container.innerHTML = "<p style='color:white'>Загрузка...</p>";

	let page = Math.floor(Math.random() * 50) + 1;

	fetch(`https://api.pexels.com/v1/curated?per_page=18&page=${page}`, {
		headers: {
			Authorization: API_KEY
		}
	})
	.then(res => res.json())
	.then(data => {
		container.innerHTML = "";

		data.photos.forEach(img => {
			let card = document.createElement("div");
			card.className = "card api-card";
			card.dataset.author = img.photographer;

			let imageUrl = img.src.medium;
			let original = img.src.large;
			let title = img.alt || "Без названия";

			card.innerHTML = `
				<img class="c-img" src="${imageUrl}">
				<h6 class="title">${title}</h6>
				<p class="author">от ${img.photographer}</p>
			`;

			card.onclick = () => openModal(original, img.photographer, title);
			container.appendChild(card);
		});
	})
	.catch(() => {
		container.innerHTML = '<p style="color:white;">Ошибка загрузки</p>';
	});
}


function openModal(src, author, title){
	let html = `
	<img src="${src}" class="modal-img">
	<h4>${title}</h4>
	<p class="modal-author">от <span>${author}</span></p>
	`;

	document.getElementById("modalBox").innerHTML = html;
	document.getElementById("modal").style.display="flex";
}

document.getElementById("modal").onclick = e=>{
	if(e.target.id==="modal") e.target.style.display="none";
}
loadImages();
</script>

</body>
</html>