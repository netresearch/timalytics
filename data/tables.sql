CREATE TABLE IF NOT EXISTS `plusminus` (
  `pm_id` int(11) NOT NULL AUTO_INCREMENT,
  `pm_username` varchar(32) NOT NULL,
  `pm_year` int(11) NOT NULL,
  `pm_month` int(11) NOT NULL,
  `pm_minutes` int(11) DEFAULT NULL,
  `pm_minutes_absolute` int(11) NOT NULL,
  PRIMARY KEY (`pm_id`),
  UNIQUE KEY `pm_username` (`pm_username`,`pm_year`,`pm_month`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Übersicht der Plus/Minusstunden';

CREATE TABLE IF NOT EXISTS `users_contracts` (
  `uc_id` int(11) NOT NULL AUTO_INCREMENT,
  `uc_username` varchar(32) NOT NULL,
  `uc_start` date NULL,
  `uc_end` date NULL,
  `uc_hours_0` float not null default '0.0',
  `uc_hours_1` float not null default '8.0',
  `uc_hours_2` float not null default '8.0',
  `uc_hours_3` float not null default '8.0',
  `uc_hours_4` float not null default '8.0',
  `uc_hours_5` float not null default '8.0',
  `uc_hours_6` float not null default '0.0',
  PRIMARY KEY (`uc_id`),
  KEY (`uc_username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Arbeitsvertrag: Stunden pro Woche/Tag';
