SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	`content` longtext NOT NULL,
	`author_id` int unsigned NOT NULL,
	PRIMARY KEY (`id`),
	KEY (`author_id`),
	FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

INSERT INTO `articles` (`id`, `title`, `content`, `author_id`) VALUES
(1,	'Little Red Cap',	'Once upon a time there was a sweet little girl. Everyone who saw her liked her, but most of all her grandmother, who did not know what to give the child next. Once she gave her a little cap made of red velvet. Because it suited her so well, and she wanted to wear it all the time, she came to be known as Little Red Cap. One day her mother said to her, “Come Little Red Cap. Here is a piece of cake and a bottle of wine. Take them to your grandmother. She is sick and weak, and they will do her well. Mind your manners and give her my greetings. Behave yourself on the way, and do not leave the path, or you might fall down and break the glass, and then there will be nothing for your sick grandmother.”',	1),
(2,	'Cinderella',	'There was once a rich man whose wife lay sick, and when she felt her end drawing near she called to her only daughter to come near her bed, and said, “Dear child, be pious and good, and God will always take care of you, and I will look down upon you from heaven, and will be with you.” And then she closed her eyes and expired. The maiden went every day to her mother’s grave and wept, and was always pious and good. When the winter came the snow covered the grave with a white covering, and when the sun came in the early spring and melted it away, the man took to himself another wife.',	2);

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
	`id` int unsigned NOT NULL AUTO_INCREMENT,
	`name` varchar(255) NOT NULL,
	`password` char(40) NOT NULL,
	`email` varchar(255) NOT NULL,
	`firstname` varchar(255) NOT NULL,
	`surname` varchar(255) NOT NULL,
	PRIMARY KEY (`id`),
	UNIQUE KEY (`name`)
) ENGINE=InnoDB;

INSERT INTO `users` (`id`, `name`, `password`, `email`, `firstname`, `surname`) VALUES
(1,	'demo',	'b60d121b438a380c343d5ec3c2037564b82ffef3',	'demo@example.com',	'John',	'Doe'),
(2,	'demo2',	'b60d121b438a380c343d5ec3c2037564b82ffef3',	'demo@example.com',	'John',	'Doe'),
(3,	'duplicate',	'b60d121b438a380c343d5ec3c2037564b82ffef3',	'demo@example.com',	'John',	'Doe');
