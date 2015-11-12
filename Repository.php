<?php

include_once('FulfilmentObj.php');
include_once('FulfilmentItemObj.php');
include_once('WooHelper.php');

class Repository {

    static function fetch_fulfilments_for_order($order_id) {
        global $wpdb;

        $fulfilmentObjs = array();
        $fulfilments = $wpdb->get_results("SELECT * FROM `moa_order_fulfilment` where `order_id` = $order_id ");

        foreach ($fulfilments as $fulfilment) {
            $fulfilmentObj = new FulfilmentObj();
            $fulfilmentObj->id = $fulfilment->id;
            $fulfilmentObj->order_id = $fulfilment->order_id;
            $fulfilmentObj->online_store = $fulfilment->online_store;
            $fulfilmentObj->inbound_carrier = $fulfilment->inbound_carrier;
            $fulfilmentObj->carrier_tracking_number = $fulfilment->carrier_tracking_number;
            $fulfilmentObj->invoice_amount = $fulfilment->invoice_amount;
            $fulfilmentObj->created_user_id = $fulfilment->created_user_id;
            $fulfilmentObj->created_date = $fulfilment->created_date;
            $fulfilmentObj->items = self::fetch_fulfilment_items($fulfilmentObj->id);

            $fulfilmentObjs[] = $fulfilmentObj;
        }

        return $fulfilmentObjs;
    }

    static function fetch_fulfilment($fulfilment_id) {
        global $wpdb;

        $fulfilment = $wpdb->get_row("SELECT * FROM `moa_order_fulfilment` where `id` = $fulfilment_id ");

        $fulfilmentObj = new FulfilmentObj();
        $fulfilmentObj->id = $fulfilment->id;
        $fulfilmentObj->order_id = $fulfilment->order_id;
        $fulfilmentObj->online_store = $fulfilment->online_store;
        $fulfilmentObj->inbound_carrier = $fulfilment->inbound_carrier;
        $fulfilmentObj->carrier_tracking_number = $fulfilment->carrier_tracking_number;
        $fulfilmentObj->invoice_amount = $fulfilment->invoice_amount;
        $fulfilmentObj->created_user_id = $fulfilment->created_user_id;
        $fulfilmentObj->created_date = $fulfilment->created_date;
        $fulfilmentObj->items = self::fetch_fulfilment_items($fulfilmentObj->id);

        return $fulfilmentObj;
    }

    static function fetch_fulfilment_items($fulfilment_id) {
        global $wpdb;

        $fulfilment_items = $wpdb->get_results("SELECT * FROM `moa_order_items` where `fulfilment_id` = $fulfilment_id");
        $fulfilment_item_objs = array();

        foreach ($fulfilment_items as $fulfilment_item) {
            $fulfilmentItemObj = new FulfilmentItemObj();

            $fulfilmentItemObj->id = $fulfilment_item->id;
            $fulfilmentItemObj->fulfilment_id = $fulfilment_item->fulfilment_id;
            $fulfilmentItemObj->order_item_id = $fulfilment_item->order_item_id;
            $fulfilmentItemObj->quantity = $fulfilment_item->quantity;
            $fulfilmentItemObj->item_price = $fulfilment_item->item_price;

            $fulfilment_item_objs[] = $fulfilmentItemObj;
        }

        return $fulfilment_item_objs;
    }

    static function insert_fulfilment($fulfilment) {
        global $wpdb;

        $wpdb->query($wpdb->prepare(
                        "INSERT INTO moa_order_fulfilment
		(order_id, online_store, inbound_carrier, carrier_tracking_number, invoice_amount, created_user_id)
		VALUES ( %d, %s, %s, %s, %s, %d )", $fulfilment->order_id, $fulfilment->online_store, $fulfilment->inbound_carrier, $fulfilment->carrier_tracking_number, $fulfilment->invoice_amount, $fulfilment->created_user_id
        ));

        $fulfilment->id = $wpdb->insert_id;

        foreach ($fulfilment->items as $item) {
            $item->fulfilment_id = $fulfilment->id;
            self::insert_fulfilment_item($item);
        }
    }

    static function insert_fulfilment_item($fulfilment_item) {
        global $wpdb;

        $wpdb->insert('moa_order_items', (array) $fulfilment_item);

        $fulfilment_item->id = $wpdb->insert_id;
    }

    static function update_fulfilment($fulfilment, $edit_fulfilment_id) {
        global $wpdb;
        $timestamp = date('Y-m-d G:i:s');
        $wpdb->update('moa_order_fulfilment', array(
            'online_store' => $fulfilment->online_store,
            'inbound_carrier' => $fulfilment->inbound_carrier,
            'carrier_tracking_number' => $fulfilment->carrier_tracking_number,
            'invoice_amount' => $fulfilment->invoice_amount,
            'created_user_id' => $fulfilment->created_user_id,
            'edit_date' => $timestamp
                ), array('id' => $edit_fulfilment_id)
        );

        foreach ($fulfilment->items as $item) {
            self::update_fulfilment_item($item);
        }
    }

    static function update_fulfilment_item($fulfilment_item) {
        global $wpdb;

        $wpdb->update('moa_order_items', array(
            'quantity' => $fulfilment_item->quantity,
            'item_price' => $fulfilment_item->item_price
        ), array('id' => $fulfilment_item->id));
    }

}
