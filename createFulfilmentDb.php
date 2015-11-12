<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

//table name
function fulfilment_setup_db() {
    custom_db_fulfilment_item_install();
    custom_db_fulfilment_install();
    custom_store_fulfilment_object();
    
}

//function to create the fulfiment table					
function custom_db_fulfilment_install() {
    $sql = "CREATE TABLE IF NOT EXISTS moa_order_fulfilment  (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `order_id` int(11) NOT NULL,
                            `online_store` varchar(30) NOT NULL,
                            `inbound_carrier` varchar(30) NOT NULL,
                            `carrier_tracking_number` varchar(30) NOT NULL,
                            `invoice_amount` numeric(15,2) NOT NULL,
                            `created_user_id` int(11) NOT NULL,
                            `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `edit_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `moa_tracking_number` varchar(30) NOT NULL,
                            `sent_user_id` int(11) NULL,
                            `sent_date` timestamp NULL,
                            
                            PRIMARY KEY (`id`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;";

    dbDelta($sql);
}

//function to create the fulfiment table
function custom_db_fulfilment_item_install() {
    $sql = "CREATE TABLE IF NOT EXISTS moa_order_items (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `fulfilment_id` int(11) NOT NULL,
                            `order_item_id` int(11) NOT NULL,
                            `quantity` int(11) NOT NULL,
                            `item_price` numeric(15,2) NOT NULL,
                            `ship_date` timestamp NOT NULL,
                            PRIMARY KEY (`id`),
                             CONSTRAINT `fulfilments` FOREIGN KEY (`fulfilment_id`) REFERENCES `moa_local`.`moa_order_fulfilment`(`id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    dbDelta($sql);
}

//function to create the fulfiment table
function custom_store_fulfilment_object() {
            $sql = "CREATE TABLE IF NOT EXISTS `moa_elogix_api_log` (
                        `id` int(11) NOT NULL AUTO_INCREMENT,
                        `fulfilment_id` int(11) NOT NULL,
                        `request` varchar(2500) NOT NULL,
                        `request_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                        `response` varchar(2500) NULL,
                        `response_date` timestamp NULL,
                        `error` varchar(2500) NULL,
                        PRIMARY KEY (`id`),
                        KEY `fulfilment_id` (`fulfilment_id`)
                      ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
    dbDelta($sql);
}




