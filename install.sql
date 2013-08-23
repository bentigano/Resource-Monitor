-- Generation Time: Aug 23, 2013 at 03:37 PM
-- Server version: 5.5.29
-- PHP Version: 5.4.10

-- --------------------------------------------------------

--
-- Table structure for table `alert_subscriptions`
--

CREATE TABLE `alert_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `monitoring_plan_id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `monitoring_plan_id` (`monitoring_plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `db_connections`
--

CREATE TABLE `db_connections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `dsn` varchar(1000) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `last_checked` datetime DEFAULT NULL,
  `last_result` tinyint(4) DEFAULT NULL,
  `last_error` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Triggers `db_connections`
--
DROP TRIGGER IF EXISTS `Add db connection to resource table`;
DELIMITER //
CREATE TRIGGER `Add db connection to resource table` AFTER INSERT ON `db_connections`
 FOR EACH ROW INSERT INTO resources (type, source_id)
VALUES (1, NEW.id)
//
DELIMITER ;
DROP TRIGGER IF EXISTS `DB connection cleanup`;
DELIMITER //
CREATE TRIGGER `DB connection cleanup` AFTER DELETE ON `db_connections`
 FOR EACH ROW BEGIN
DELETE FROM resources WHERE type = 1 AND source_id = OLD.id;
DELETE FROM queries WHERE which_db = OLD.id;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `monitoring_plans`
--

CREATE TABLE `monitoring_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `enabled` tinyint(4) DEFAULT NULL,
  `last_checked` datetime DEFAULT NULL,
  `run_mondays` tinyint(4) DEFAULT NULL,
  `run_tuesdays` tinyint(4) DEFAULT NULL,
  `run_wednesdays` tinyint(4) DEFAULT NULL,
  `run_thursdays` tinyint(4) DEFAULT NULL,
  `run_fridays` tinyint(4) DEFAULT NULL,
  `run_saturdays` tinyint(4) DEFAULT NULL,
  `run_sundays` tinyint(4) DEFAULT NULL,
  `frequency` int(11) DEFAULT NULL,
  `frequency_unit` varchar(50) DEFAULT NULL,
  `starting_at` varchar(5) DEFAULT NULL,
  `ending_at` varchar(5) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Triggers `monitoring_plans`
--
DROP TRIGGER IF EXISTS `Monitoring plan cleanup`;
DELIMITER //
CREATE TRIGGER `Monitoring plan cleanup` AFTER DELETE ON `monitoring_plans`
 FOR EACH ROW BEGIN
DELETE FROM alert_subscriptions WHERE monitoring_plan_id = OLD.id;
END
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `queries`
--

CREATE TABLE `queries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `which_db` int(11) NOT NULL,
  `query` varchar(2000) NOT NULL,
  `fail_count` int(11) NOT NULL,
  `last_checked` datetime DEFAULT NULL,
  `last_result` tinyint(4) DEFAULT NULL,
  `last_error` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Triggers `queries`
--
DROP TRIGGER IF EXISTS `Add query to resource table`;
DELIMITER //
CREATE TRIGGER `Add query to resource table` AFTER INSERT ON `queries`
 FOR EACH ROW INSERT INTO resources (type, source_id)
VALUES (2, NEW.id)
//
DELIMITER ;
DROP TRIGGER IF EXISTS `Remove query from resource table`;
DELIMITER //
CREATE TRIGGER `Remove query from resource table` AFTER DELETE ON `queries`
 FOR EACH ROW DELETE FROM resources WHERE type = 1 AND source_id = OLD.id
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `resources`
--

CREATE TABLE `resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  `source_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`,`source_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Triggers `resources`
--
DROP TRIGGER IF EXISTS `Resource cleanup`;
DELIMITER //
CREATE TRIGGER `Resource cleanup` AFTER DELETE ON `resources`
 FOR EACH ROW DELETE FROM monitoring_plans WHERE resource_id = OLD.id
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `soap_services`
--

CREATE TABLE `soap_services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `wsdl_location` varchar(255) NOT NULL,
  `last_checked` datetime DEFAULT NULL,
  `last_result` tinyint(4) DEFAULT NULL,
  `last_error` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Triggers `soap_services`
--
DROP TRIGGER IF EXISTS `Add soap service to resource table`;
DELIMITER //
CREATE TRIGGER `Add soap service to resource table` AFTER INSERT ON `soap_services`
 FOR EACH ROW INSERT INTO resources (type, source_id)
VALUES (3, NEW.id)
//
DELIMITER ;
DROP TRIGGER IF EXISTS `Remove soap service from resource table`;
DELIMITER //
CREATE TRIGGER `Remove soap service from resource table` AFTER DELETE ON `soap_services`
 FOR EACH ROW DELETE FROM resources WHERE type = 1 AND source_id = OLD.id
//
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `status_checks`
--

CREATE TABLE `status_checks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `datetime_checked` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `success` tinyint(4) NOT NULL,
  `error_details` varchar(2000) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;