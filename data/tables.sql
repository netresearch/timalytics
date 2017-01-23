CREATE DATABASE IF NOT EXISTS timalytics
  CHARACTER SET = utf8
  COLLATE utf8_bin;

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
