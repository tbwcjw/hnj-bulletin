CREATE TABLE `boards` (
  `num` int unsigned NOT NULL AUTO_INCREMENT,
  `link` varchar(16) NOT NULL,
  `title` varchar(32) NOT NULL,
  `description` varchar(128) NOT NULL,
  `banner` varchar(255) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
CREATE TABLE `banned` (
  `num` int unsigned NOT NULL AUTO_INCREMENT,
  `ip` varchar(255) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `alias` varchar(255) DEFAULT NULL,
  `datetime` timestamp NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
CREATE TABLE `replies` (
  `num` int unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(64) NOT NULL,
  `board` varchar(255) NOT NULL,
  `parent_id` varchar(64) NOT NULL,
  `poster` varchar(255) NOT NULL,
  `content` blob NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
CREATE TABLE `rules` (
  `num` int unsigned NOT NULL AUTO_INCREMENT,
  `board` varchar(32) NOT NULL DEFAULT 'global',
  `title` varchar(255) NOT NULL,
  `content` varchar(255) NOT NULL,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci
CREATE TABLE `threads` (
  `num` int unsigned NOT NULL AUTO_INCREMENT,
  `id` varchar(64) NOT NULL,
  `board` varchar(255) NOT NULL,
  `poster` varchar(255) NOT NULL DEFAULT 'Anonymous',
  `subject` varchar(255) NOT NULL,
  `content` blob NOT NULL,
  `magnet` varchar(255) NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`num`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci