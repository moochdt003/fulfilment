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
        if (empty($this->items)) {
            $errors[] = "There are no items to fulfil.";
        }
        //Loop through and validate fulfilment items

        return $errors;
    }

    function getNumberOfItemsFulfilled() {
        $total_quantity = 0;

        foreach ($this->items as $item) {
            $total_quantity += $item->quantity;
        }

        return $total_quantity;
    }
}