<?php
include_once('FulfilmentObj.php');
include_once('Repository.php');
include_once('WooHelper.php');

if ('POST' == $_SERVER['REQUEST_METHOD'] && $_POST['submit'] == "Add New" && isset($_POST['submit'])) {
    $fulfilment = new FulfilmentObj();

    $fulfilment->order_id = $_GET['order'];
    $fulfilment->online_store = $_POST['online_store'];
    $fulfilment->inbound_carrier = $_POST['inbound_carrier'];
    $fulfilment->tracking_number = $_POST['tracking_number'];
    $fulfilment->invoice_amount = $_POST['invoice_amount'];
    $fulfilment->created_user_id = get_current_user_id();
    
    $fulfilment->items = array();
    
    for ($i = 0; $i < sizeof($_POST['order_item_id']); $i++) {
        $itemObj = new FulfilmentItemObj();
        
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

    Repository::insert_fulfilment($fulfilment);
}

//set order id variable
if (isset($_GET['order'])) {
    $order_id = $_GET['order'];
}

//initialise
$fulfilments = Repository::fetch_fulfilments_for_order($order_id);
$order_items = WooHelper::get_order_items($order_id);
$order_item_dictionary = array();
$items_not_fulfiled = array();

foreach ($order_items as $order_item) {
    $order_item_dictionary[$order_item->order_item_id] = $order_item;
}

foreach ($fulfilments as $fulfilment) {
    foreach ($fulfilment->items as $fulfilment_item) {
        $order_item = $order_item_dictionary[$fulfilment_item->order_item_id];
        $order_item->quantity_needed -= $fulfilment_item->quantity;
    }
}

foreach ($order_items as $order_item) {
    if ($order_item->quantity_needed == 0) {
        continue;
    }
    
    $items_not_fulfiled[] = $order_item;
}
?>

<div class="wrap"><div id="icon-tools" class="icon32"></div>
    <div class="logo_section_MOA"><img src="../wp-content/plugins/fulfilment/logo.png" alt="MOA" ><h2 style="font-family:Comic Sans, Comic Sans MS, cursive;">Fulfillment Zone</h2></div>
    <button style='float:right;' class="button button-secondary" onclick="location.href='./edit.php?post_type=shop_order';">Back to Woocommerce Orders</button>
    
    <h3>Order # :   <?php echo $order_id ?> </h3>

    <p>
    <h2>Fulfilled orders </h2>

    <table width="100%" border="1" class="widefat">
        <tr>
            <th>Fulfillment ID</th>
            <th>Online Store</th>
            <th>Inbound carrier</th>
            <th>Inbound tracking number</th>
            <th>Invoice amount</th>
            <th>Date</th>
            <th>Status</th>
        </tr>
        <?php foreach ($fulfilments as $fulfilment) : ?>
            <tr>
                <td><?php echo $fulfilment->id ?></td>
                <td><?php echo $fulfilment->online_store ?></td>
                <td><?php echo $fulfilment->inbound_carrier ?></td>
                <td><?php echo $fulfilment->tracking_number ?></td>
                <td><?php echo $fulfilment->invoice_amount ?></td>
                <td><?php echo $fulfilment->created_date ?></td>
                <td><button class="show_fulfilments button button-primary">Show items</button></td>
            </tr>

            <tr class="hide_show">
                <td colspan="6"><strong>Fulfillment items (ID: <?php echo $fulfilment->id ?>):</strong>

                    <table width="100%" border="1" class="widefat">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Attributes</th>
                            <th>HS Information</th>
                            <th>HS Code</th>
                            <th>Price</th>
                        </tr>
                        <?php foreach ($fulfilment->items as $fulfilment_item) :
                            $order_item = $order_item_dictionary[$fulfilment_item->order_item_id];
                            ?>
                            <tr>
                                <td><?php echo $order_item->product_name ?></td>
                                <td><?php echo $fulfilment_item->quantity ?></td>
                                <td>
                                    <?php if ($order_item->attributes) : ?>
                                    <ul>
                                        <?php foreach ($order_item->attributes as $key => $value) : ?>
                                            <li><?php echo "$key:$value" ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <?php endif; ?>
                                </td>
                                        <td>
                                            <ul>
                                                <li> <?php echo $order_item->hs_main_category ?>  </li> 
                                                <li> <?php echo $order_item->hs_sub_category ?>  </li> 
                                           </ul>
                                        </td>
                                <td style="color:red;">  <?php echo $order_item->hs_code ?> </td>
                                <td><?php echo $fulfilment_item->item_price ?></td>
                                
                            </tr>
                        <?php endforeach; ?>
                    </table>
                </td>
            </tr>
            <tr>

            </tr>
        <?php endforeach; ?>
    </table>       

</p>


<p>
<h2>Items to be fulfilled:</h2>

<table class="widefat" width="100%" border="1">
    <tr>
        <th>Product</th>
        <th>Quantity<br>Still Required</th>
        <th>Attributes</th>
        <th>Manufacturer</th>
        <th>Item Price</th>
    </tr>
    <?php foreach ($items_not_fulfiled as $order_item) : ?>
        <tr>
            <td><?php echo $order_item->product_name ?></td>
            <td><?php echo $order_item->quantity_needed ?></td>
            <td>
                <?php if ($order_item->attributes) : ?>
                <ul>
                    <?php foreach ($order_item->attributes as $key => $value) : ?>
                        <li><?php echo "$key:$value" ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </td>
            <td> - </td>
            <td><?php echo $order_item->sale_price ?></td>
        </tr>
    <?php endforeach; ?>
</table>
</p>


<p>
    <a href="#TB_inline?width=600&height=550&inlineId=modal-window-id" class="thickbox">
        <button class="btn button button-primary">Add fulfilment</button>

    </a>
</p>

</div>

<?php include_once("add_fulfilment_modal.php"); ?>

<script src="../wp-content/plugins/fulfilment/js/fulfilment.js" type="text/javascript"></script>
<link   href="../wp-content/plugins/fulfilment/css/interface_styles.css" rel="stylesheet">
    


