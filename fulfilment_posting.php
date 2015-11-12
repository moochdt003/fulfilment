<?php
           
if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['submit'] == "Add New") {
    $fulfilment = new FulfilmentObj();

    $fulfilment->order_id = $_GET['order'];
    $fulfilment->online_store = $_POST['online_store'];
    $fulfilment->inbound_carrier = $_POST['inbound_carrier'];
    $fulfilment->carrier_tracking_number = $_POST['carrier_tracking_number'];
    $fulfilment->invoice_amount = $_POST['invoice_amount'];
    $fulfilment->created_user_id = get_current_user_id();

    $fulfilment->items = array();

    for ($i = 0; $i < sizeof($_POST['order_item_id']); $i++) {
        $itemObj = new FulfilmentItemObj();

        $itemObj->order_item_id = $_POST['order_item_id'][$i];
        $itemObj->item_price = $_POST['item_price'][$i];
        $itemObj->quantity = $_POST['fulfilled_qty'][$i];

        if ($itemObj->quantity > 0) {
            $fulfilment->items[] = $itemObj;
        }
    }

    $errors = $fulfilment->validate();

    if (!empty($errors)) {
        //	print_r($errors);
        return;
    }

    Repository::insert_fulfilment($fulfilment);
}


if (isset($_POST['submit_to_elogix'])) {

    $fulfilment_id = (int) $_POST['fulfilment_id'];

    ElogixProxy::send_fulfilment($fulfilment_id);
}


if (isset($_POST['update'])) {

    $edit_fulfilment_id = $_POST['fulfilmentID'];

    $fulfilment = new FulfilmentObj();

    $fulfilment->online_store = $_POST['online_store'];
    $fulfilment->inbound_carrier = $_POST['inbound_carrier'];
    $fulfilment->carrier_tracking_number = $_POST['carrier_tracking_number'];
    $fulfilment->invoice_amount = $_POST['invoice_amount'];
    $fulfilment->created_user_id = get_current_user_id();

    $fulfilment->items = array();
    
    for ($i = 0; $i < sizeof($_POST['order_item_id']); $i++) {
        $itemObj = new FulfilmentItemObj();

        $itemObj->id = $_POST['item_id'][$i];
        $itemObj->order_item_id = $_POST['order_item_id'][$i];
        $itemObj->item_price = $_POST['item_price'][$i];
        $itemObj->quantity = $_POST['fulfilled_qty'][$i];

        $fulfilment->items[] = $itemObj;
    }

    $errors = $fulfilment->validate();

    if (!empty($errors)) {
        //	print_r($errors);
        return;
    }
    
    Repository::update_fulfilment($fulfilment, $edit_fulfilment_id);
}

