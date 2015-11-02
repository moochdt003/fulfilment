<?php

class FulfilmentObj {

    public $id;
    public $order_id;
    public $online_store;
    public $inbound_carrier;
    public $carrier_tracking_number;
    public $invoice_amount;
    public $created_user_id;
    public $created_date;
    public $items;

    function validate() {
        $errors = array();

        if (empty($this->online_store)) {
            $errors[] = "Online store is required";
        }
        if (empty($this->carrier_tracking_number)) {
            $errors[] = "Tracking Number is required";
        }
        if (empty($this->inbound_carrier)) {
            $errors[] = "Carrier name is required";
        }
        if(empty($this->items)) {
            $errors[] = "There are no items to fulfil.";
        }
       //Loop through and validate fulfilment items
        
        return $errors;
    }

}

function update_fulfilment_item($order_id) {

    $fulfilments_item_order = new WC_Order($order_id);
    $fulfilment_items = $fulfilments_item_order->get_items(); //to get info about product

    foreach ($fulfilment_items as $order_product_detail) {
        echo "<tr>
                                <td>" . $order_product_detail['product_id'] . "</td>
                                <td>" . $order_product_detail['name'] . "</td>
                                <td>" . $order_product_detail['qty'] . "</td>
                                <td>" . $order_product_detail['line_total'] . "</td>
                         </tr>";
    }
}
