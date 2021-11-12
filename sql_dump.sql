SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

CREATE TABLE `f_pages` (
  `id` int(10) UNSIGNED NOT NULL,
  `index` varchar(100) NOT NULL,
  `url` varchar(250) NOT NULL,
  `level` int(10) UNSIGNED NOT NULL DEFAULT '1',
  `title` varchar(250) NOT NULL,
  `link` varchar(100) NOT NULL,
  `keywords` text NOT NULL,
  `description` text NOT NULL,
  `content` longtext NOT NULL,
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `parent` int(10) UNSIGNED NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `f_pages` (`id`, `index`, `url`, `level`, `title`, `link`, `keywords`, `description`, `content`, `modified`, `parent`, `order`) VALUES
(1, '', '', 0, 'Главная страница', 'Главная', '', '', '<p>Какой-то текст на главной странице</p>', '2021-01-01 00:00:00', 0, 1),
(2, 'test1', 'test1', 1, 'Тестовая страница 1', 'Тест 1', '', '', '<p>Тестовая страница 1 — текст</p>', '2021-01-01 00:00:00', 0, 2),
(3, 'test2', 'test2', 1, 'Тестовая страница 2', 'Тест 2', '', '', '<p>Тестовая страница 2 — текст</p>', '2021-01-01 00:00:00', 0, 3);

CREATE TABLE `f_values` (
  `id` int(10) UNSIGNED NOT NULL,
  `index` varchar(250) NOT NULL,
  `value` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `f_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index` (`index`,`parent`),
  ADD UNIQUE KEY `url` (`url`),
  ADD KEY `parent` (`parent`),
  ADD KEY `order` (`order`);

ALTER TABLE `f_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `index` (`index`);

ALTER TABLE `f_pages`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

ALTER TABLE `f_values`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;
