-- Adminer 5.4.2 MySQL 8.4.8 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `employees`;
CREATE TABLE `employees` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `img_path` varchar(45) NOT NULL,
  `name` varchar(45) NOT NULL,
  `birth` int NOT NULL,
  `id_type` int NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `employees` (`id`, `img_path`, `name`, `birth`, `id_type`, `description`) VALUES
(1,	'1.jpg',	'Елена Васильевна Петрова',	1985,	1,	'Скрупулезная и творческая личность с passion к устойчивому дизайну. Известна своей способностью руководить сложными проектами с спокойствием и вдохновляющим подходом.'),
(2,	'2.jpg',	'Виктор Александрович Кузнецов',	1950,	4,	'Опытный лидер с острым умом и прямолинейным характером. Его обширный опыт направляет фирму с мудростью и чувством традиции.'),
(3,	'3.jpg',	'Григорий Иванович Соколов',	1948,	7,	'Харизматичная и авторитетная фигура, уважаемая за стратегическое видение и глубокое понимание градостроительства.'),
(4,	'4.jpg',	'Дмитрий Сергеевич Иванов',	1978,	8,	'Энергичный и общительный командный игрок, мастерски координирующий проекты и способствующий сотрудничеству в коллективе.'),
(5,	'5.jpg',	'Алексей Павлович Морозов',	1980,	3,	'Внимательный к деталям новатор с любовью к современным архитектурным эстетикам и преданностью удовлетворению клиентов.'),
(7,	'7.jpg',	'Николай Евгеньевич Волков',	1975,	2,	'Надежный и аналитический мыслитель, известный своей экспертизой в обеспечении структурной целостности каждого проекта.'),
(8,	'8.jpg',	'Олег Анатольевич Смирнов',	1965,	6,	'Прагматичный и организованный лидер, который обеспечивает бесперебойную работу фирмы своим эффективным стилем управления.'),
(9,	'1773206175_val.jpg',	'Стажер',	1988,	2,	'ф');

DROP TABLE IF EXISTS `job_title`;
CREATE TABLE `job_title` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `job_title` (`id`, `name`) VALUES
(1,	'Архитектор'),
(2,	'Инженер'),
(3,	'Дизайнер'),
(4,	'Генеральный деректор'),
(5,	'Старший архитектор'),
(6,	'Директор по операциям'),
(7,	'Председатель совета директоров'),
(8,	'Менеджер проектов');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `FIO` varchar(255) DEFAULT NULL,
  `date` varchar(10) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `sex` varchar(10) DEFAULT NULL,
  `interes` varchar(255) DEFAULT NULL,
  `vk` varchar(255) DEFAULT NULL,
  `blood` varchar(3) DEFAULT NULL,
  `resus` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

INSERT INTO `users` (`id`, `FIO`, `date`, `address`, `sex`, `interes`, `vk`, `blood`, `resus`, `email`, `password`) VALUES
(6,	'Иванов Иван Иванович',	'2001-03-02',	'Москва',	'М',	'sdfghjkl;',	'https://vk.com/feed',	'I',	'+',	'email11@gmail.com',	'$2y$10$olY28NnXeXyae314jY3ese.EhQBaI1OQG0PGxzOl1.3ki91YbxlUy'),
(7,	'Попов Дмитрий Александрович',	'31/07/2005',	'Волгоград, ...',	'М',	'-',	'https://vk.com/axawys',	'I',	'+',	'axawys@gmail.com',	'$2y$10$abcOclz.22MohHsozeS50OKY9l/3fUDU4Wu3JEoFzA6V.g3VZQ6Nq');

-- 2026-03-11 05:19:23 UTC
