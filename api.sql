
CREATE DATABASE `api`;
USE api
DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `ID` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `DOB` varchar(255) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `phone_numbers` varchar(255) NOT NULL,
  UNIQUE KEY `ID` (`ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



INSERT INTO `customers` (`ID`, `fullname`, `DOB`, `sex`, `phone_numbers`) VALUES
(1321321, 'nirsaban', '26-01-1996', 'male', '053-2898849'),
(12345678, 'nirsaban', '26-01-1996', 'male', '053-2898849'),
(123456789, 'aaaaaaaaaa', '26-01-1996', 'male', '053-2898849');
COMMIT;


