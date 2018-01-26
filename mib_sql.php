CREATE TABLE mib_locations (
	`mib_locations_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`mib_location_name` VARCHAR(50) NULL DEFAULT '0',
	`mib_location_type_fk` INT(11) NULL DEFAULT '0',
	`mib_location_address1` VARCHAR(50) NULL DEFAULT NULL,
	`mib_location_address2` VARCHAR(50) NULL DEFAULT NULL,
	`mib_location_town` VARCHAR(50) NULL DEFAULT NULL,
	`mib_location_county` VARCHAR(50) NULL DEFAULT NULL,
	`mib_location_postcode` CHAR(10) NULL DEFAULT NULL,
	`mib_location_phone` CHAR(15) NULL DEFAULT NULL,
	`mib_location_contact1` CHAR(25) NULL DEFAULT NULL,
	`mib_location_contact2` CHAR(25) NULL DEFAULT NULL,
	`mib_location_willing` TINYINT(3) NULL DEFAULT '0',
    `mib_location_comments` TEXT NULL,
	`mib_location_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	`mib_location_lastupdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`mib_locations_id`)
)ENGINE=MyISAM;
CREATE TABLE mib_bottles (
	`mib_bottles_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`mib_bottles_location_fk` INT(10) UNSIGNED NULL DEFAULT '0',
	`mib_bottles_date` INT(10) UNSIGNED NULL DEFAULT '0',
	`mib_bottles_action_fk` INT(10) UNSIGNED NULL DEFAULT '0',
	`mib_bottles_quantity` INT(10) UNSIGNED NULL DEFAULT '0',
	`mib_bottles_comments` TEXT NULL,
	`mib_bottles_user` VARCHAR(200) NULL DEFAULT NULL,
	`mib_bottles_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	`mib_bottles_lastupdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`mib_bottles_id`)
)ENGINE=MyISAM;
CREATE TABLE mib_action (
	`mib_action_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`mib_action_action` VARCHAR(100) NULL DEFAULT NULL,
	`mib_action_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	`mib_action_lastupdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`mib_action_id`)
)ENGINE=MyISAM;
CREATE TABLE mib_type (
	`mib_type_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`mib_type_name` CHAR(50) NULL DEFAULT NULL,
	`mib_type_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
	`mib_type_updated` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
	PRIMARY KEY (`mib_type_id`)
)ENGINE=MyISAM;