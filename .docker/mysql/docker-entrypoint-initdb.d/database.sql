create database IF NOT EXISTS gac_test;
use gac_test;

-- TABLE VEHICLE
CREATE TABLE IF NOT EXISTS `vehicle`
(
    `vehicle_id`   int(10) unsigned NOT NULL AUTO_INCREMENT,
    `plate_number` varchar(20)      NOT NULL COMMENT '',
    `brand`        varchar(100)     NOT NULL COMMENT '',
    `model`        varchar(150)     NOT NULL COMMENT '',
    PRIMARY KEY (`vehicle_id`),
    UNIQUE KEY `plate_number_idx` (`plate_number`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- TABLE EXPENSE
-- RELATION 1-N avec vehicle
CREATE TABLE IF NOT EXISTS `expense`
(
    `expense_id`     int(10) unsigned                                                 NOT NULL AUTO_INCREMENT,
    `vehicle_id`     int(10) unsigned                                                 NOT NULL,
    `expense_number` varchar(64)                                                      NOT NULL COMMENT '',
    `invoice_number` varchar(255)                                                     NOT NULL COMMENT '',
    `issued_on`      datetime                                                         NOT NULL,
    `category`       enum ('gasoline','diesel','electricity_charge','gpl','hydrogen') NOT NULL COMMENT '',
    `value_te`       decimal(10, 3)                                                   NOT NULL COMMENT '',
    `tax_rate`       decimal(5, 3)                                                    NOT NULL COMMENT '',
    `value_ti`       decimal(10, 3)                                                   NOT NULL COMMENT '',
    PRIMARY KEY (`expense_id`),
    KEY `vehicle_id_idx` (`vehicle_id`),
    UNIQUE KEY `expense_number_idx` (`expense_number`),
    CONSTRAINT `fk_vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicle` (`vehicle_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;

-- TABLE GAC_STATION
-- RELATION 1-1 avec expense
CREATE TABLE IF NOT EXISTS `gas_station`
(
    `gas_station_id`  int(10) unsigned NOT NULL,
    `expense_id`      int(10) unsigned NOT NULL,
    `description`     TEXT             NOT NULL COMMENT '',
    `coordinate`      point            NOT NULL COMMENT '',
    PRIMARY KEY (`gas_station_id`),
    UNIQUE (expense_id),
    CONSTRAINT `fk_expense_id` FOREIGN KEY (`expense_id`) REFERENCES `expense` (`expense_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;
