<?php

//include_once('Repository.php');

class ElogixProxy {

    static function send_fulfilment($fulfilment_id) {

        $fulfilment_data = Repository::fetch_fulfilment($fulfilment_id);
        $fulfilment_shipping_data = WooHelper::get_shipping_address($fulfilment_data->order_id);
        $order_items = WooHelper::get_order_items($fulfilment_data->order_id);
        $order_notes = WooHelper::get_order_notes($fulfilment_data->order_id);

        $request_obj = array
            (
            "elogx-incoming" => array
                (
                "auth" => array
                    (
                    "guid" => "defa57a5-3823-4a3a-96ff-0c969cd18da4",
                    "username" => "orders@mallofamerica.co.za",
                    "password" => "testPASS123"
                ),
                "inbound-shipment" => array
                    (
                    "trackNo" => $fulfilment_data->carrier_tracking_number,
                    "carrier" => $fulfilment_data->inbound_carrier,
                    "store" => $fulfilment_data->online_store
                ),
                "final-shipment" => array
                    (
                    "orderNo" => $fulfilment_data->order_id,
                    "fulfilmentNo" => $fulfilment_id,
                    "instructions" => $order_notes,
                    "shipDate" => $fulfilment_data->created_date,
                    "delivery-address" => array
                        (
                        "company" => $fulfilment_shipping_data["company"],
                        "person" => $fulfilment_shipping_data["person"],
                        "tel" => $fulfilment_shipping_data["tel"],
                        "addr1" => $fulfilment_shipping_data["addr1"],
                        "addr2" => $fulfilment_shipping_data["addr2"],
                        "addr3" => $fulfilment_shipping_data["addr3"],
                        "area" => $fulfilment_shipping_data["area"],
                        "postCode" => $fulfilment_shipping_data["postCode"],
                    )
                ),
                "product" => array()
            )
        );

        foreach ($fulfilment_data->items as $fulfilment_item) {
            $order_item = $order_items[$fulfilment_item->order_item_id];
            $request_obj["elogx-incoming"]["product"][] = array
                (
                "barcode" => $order_item->product_barcode,
                "description" => $order_item->product_name,
                "numItems" => $fulfilment_item->quantity,
                "costPerItem" => $fulfilment_item->item_price,
                "hsMainCategory" => $order_item->hs_main_category,
                "hsSubCategory" => $order_item->hs_sub_category,
                "hsCode" => $order_item->hs_code,
                "weight" => $order_item->weight,
                "width" => $order_item->width,
                "height" => $order_item->height,
                "length" => $order_item->length,
                "manufacturer" => $order_item->manufacturer,
                "model" => $order_item->model,
                "color" => $order_item->color
            );
        }

        $request_json = json_encode($request_obj);

        global $wpdb;

        $wpdb->insert("moa_elogix_api_log", array(
            "request" => $request_json,
            "fulfilment_id" => $fulfilment_id
                )
        );

        $log_id = $wpdb->insert_id;

        $url = 'http://elogx.v2.project-stage.com/api/incoming.aspx?format=json';
        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $request_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);

        $result_json = curl_exec($ch);
        $error = null;
        $timestamp = date('Y-m-d G:i:s');
                
        if (curl_errno($ch)) {
            $error = curl_error($ch);
        } else {
            curl_close($ch);
            
            $result = json_decode($result_json);
            if ($result->error) {
                $error = $result->error->code;
            }
        }
        
        $wpdb->update("moa_elogix_api_log", array(
            "response" => $result_json,
            "response_date" => $timestamp,
            "error" => $error
            ),
            array(
                "id" => $log_id
            )
        );
        
        if (!$error) {
            $wpdb->update("moa_order_fulfilment", array(
                "moa_tracking_number" => $result["trackNo"],
                "sent_user_id" => get_current_user_id(),
                "sent_date" => $timestamp,
                ),
                array(
                    "id" => $fulfilment_id
                )
            );
        }
    }

}
