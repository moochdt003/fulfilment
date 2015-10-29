<?php

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

//table name
function fulfilment_setup_db() {
    custom_db_fulfilment_item_install();
    custom_db_fulfilment_install();
}

//function to create the fulfiment table					
function custom_db_fulfilment_install() {
    $sql = "CREATE TABLE IF NOT EXISTS moa_order_fulfilment  (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `order_id` int(11) NOT NULL,
                            `online_store` varchar(30) NOT NULL,
                            `inbound_carrier` varchar(30) NOT NULL,
                            `tracking_number` varchar(30) NOT NULL,
                            `invoice_amount` varchar(30) NOT NULL,
                            `created_user_id` int(11) NOT NULL,
                            `created_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
                          ) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;";

    dbDelta($sql);
}

//function to create the fulfiment table
function custom_db_fulfilment_item_install() {
    $sql = "CREATE TABLE IF NOT EXISTS moa_fulfilment_order_item (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `fulfilment_id` int(11) NOT NULL,
                            `order_item_id` int(11) NOT NULL,
                            `quantity` int(11) NOT NULL,
                            `item_price` float NOT NULL,
                            `ship_date` timestamp NOT NULL,
                            PRIMARY KEY (`id`),
                             CONSTRAINT `fulfilments` FOREIGN KEY (`fulfilment_id`) REFERENCES `moa_local`.`moa_order_fulfilment`(`id`)
                            ) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
    dbDelta($sql);
}
