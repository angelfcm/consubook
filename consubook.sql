-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Versión del servidor:         5.5.5-10.0.16-MariaDB - mariadb.org binary distribution
-- SO del servidor:              Win64
-- HeidiSQL Versión:             8.3.0.4694
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

-- Volcando estructura de base de datos para consubook
CREATE DATABASE IF NOT EXISTS `consubook` /*!40100 DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci */;
USE `consubook`;


-- Volcando estructura para tabla consubook.cbk_books
CREATE TABLE IF NOT EXISTS `cbk_books` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `author` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `editorial` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `year` year(4) DEFAULT NULL,
  `edition` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_category` int(11) unsigned DEFAULT NULL,
  `id_book_image` int(10) unsigned DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_cbk_books_cbk_books_categories` (`id_category`),
  KEY `FK_cbk_books_cbk_books_images` (`id_book_image`),
  FULLTEXT KEY `author` (`author`),
  FULLTEXT KEY `editorial` (`editorial`),
  FULLTEXT KEY `titulo` (`title`),
  CONSTRAINT `FK_cbk_books_cbk_books_categories` FOREIGN KEY (`id_category`) REFERENCES `cbk_books_categories` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `FK_cbk_books_cbk_books_images` FOREIGN KEY (`id_book_image`) REFERENCES `cbk_books_images` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_books: ~20 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_books` DISABLE KEYS */;
INSERT INTO `cbk_books` (`id`, `title`, `author`, `editorial`, `year`, `edition`, `id_category`, `id_book_image`, `created_at`, `modified_at`) VALUES
	(1, 'Mate para noobs', 'Angel Carriola', 'Editorial 1', '2014', NULL, 5, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(2, 'Vectorial para tronkos', 'Cecilia Pérez', 'Editorial 2', '2013', NULL, 3, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(3, 'Tontules X', 'Angel Carriola', 'Editorial 2', '2013', NULL, 3, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(4, 'Maestra tronka', 'Autor 2', 'Editorial 2', '2013', NULL, 5, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(5, 'Libro xD', 'Cecilia Pérez', 'Editorial 2', '2013', NULL, 11, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(6, 'Libro Extraño 2', 'Autor 2', 'Editorial 2', '2013', NULL, 5, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(7, 'Cómo salir de Bronza 3', 'Angel Carriola', 'Editorial 2', '2013', NULL, 2, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(8, 'Cómo dejar de fedear', 'Angel Carriola', 'Editorial 2', '2013', NULL, 8, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(9, 'Estilos aguacate', 'Cecilia Pérez', 'Editorial 2', '2013', NULL, 1, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(10, 'Tengo miedo', 'Angel Carriola', 'Editorial 2', '2013', NULL, 1, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(11, 'Jajaja el Libro', 'Cecilia Pérez', 'Editorial 2', '2013', NULL, 16, 1, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(12, 'Libro de LoL', 'Autor 2', 'Editorial 2', '2013', NULL, 17, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(13, 'El libro de carcajadas', 'Autor 2', 'Editorial 2', '2013', NULL, 12, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(14, 'Un libro para locos', 'Autor 2', 'Editorial 2', '2013', NULL, 2, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(15, 'No veas este libro', 'Autor 2', 'Editorial 2', '2013', NULL, 11, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(16, 'Cuando la noche no es noche', 'Autor 2', 'Editorial 2', '2013', NULL, 7, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(17, 'Libro para libros', 'Autor 2', 'Editorial 2', '2013', NULL, 6, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(18, 'Libro Extraño 1', 'Autor 2', 'Editorial 2', '2013', NULL, 6, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(19, 'Libro de má', 'Autor 2', 'Editorial 2', '2013', NULL, 6, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16'),
	(20, 'Mi libro especial', 'Autor 2', 'Editorial 2', '2013', NULL, 9, NULL, '2015-05-23 14:29:15', '2015-05-23 14:29:16');
/*!40000 ALTER TABLE `cbk_books` ENABLE KEYS */;


-- Volcando estructura para tabla consubook.cbk_books_categories
CREATE TABLE IF NOT EXISTS `cbk_books_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `cbk_books_categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `cbk_books_categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_books_categories: ~17 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_books_categories` DISABLE KEYS */;
INSERT INTO `cbk_books_categories` (`id`, `name`, `code`, `parent_id`) VALUES
	(1, 'Generalidades', '000', NULL),
	(2, 'Conocimiento', '001', 1),
	(3, 'El libro', '002', 1),
	(4, 'Subcategoría Libro', '002.1', 3),
	(5, 'Bibliografía', '010', 1),
	(6, 'Sistemas', '003', 1),
	(7, 'Bibliografías', '011', 5),
	(8, 'Filosofía y Psicología', '100', NULL),
	(9, 'Ciencias sociales', '300', NULL),
	(10, 'Lenguas', '400', NULL),
	(11, 'Ciencias naturales y matemáticas', '500', NULL),
	(12, 'Tecnología (Ciencias aplicadas)', '600', NULL),
	(13, 'Las Artes. Bellas artes y artes decorativas', '700', NULL),
	(14, 'Literatura y retórica', '800', NULL),
	(15, 'Geografía e Historia', '900', NULL),
	(16, 'Religión', '200', NULL),
	(17, 'Subcategoria Libro Libro', '002.11', 4);
/*!40000 ALTER TABLE `cbk_books_categories` ENABLE KEYS */;


-- Volcando estructura para tabla consubook.cbk_books_copies
CREATE TABLE IF NOT EXISTS `cbk_books_copies` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_book` int(10) unsigned NOT NULL,
  `isbn` varchar(13) COLLATE utf8_unicode_ci DEFAULT NULL,
  `inventary_code` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `available` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `isbn` (`isbn`),
  UNIQUE KEY `inventary_code` (`inventary_code`),
  KEY `id_book` (`id_book`),
  CONSTRAINT `cbk_books_copies_ibfk_1` FOREIGN KEY (`id_book`) REFERENCES `cbk_books` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_books_copies: ~2 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_books_copies` DISABLE KEYS */;
INSERT INTO `cbk_books_copies` (`id`, `id_book`, `isbn`, `inventary_code`, `available`) VALUES
	(1, 2, '123123123', '2455555', 1),
	(2, 2, '332432423', '2455556', 0);
/*!40000 ALTER TABLE `cbk_books_copies` ENABLE KEYS */;


-- Volcando estructura para tabla consubook.cbk_books_images
CREATE TABLE IF NOT EXISTS `cbk_books_images` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `image` mediumblob,
  `extension` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `size` int(11) DEFAULT NULL,
  `width` int(11) DEFAULT NULL,
  `height` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_books_images: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_books_images` DISABLE KEYS */;
INSERT INTO `cbk_books_images` (`id`, `image`, `extension`, `size`, `width`, `height`, `created_at`, `modified_at`) VALUES
/*!40000 ALTER TABLE `cbk_books_images` ENABLE KEYS */;


-- Volcando estructura para tabla consubook.cbk_user_account
CREATE TABLE IF NOT EXISTS `cbk_user_account` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `firstname` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `lastname` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gender` enum('male','female','unspecified') COLLATE utf8_unicode_ci DEFAULT 'unspecified',
  `age` tinyint(3) unsigned DEFAULT NULL,
  `phone` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  `address` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `user_role_id` int(11) DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `user_role_id` (`user_role_id`),
  CONSTRAINT `cbk_user_account_ibfk_1` FOREIGN KEY (`user_role_id`) REFERENCES `cbk_user_role` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_user_account: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_user_account` DISABLE KEYS */;
INSERT INTO `cbk_user_account` (`id`, `username`, `email`, `password`, `firstname`, `lastname`, `gender`, `age`, `phone`, `address`, `user_role_id`, `created_at`, `modified_at`) VALUES
	(9, 'angelfcm', 'angelfcm1@gmail.com', '$2a$08$yPURIxoh6XKs4ioWvZA9SOeCxkqUNLWgKmyu2O.8YhRMgQtcEGzQO', 'angel', 'carriola', 'male', NULL, '3338448782', NULL, 2, '2015-05-17 01:11:28', NULL),
	(10, 'CecyPerez', 'cecilia_perez03@hotmail.com', '$2a$08$dyzlVaz1Sp1k9eOWnLpXved2DPUHyyqWEGfWKoMA0u2b.OAl0wsza', 'Cecilia', 'Perez', 'female', NULL, '3316060425', NULL, 2, '2015-05-17 10:16:08', NULL),
	(11, 'Eduardo', 'bugueados@gmail.com', '$2a$08$7MavSjcFAQyYczGdsRAAtuUJh6qJTeB/JR2YyOd6RH7za7FCgt0pe', 'Eddy', 'Eddy', 'male', NULL, '3319553511', NULL, 2, '2015-05-22 16:16:14', NULL);
/*!40000 ALTER TABLE `cbk_user_account` ENABLE KEYS */;


-- Volcando estructura para tabla consubook.cbk_user_confirmation
CREATE TABLE IF NOT EXISTS `cbk_user_confirmation` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `account_id` int(10) unsigned NOT NULL,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `confirmed` tinyint(1) DEFAULT '0',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `modified_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `account_id` (`account_id`),
  UNIQUE KEY `code` (`code`),
  CONSTRAINT `cbk_user_confirmation_ibfk_1` FOREIGN KEY (`account_id`) REFERENCES `cbk_user_account` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_user_confirmation: ~3 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_user_confirmation` DISABLE KEYS */;
INSERT INTO `cbk_user_confirmation` (`id`, `account_id`, `code`, `confirmed`, `created_at`, `modified_at`) VALUES
	(9, 9, '03def444709ac009763de9bfd518f767', 1, '2015-05-17 01:11:29', '2015-05-17 01:11:41'),
	(10, 10, 'a56914d31b9772defd607c901dddce4d', 1, '2015-05-17 10:16:08', '2015-05-17 10:17:23'),
	(11, 11, '9a679911fdfff35f374da0fa1ba9a7a7', 1, '2015-05-22 16:16:14', '2015-05-22 16:18:01');
/*!40000 ALTER TABLE `cbk_user_confirmation` ENABLE KEYS */;


-- Volcando estructura para tabla consubook.cbk_user_resources
CREATE TABLE IF NOT EXISTS `cbk_user_resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `action` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_user_resources: ~4 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_user_resources` DISABLE KEYS */;
INSERT INTO `cbk_user_resources` (`id`, `controller`, `action`) VALUES
	(1, 'Index', 'index'),
	(2, 'User', 'index'),
	(3, 'User', 'singup'),
	(4, 'User', 'login');
/*!40000 ALTER TABLE `cbk_user_resources` ENABLE KEYS */;


-- Volcando estructura para tabla consubook.cbk_user_role
CREATE TABLE IF NOT EXISTS `cbk_user_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `permission` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.cbk_user_role: ~2 rows (aproximadamente)
/*!40000 ALTER TABLE `cbk_user_role` DISABLE KEYS */;
INSERT INTO `cbk_user_role` (`id`, `name`, `permission`) VALUES
	(1, 'Guest', 'a:3:{i:0;a:3:{s:10:"controller";s:4:"User";s:6:"action";s:6:"singup";s:5:"allow";b:1;}i:1;a:3:{s:10:"controller";s:5:"Index";s:6:"action";s:5:"index";s:5:"allow";b:1;}i:2;a:3:{s:10:"controller";s:4:"User";s:6:"action";s:5:"login";s:5:"allow";i:1;}}'),
	(2, 'Account', '');
/*!40000 ALTER TABLE `cbk_user_role` ENABLE KEYS */;


-- Volcando estructura para procedimiento consubook.deleteUnconfirmedAccounts
DELIMITER //
CREATE DEFINER=`root`@`localhost` PROCEDURE `deleteUnconfirmedAccounts`()
BEGIN
	DELETE user.*  FROM 
		cbk_user_account as user 
		INNER JOIN cbk_user_confirmation as confirm 
		ON user.id = confirm.account_id  
	WHERE DATE_ADD(confirm.created_at, INTERVAL 2 DAY) < NOW();
END//
DELIMITER ;


-- Volcando estructura para evento consubook.deleteUnconfirmedAccounts
DELIMITER //
CREATE DEFINER=`root`@`localhost` EVENT `deleteUnconfirmedAccounts` ON SCHEDULE EVERY 1 HOUR STARTS '2015-05-14 00:41:06' ON COMPLETION PRESERVE ENABLE DO BEGIN

	CALL deleteUnconfirmedAccounts ();

END//
DELIMITER ;


-- Volcando estructura para tabla consubook.test
CREATE TABLE IF NOT EXISTS `test` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `file` mediumblob,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Volcando datos para la tabla consubook.test: ~1 rows (aproximadamente)
/*!40000 ALTER TABLE `test` DISABLE KEYS */;
INSERT INTO `test` (`id`, `file`) VALUES
/*!40000 ALTER TABLE `test` ENABLE KEYS */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;