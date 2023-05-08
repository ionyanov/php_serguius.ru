-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Май 08 2023 г., 19:12
-- Версия сервера: 5.7.21-20-beget-5.7.21-20-1-log
-- Версия PHP: 5.6.40

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Структура таблицы `categories`
--
-- Создание: Дек 20 2017 г., 12:03
-- Последнее обновление: Май 08 2023 г., 09:54
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `active_image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `halfactive_image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `inactive_image` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `order` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `items`
--
-- Создание: Дек 20 2017 г., 12:03
-- Последнее обновление: Май 08 2023 г., 10:13
--

DROP TABLE IF EXISTS `items`;
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

-- --------------------------------------------------------

--
-- Структура таблицы `settings`
--
-- Создание: Дек 20 2017 г., 12:03
--

DROP TABLE IF EXISTS `settings`;
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
-- Создание: Дек 20 2017 г., 12:03
-- Последнее обновление: Май 08 2023 г., 07:57
--

DROP TABLE IF EXISTS `users`;
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
  MODIFY `uid` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
