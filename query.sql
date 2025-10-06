CREATE TABLE `eltee_dmcc`.`port_master` ( `port_master_id` INT NOT NULL AUTO_INCREMENT , `port_name` VARCHAR(100) NULL DEFAULT NULL , `port_address` TEXT NULL DEFAULT NULL , `portShipment_Type` VARCHAR(50) NULL DEFAULT NULL , `country_id` INT NOT NULL , `status` ENUM('Active', 'Deactive') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Active' , PRIMARY KEY (`port_master_id`)) ENGINE = InnoDB;

ALTER TABLE `pro_forma_head` ADD `time_of_shipment` VARCHAR(150) NULL DEFAULT NULL AFTER `bank_detail_id`, ADD `country_of_origin` INT NULL DEFAULT NULL AFTER `time_of_shipment`, ADD `country_of_supply` INT NULL DEFAULT NULL AFTER `country_of_origin`, ADD `port_id` INT NULL DEFAULT NULL AFTER `country_of_supply`, ADD `part_shipment` VARCHAR(150) NULL DEFAULT NULL AFTER `port_id`, ADD `trans_shipment` VARCHAR(150) NULL DEFAULT NULL AFTER `part_shipment`, ADD `insurance` VARCHAR(150) NULL DEFAULT NULL AFTER `trans_shipment`, ADD `marking` VARCHAR(150) NULL DEFAULT NULL AFTER `insurance`, ADD `packing` VARCHAR(150) NULL DEFAULT NULL AFTER `marking`;

ALTER TABLE `pro_forma_head` CHANGE `port_id` `port_of_loading` INT(11) NULL DEFAULT NULL;

ALTER TABLE `pro_forma_head` ADD `destination_port` INT NULL AFTER `port_of_loading`;

ALTER TABLE `pro_forma_head` ADD `port_of_loading_name` VARCHAR(150) NULL AFTER `destination_port`, ADD `destination_port_name` VARCHAR(150) NULL AFTER `port_of_loading_name`;

ALTER TABLE `terms_conditions` ADD `term_type` VARCHAR(50) NULL AFTER `terms_id`;

CREATE TABLE `eltee_dmcc`.`shipment_document` ( `shipment_document_id` INT NOT NULL AUTO_INCREMENT , `shipment_document_name` VARCHAR(200) NULL , `status` ENUM('Active','Deactive') CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT 'Active' , PRIMARY KEY (`shipment_document_id`)) ENGINE = InnoDB;

CREATE TABLE `eltee_dmcc`.`pro_forma_head_shipment_detail` ( `pi_shipments_detail_id` INT NOT NULL AUTO_INCREMENT , `pi_no` INT NULL , `shipment_document_name` VARCHAR(150) NULL , PRIMARY KEY (`pi_shipments_detail_id`)) ENGINE = InnoDB;

CREATE TABLE `eltee_dmcc`.`pro_forma_head_termcondition_detail` ( `pi_term_detail_id` INT NOT NULL AUTO_INCREMENT , `pi_no` INT NULL , `terms_id` INT NULL , `title` VARCHAR(255) NULL , `discription` TEXT NULL , PRIMARY KEY (`pi_term_detail_id`)) ENGINE = InnoDB;

ALTER TABLE `payment_terms` CHANGE `payment_term` `payment_term` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE `pro_forma_head` ADD `port_id` INT NULL AFTER `packing`, ADD `country_name` VARCHAR(250) NULL AFTER `port_id`, ADD `incoterm` VARCHAR(250) NULL AFTER `country_name`;

CREATE TABLE `eltee_dmcc`.`incoterms_master` ( `incoterms_id` INT NOT NULL AUTO_INCREMENT , `incoterms` VARCHAR(50) NULL , `incoterms_fullform` VARCHAR(250) NULL , `status` INT NOT NULL DEFAULT '1' , PRIMARY KEY (`incoterms_id`)) ENGINE = InnoDB;

INSERT INTO `incoterms_master` (`incoterms_id`, `incoterms`, `incoterms_fullform`, `status`) VALUES (NULL, 'EXW', 'Ex Works (insert place of delivery)', '1'), (NULL, 'FCA', 'Free Carrier (Insert named place of delivery)', '1');

INSERT INTO `incoterms_master` (`incoterms_id`, `incoterms`, `incoterms_fullform`, `status`) VALUES (NULL, 'CPT', 'Carriage Paid to (insert place of destination)', '1'), (NULL, 'CIP', 'Carriage and Insurance Paid To (insert place of destination)', '1'), (NULL, 'DAP', 'Delivered at Place (insert named place of destination)', '1'),(NULL, 'DPU', 'Delivered at Place Unloaded (insert of place of destination)', '1'),(NULL, 'DDP', 'Delivered Duty Paid (Insert place of destination)', '1'),(NULL, 'FAS', 'Free Alongside Ship (insert name of port of loading)', '1'),(NULL, 'FOB', 'Free on Board (insert named port of loading)', '1'),(NULL, 'CFR', 'Cost and Freight (insert named port of destination)', '1'),(NULL, 'CIF', 'Cost Insurance and Freight (insert named port of destination)', '1')

ALTER TABLE `pro_forma_head` CHANGE `incoterm` `incoterms_id` INT NULL DEFAULT NULL;


UPDATE `all_submodule` SET `module_id` = '0' WHERE `all_submodule`.`submodule_id` = 9
UPDATE `all_submodule` SET `module_id` = '0' WHERE `all_submodule`.`submodule_id` = 17
UPDATE `all_submodule` SET `module_id` = '0' WHERE `all_submodule`.`submodule_id` = 19


CREATE TABLE `eltee_dmcc`.`purchase_order` ( `po_id` INT NOT NULL AUTO_INCREMENT , `supplier_id` INT NULL , `supplier_pi_no` INT NULL , `supplier_pi_date` DATE NULL , `po_date` DATE NULL , `valid_upto` DATE NULL , `currency_id` INT NULL , `state_id` INT NULL , `country_id` INT NULL , `total_amt` BIGINT NULL , `discount` INT NULL , `grand_total` BIGINT NULL , `remark` TEXT NULL , PRIMARY KEY (`po_id`)) ENGINE = InnoDB;

CREATE TABLE `eltee_dmcc`.`purchase_order_termscondition_detail` ( `po_term_detail_id` INT NOT NULL AUTO_INCREMENT , `purchase_order_id` INT NULL , `term_id` INT NULL , `title` VARCHAR(255) NULL , `discription` TEXT NULL , PRIMARY KEY (`po_term_detail_id`)) ENGINE = InnoDB;

CREATE TABLE `eltee_dmcc`.`purchase_order_details` ( `po_details_id` int(11) NOT NULL, `purchase_order_id` int(11) NOT NULL, `product_id` int(11) NOT NULL, `each_bag_weight` varchar(100) DEFAULT NULL, `no_of_bags` varchar(100) DEFAULT NULL, `total_weight` varchar(100) DEFAULT NULL, `rate` varchar(100) DEFAULT NULL, `rateperton` float NOT NULL, `packaging_type` text NOT NULL, `gst` varchar(150) DEFAULT NULL, `trade_discount` float NOT NULL DEFAULT 0, `cash_discount` float NOT NULL DEFAULT 0, `total_amt` varchar(100) DEFAULT NULL, `packaging_id` int(11) NOT NULL ) ENGINE=InnoDB DEFAULT CHARSET=latin1

ALTER TABLE `purchase_order_details` ADD PRIMARY KEY(`po_details_id`);

ALTER TABLE `purchase_order` ADD `user_id` INT NULL AFTER `remark`, ADD `comp_id` INT NULL AFTER `user_id`, ADD `branch_id` INT NULL AFTER `comp_id`;

CREATE TABLE `eltee_dmcc`.`purchase_order_payment` ( `purchase_order_payment_id` INT NOT NULL AUTO_INCREMENT , `purchase_order_id` INT NULL , `pay_percentage` VARCHAR(50) NULL , `pay_in_advance` VARCHAR(50) NULL , `after_percentage` INT NULL , `pay_later` VARCHAR(50) NULL , `payment_desc_id` INT NULL , `after_payment_desc_id` INT NULL , `payment_mode_id` INT NULL , `time_period` VARCHAR(30) NULL , PRIMARY KEY (`purchase_order_payment_id`)) ENGINE = InnoDB;

ALTER TABLE `purchase_order` CHANGE `supplier_id` `supplier_id` VARCHAR(50) NULL DEFAULT NULL;

ALTER TABLE `purchase_order` ADD `purchase_order_status` INT NOT NULL DEFAULT '0' AFTER `branch_id`;

INSERT INTO `all_modules` (`module_id`, `module_name`, `module_url`, `icon`, `sequence`) VALUES (NULL, 'Purchase', NULL, '<i class=\"fa fa-shopping-cart\" aria-hidden=\"true\"></i>', '4');

INSERT INTO `all_submodule` (`submodule_id`, `submodule_name`, `submodule_url`, `module_id`, `sequence`) VALUES (NULL, 'Purchase Order Requests', 'purchase_order_requests.php', '7', '1'), (NULL, 'Purchase Order Invoice', 'purchase_orders.php', '7', '2');

INSERT INTO `all_submodule` (`submodule_id`, `submodule_name`, `submodule_url`, `module_id`, `sequence`) VALUES (NULL, 'Deactive Purchase Order', 'deactive_purchase_orders.php', '7', '3');

ALTER TABLE `purchase_order` CHANGE `supplier_pi_no` `supplier_pi_no` VARCHAR(50) NULL DEFAULT NULL;

ALTER TABLE `purchase_order` CHANGE `supplier_id` `supplier_id` INT NULL DEFAULT NULL;

ALTER TABLE `purchase_order_details` CHANGE `po_details_id` `po_details_id` INT(11) NOT NULL AUTO_INCREMENT;

ALTER TABLE `purchase_order` ADD `active_status` INT NOT NULL DEFAULT '1' AFTER `purchase_order_status`, ADD `deactivate_date` DATETIME NULL AFTER `active_status`, ADD `deactivate_reason` TEXT NULL AFTER `deactivate_date`, ADD `deactivate_user` INT NOT NULL DEFAULT '0' AFTER `deactivate_reason`, ADD `added_datetime` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `deactivate_user`;


INSERT INTO `payment_description` (`payment_desc_id`, `payment_description`) VALUES (NULL, 'After');
INSERT INTO `payment_description` (`payment_desc_id`, `payment_description`) VALUES (NULL, 'Before');