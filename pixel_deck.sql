-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Май 07 2026 г., 12:10
-- Версия сервера: 10.4.32-MariaDB
-- Версия PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `pixel_deck`
--

-- --------------------------------------------------------

--
-- Структура таблицы `cards`
--

CREATE TABLE `cards` (
  `Card_ID` int(11) NOT NULL,
  `Name` varchar(250) DEFAULT NULL,
  `Image` varchar(255) DEFAULT NULL,
  `Type` varchar(100) DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Upload_date` date DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `cards`
--

INSERT INTO `cards` (`Card_ID`, `Name`, `Image`, `Type`, `Description`, `Upload_date`, `User_ID`) VALUES
(14, 'BEAST!', 'uploads/69f0bc5be5664.png', 'Img', 'from TBOI Repentance +', '2026-04-28', 1),
(15, 'Hippo', 'uploads/69f0bc80db130.png', 'Pa', 'Бегемотик :)', '2026-04-28', 1),
(16, 'Shadowy Figure', 'uploads/69f0bca94d4c3.jpg', 'Img', 'Oh, look at that cool shady guy!', '2026-04-28', 1),
(17, 'book card', 'uploads/69f0bcd62d7ec.png', 'Pa', '11', '2026-04-28', 1),
(18, 'Догма', 'uploads/69f0bcf2a8fa5.png', 'Img', 'Статика', '2026-04-28', 1),
(19, 'G логотип', 'uploads/69f0bd0e8d395.png', 'Img', 'Прикольный логотип', '2026-04-28', 1),
(21, 'Уголь', 'uploads/69f36e68d96b9.png', 'Pht', 'Уголь', '2026-04-30', 2),
(22, 'Sandler', 'uploads/69f3a7b06cdd0.png', 'Pa', 'Криповый чел', '2026-04-30', 10),
(24, 'Melon', 'uploads/69fc4f5643c29.png', 'Img', 'Арбуз', '2026-05-07', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `collections`
--

CREATE TABLE `collections` (
  `Collection_ID` int(11) NOT NULL,
  `Name` varchar(250) DEFAULT NULL,
  `Public` tinyint(1) DEFAULT NULL,
  `User_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `collections`
--

INSERT INTO `collections` (`Collection_ID`, `Name`, `Public`, `User_ID`) VALUES
(1, 'Колода 1', 1, 1),
(2, 'Coller', 0, 9),
(3, 'Колода 2', 0, 1),
(4, 'Колода 3', 0, 1),
(5, 'Коллекция 24590', 0, 1);

-- --------------------------------------------------------

--
-- Структура таблицы `collection_cards`
--

CREATE TABLE `collection_cards` (
  `Collection_cards_ID` int(11) NOT NULL,
  `Collection_ID` int(11) DEFAULT NULL,
  `Card_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `collection_cards`
--

INSERT INTO `collection_cards` (`Collection_cards_ID`, `Collection_ID`, `Card_ID`) VALUES
(8, 2, 17),
(29, 1, 18),
(30, 1, 21),
(31, 1, 19);

-- --------------------------------------------------------

--
-- Структура таблицы `likes`
--

CREATE TABLE `likes` (
  `Like_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Card_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `likes`
--

INSERT INTO `likes` (`Like_ID`, `User_ID`, `Card_ID`) VALUES
(6, 1, 18),
(9, 1, 16),
(10, 1, 15),
(12, 2, 21),
(13, 1, 21),
(14, 9, 21),
(15, 9, 17),
(16, 10, 16),
(17, 10, 17);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL,
  `Login` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `Mail` varchar(100) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`User_ID`, `Login`, `Password`, `Mail`, `Country`) VALUES
(1, 'admin', '$2y$10$usZBRUj/R7u1Qg8clpBb2O0/fMzWJwvUIJJTK76VzQLVYZ1O2t2.G', 'admin@gmail.com', 'Беларусь'),
(2, '1', '$2y$10$nHrEtJz7p1ujuWaC6IBdhuYWLpmYB/Ko76hxSkDmfW32nxpQKp9ie', '1@mail.com', 'Россия'),
(4, '2', '$2y$10$tBNeOVfV3BfMTUv0jc.9wOSFZNPpKwy0Zh8gqIHxh0nbeR3aV57KO', '2@mail.com', 'Беларусь'),
(7, '3', '$2y$10$gJqeH56Oagzzw5T6pXuQ9.jELdSG/Sr1FI03vP4xo2nEoHymViAlC', '3@mail.com', 'Китай'),
(8, '4', '$2y$10$7EDt99/NxJotFZsyvjN0Eefuiipm9NUm17XIjwl7OPrHasV75yJ32', '4@mail.com', 'Франция'),
(9, '7', '$2y$10$UhmDzFpWYJwLz/bfIuLJn.LlaqEjzn9AwD2uA0Pn8u5s7KIoKoySm', '7@mail.com', 'Франция'),
(10, 'Lesh', '$2y$10$ZYz7LLYWCn/duM7Zny1zmurZ6et1qSuBslcp1t6kmXcLomRBWvNLG', 'lesh@mail.com', 'США');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `cards`
--
ALTER TABLE `cards`
  ADD PRIMARY KEY (`Card_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Индексы таблицы `collections`
--
ALTER TABLE `collections`
  ADD PRIMARY KEY (`Collection_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Индексы таблицы `collection_cards`
--
ALTER TABLE `collection_cards`
  ADD PRIMARY KEY (`Collection_cards_ID`),
  ADD KEY `Collection_ID` (`Collection_ID`),
  ADD KEY `Card_ID` (`Card_ID`);

--
-- Индексы таблицы `likes`
--
ALTER TABLE `likes`
  ADD PRIMARY KEY (`Like_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Card_ID` (`Card_ID`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `cards`
--
ALTER TABLE `cards`
  MODIFY `Card_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT для таблицы `collections`
--
ALTER TABLE `collections`
  MODIFY `Collection_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `collection_cards`
--
ALTER TABLE `collection_cards`
  MODIFY `Collection_cards_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT для таблицы `likes`
--
ALTER TABLE `likes`
  MODIFY `Like_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `cards`
--
ALTER TABLE `cards`
  ADD CONSTRAINT `cards_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Ограничения внешнего ключа таблицы `collections`
--
ALTER TABLE `collections`
  ADD CONSTRAINT `collections_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`);

--
-- Ограничения внешнего ключа таблицы `collection_cards`
--
ALTER TABLE `collection_cards`
  ADD CONSTRAINT `collection_cards_ibfk_1` FOREIGN KEY (`Collection_ID`) REFERENCES `collections` (`Collection_ID`),
  ADD CONSTRAINT `collection_cards_ibfk_2` FOREIGN KEY (`Card_ID`) REFERENCES `cards` (`Card_ID`);

--
-- Ограничения внешнего ключа таблицы `likes`
--
ALTER TABLE `likes`
  ADD CONSTRAINT `likes_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `likes_ibfk_2` FOREIGN KEY (`Card_ID`) REFERENCES `cards` (`Card_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
