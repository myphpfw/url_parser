CREATE TABLE IF NOT EXISTS `mpf_routes` (
  `ID` bigint(20) NOT NULL AUTO_INCREMENT,
  `uri` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `path` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `URI` (`uri`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;
