CREATE TABLE `account_info` (
  `id` int(11) UNSIGNED NOT NULL,
  `account` varchar(32) NOT NULL,
  `name` varchar(255) NOT NULL,
  `gender` int(1) UNSIGNED NOT NULL DEFAULT '1',
  `birthday` date NOT NULL,
  `email` varchar(255) NOT NULL,
  `note` text NOT NULL,
  `create_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modify_time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `delete_time` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;