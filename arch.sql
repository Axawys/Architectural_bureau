-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 07 2024 г., 11:05
-- Версия сервера: 5.7.39
-- Версия PHP: 7.3.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `candies`
--

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE `employees` (
  `id` int(11) UNSIGNED NOT NULL,
  `img_path` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `birth` int(11) NOT NULL,
  `id_type` int(11) NOT NULL,
  `description` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `employees` (`id`, `img_path`, `name`, `birth`, `id_type`, `description`) VALUES
(2, '2.jpg', 'Виктор Александрович Кузнецов', 1950, 4, 'Опытный лидер с острым умом и прямолинейным характером. Его обширный опыт направляет фирму с мудростью и чувством традиции.'),
(5, '5.jpg', 'Алексей Павлович Морозов', 1980, 3, 'Внимательный к деталям новатор с любовью к современным архитектурным эстетикам и преданностью удовлетворению клиентов.'),
(7, '7.jpg', 'Николай Евгеньевич Волков', 1975, 2, 'Надежный и аналитический мыслитель, известный своей экспертизой в обеспечении структурной целостности каждого проекта.'),
(1, '1.jpg', 'Елена Васильевна Петрова', 1985, 1, 'Скрупулезная и творческая личность с passion к устойчивому дизайну. Известна своей способностью руководить сложными проектами с спокойствием и вдохновляющим подходом.'),
(3, '3.jpg', 'Григорий Иванович Соколов', 1948, 7, 'Харизматичная и авторитетная фигура, уважаемая за стратегическое видение и глубокое понимание градостроительства.'),
(4, '4.jpg', 'Дмитрий Сергеевич Иванов', 1978, 8, 'Энергичный и общительный командный игрок, мастерски координирующий проекты и способствующий сотрудничеству в коллективе.'),
(6, '6.jpg', 'Софья Михайловна Лебедева', 1992, 1, 'Яркая и энтузиастка-новичок, жаждущая учиться и привносить свежие идеи с помощью своих смелых концепций дизайна.'),
(8, '8.jpg', 'Олег Анатольевич Смирнов', 1965, 6, 'Прагматичный и организованный лидер, который обеспечивает бесперебойную работу фирмы своим эффективным стилем управления.');

-- --------------------------------------------------------

--
-- Структура таблицы `types`
--

CREATE TABLE `job_title` (
  `id` int(11) UNSIGNED NOT NULL,
  `name` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `types`
--


INSERT INTO `job_title` (`id`, `name`) VALUES
(1, 'Архитектор'),
(2, 'Инженер'),
(3, 'Дизайнер'),
(4, 'Генеральный деректор'),
(5, 'Старший архитектор'),
(6, 'Директор по операциям'),
(7, 'Председатель совета директоров'),
(8, 'Менеджер проектов');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `FIO` varchar(255) DEFAULT NULL,
  `date` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `interes` varchar(255) DEFAULT NULL,
  `vk` varchar(255) DEFAULT NULL,
  `blood` varchar(3) DEFAULT NULL,
  `resus` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `FIO`, `date`, `address`, `sex`, `interes`, `vk`, `blood`, `resus`, `email`, `password`) VALUES
(5, 'Иванов Иван Иванович', '2001-03-02', 'Москва', 'Ж', 'бюдлоирмпавкыувкнпо', 'https://vk.com/feed', 'IV', '-', 'email4@gmail.com', '$2y$10$81RTRmlaIPpji48Y5H1T7evzpF/v0CN4YFUSgigkQGzZTi2qexJTG'),
(6, 'Иванов Иван Иванович', '2001-03-02', 'Москва', 'М', 'sdfghjkl;', 'https://vk.com/feed', 'I', '+', 'email11@gmail.com', '$2y$10$olY28NnXeXyae314jY3ese.EhQBaI1OQG0PGxzOl1.3ki91YbxlUy');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `types`
--
ALTER TABLE `job_title`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `types`
--
ALTER TABLE `job_title`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
