-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--

CREATE TABLE `categories` (
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active_image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `halfactive_image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `inactive_image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `categories`
--

INSERT INTO `categories` (`title`, `active_image`, `halfactive_image`, `inactive_image`, `is_active`, `name`, `order`) VALUES
('Graphiks. Графика', 'images/menu/paintes_activ.jpg', 'images/menu/paintes_no.jpg', 'images/menu/paintes_notactiv.jpg', 1, 'graphics', 1),
('life. Живопись', 'images/menu/life_activ.jpg', 'images/menu/life_no.jpg', 'images/menu/life_notactiv.jpg', 1, 'life', 0),
('Murals. Фрески', 'images/menu/fresco_activ.jpg', 'images/menu/fresco_no.jpg', 'images/menu/fresco_notactiv.jpg', 1, 'murals', 3),
('Others. Разное', 'images/menu/others_activ.jpg', 'images/menu/others_no.jpg', 'images/menu/others_notactiv.jpg', 1, 'others', 4),
('Photos. Фотография', 'images/menu/fotos_activ.jpg', 'images/menu/fotos_no.jpg', 'images/menu/fotos_notactiv.jpg', 1, 'photo', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `src` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `preview` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `title` text COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL,
  `category` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Дамп данных таблицы `items`
--

INSERT INTO `items` (`id`, `src`, `preview`, `title`, `description`, `order`, `category`, `is_active`) VALUES
(1, '/images/photo/big/00.jpg', '/images/photo/small/00.jpg', 'Солнце Садится.', '2005 г.', 1, 'photo', 1),
(9, '/images/life/big/00.jpg', '/images/life/small/00.jpg', 'Пьющий из Кувшина', '92х73. 2000г.', 1, 'life', 1),
(10, '/images/life/big/01.jpg', '/images/life/small/01.jpg', 'Лицо незнакомца', '70х60. 2011г.', 7, 'life', 1),
(11, '/images/life/big/02.jpg', '/images/life/small/02.jpg', 'Автопортрет-1', '70х60. 2009г.', 13, 'life', 1),
(33, '/images/painting/big/00.jpg', '/images/painting/small/00.jpg', 'Улыбающийся автопортрет	', '61х45. 2009г.\r\nТушь. Кисть. Перо', 1, 'graphics', 1),
(34, '/images/painting/big/01.jpg', '/images/painting/small/01.jpg', 'Старуха', '69х50. 2009г.\r\nТушь. Кисть. Перо', 7, 'graphics', 1),
(35, '/images/painting/big/02.jpg', '/images/painting/small/02.jpg', 'Selfportrait', '61х45. 2008г.\r\nТушь. Кисть. Перо', 13, 'graphics', 1),
(57, '/images/photo/big/01.jpg', '/images/photo/big/01.jpg', 'Стена Вдоль Полотна.', '1989 г.', 7, 'photo', 1),
(58, '/images/photo/big/02.jpg', '/images/photo/small/02.jpg', 'Длинная Туча.', '2004г.', 13, 'photo', 1),
(78, '/images/other/big/00.jpg', '/images/other/small/00.jpg', 'Большая Ива <br>\r\n(Bourgogne - Cersot)', '60х70. 2010г.', 1, 'others', 1),
(79, '/images/other/big/01.jpg', '/images/other/small/01.jpg', 'Усадьба в Серсо <br>\r\n(Bourgogne - Cersot)', '60х70. 2010г.', 7, 'others', 1),
(80, '/images/other/big/02.jpg', '/images/other/small/02.jpg', 'Загон на выпасе <br>\r\n(Bourgogne - Cersot)', '60х70. 2010г.', 13, 'others', 1),
(102, '/images/fresco/big/00.jpg', '/images/fresco/small/00.jpg', 'Светский Выезд Принца Сидхартхи.', 'Центральная Стена<br>\r\nПотолок', 1, 'murals', 1),
(103, '/images/fresco/big/01.jpg', '/images/fresco/small/01.jpg', 'Роспись Потолка. Музыканты.', 'Фрагмент Фриза <br>\r\nОрнамент', 7, 'murals', 1),
(104, '/images/fresco/big/02.jpg', '/images/fresco/small/02.jpg', 'Роспись Потолка.\r\n Музыканты.', 'Леса. Эскиз. Начало', 13, 'murals', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--

CREATE TABLE `settings` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Web site security system settings.';

--
-- Дамп данных таблицы `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'ROWS', '6'),
(2, 'COLS', '6'),
(3, 'TO_INDEX_TITLE', 'To the Main Page. На Главную Страницу'),
(4, 'TITLE', 'SERGUIUS - сайт \"Творчество Сергея Агасаряна\" - СЕРГИУС'),
(5, 'KEYWORDS', 'Агасарян Кирсанов Продам Куплю Картины Современные художники Московский художник Художники Рисунки Искусство Галереи Живопись Фреска Графика Фотографии Иконы Интерьеры Роспись Узоры Дизайн Ремонт Офорты Гравюры Орнаменты Витражи Копии Портреты Пейзажи Резьба по дереву Agasaryen Agasaryan'),
(6, 'COPYRIGHT', 'Serguey Agasaryen Сергей Агасарян');

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `uid` int(10) UNSIGNED NOT NULL,
  `id` varchar(255) NOT NULL,
  `pass` varchar(32) NOT NULL,
  `hash` varchar(32) NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT '0',
  `lockcount` tinyint(3) UNSIGNED NOT NULL DEFAULT '0',
  `updated` date NOT NULL DEFAULT '0000-00-00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='System users information.';

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`uid`, `id`, `pass`, `hash`, `active`, `lockcount`, `updated`) VALUES
(1, 'admin', '1e0bc1113e544133e0ce23c2f0684186', 'f8eec761528ba161a1d5c83d0cfc0ffa', 1, 2, '2023-07-11');

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`name`);

--
-- Индексы таблицы `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`uid`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=126;

--
-- AUTO_INCREMENT для таблицы `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
