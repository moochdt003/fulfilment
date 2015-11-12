<?php
include_once('FulfilmentObj.php');
include_once('Repository.php');
include_once('WooHelper.php');
include_once('elogix_proxy.php');
include_once('fulfilment_posting.php');


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

$nothing_fulfiled ="";
  if(!$fulfilments){
        $nothing_fulfiled = 'isEmpty'; 
    }else{
        $nothing_fulfiled = ''; 
    }
        
$nothing = "";
  if($order_item->quantity_needed <= 0){
        $nothing = 'isEmpty'; 
    }else{
        $nothing = ''; 
    }
?>

<div class="wrap"><div id="icon-tools" class="icon32"></div>
    
    <div class="logo_section_MOA">
        <img src="../wp-content/plugins/fulfilment/images/logo.png" alt="MOA" >
        <h2 style="font-family:Comic Sans, Comic Sans MS, cursive;">Fulfillment Zone</h2>
    </div>
    <button style='float:right;' class="button button-secondary" onclick="location.href='./edit.php?post_type=shop_order';">Back to Woocommerce Orders</button>
    
    <h3>Order # :   <?php echo $order_id ?> </h3>

    <p>
 <div class="<?php echo $nothing_fulfiled; ?>">
    <h2>Fulfilled orders </h2>

    <table width="100%" border="0" class="widefat striped wp-list-table main_fulfiment">
        <tr>
            <th width="20">&nbsp;</th>
            <th width="">Fulfillment ID</th>
            <th>Online Store</th>
            <th>Carrier</th>
            <th>Inbound trackNo</th>
             <th>Outbound trackNo</th>
            <th>Invoice amount</th>
            <th>Created</th>
            <th>Sender</th>
            <th>Date &amp; Time</th>
            <th>&nbsp;</th>
        </tr>
        <?php foreach ($fulfilments as $fulfilment) : ?>
            <tr>
                <td><img class="show_fulfilments" src="../wp-content/plugins/fulfilment/images/plus.png" height="auto" width="20px" alt="collapse" title="Show fulfilments"><!--button class="show_fulfilments button button-primary">View</button--></td>
                <td><?php echo $fulfilment->id ?></td>
                <td><?php echo $fulfilment->online_store ?></td>
                <td><?php echo $fulfilment->inbound_carrier ?></td>
                <td><?php echo $fulfilment->carrier_tracking_number ?></td>
                <td>&nbsp;</td>
                <td><?php echo $fulfilment->invoice_amount ?></td>
                <td><?php echo $fulfilment->created_user_id ?></td>
                <td>&nbsp;</td>
                <td><?php echo $fulfilment->created_date ?></td>
                <td> <a href="http://localhost/moadev/wp-content/plugins/fulfilment/edit_fulfilment_modal.php?edit_fulfilment_id=<?php echo $fulfilment->id ?>" class="thickbox">
                    <img src="../wp-content/plugins/fulfilment/images/add_edit_delete.jpg" height="auto" width="23px" title="Edit fulfilment" class="edit_fulfilment">
                    </a>
                    <form  method="POST" style="float:right;">
                        <input type="hidden" value="<?php echo $fulfilment->id ?>" name="fulfilment_id">
                        <?php if ($fulfilment->getNumberOfItemsFulfilled() > 0) : ?>
                        <input  type="submit" id="submit_to_elogix" name="submit_to_elogix" class="button button-primary" value="Submit to Elogix" onclick="return confirm('Please confirm all details are correct before you sumbit fulfilment to Elogix. Click OK to continue or click CANCEL to stop the process!')" >
                        <?php endif; ?>
                    </form>
                </td>
            </tr>

            <tr class="hide_show">
               
                <td colspan="11"><strong>Fulfillment items (ID: <?php echo $fulfilment->id ?>):</strong>

                    <table width="100%" border="0" class="widefat striped wp-list-table ">
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Attributes</th>
                            <th>HS Information</th>
                            <th>HS Code</th>
                            <th>Price</th>
                        </tr>
                        <?php foreach ($fulfilment->items as $fulfilment_item) :
                            if ($fulfilment_item->quantity == 0) continue; 
                            $order_item = $order_item_dictionary[$fulfilment_item->order_item_id];
                            ?>
                            <tr>
                                <td><?php echo $order_item->product_name ?></td>
                                <td><?php echo $fulfilment_item->quantity ?></td>
                                <td>
                                    <?php if ($order_item->attributes) : ?>
                                    <ul>
                                        <?php foreach ($order_item->attributes as $key => $value) : ?>
                                            <li class="moa_attributes"><?php echo str_replace("attribute_pa_","","$key:$value"); ?></li>
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
 </div>
</p>


<div class="<?php echo $nothing; ?>">
<h2>Items to be fulfilled:</h2>
    <p>

        <table class="widefat striped wp-list-table" width="100%" border="0">
            <tr>
                <th>Product</th>
                <th>Quantity<br>Still Required</th>
                <th>Attributes</th>
                <th>Model</th>
                <th>Manufacturer</th>
                <th>Item Price</th>
                 <th>Product URL</th>
            </tr>
            <?php foreach ($items_not_fulfiled as $order_item) : ?>
                <tr>
                    <td><?php echo $order_item->product_name ?></td>
                    <td><?php echo $order_item->quantity_needed ?></td>
                    <td>
                        <?php if ($order_item->attributes) : ?>
                        <ul>
                            <?php foreach ($order_item->attributes as $key => $value) : ?>
                                <li class="moa_attributes"><?php echo str_replace("attribute_pa_","","$key:$value"); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $order_item->model ?> </td>
                       <td><?php echo $order_item->manufacturer ?> </td>
                    <td><?php echo $order_item->sale_price ?></td>
                    <td><a href="<?php echo $order_item->product_url ?>" target="blank"><?php echo $order_item->product_url ?></a></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </p>
    <p>
        <a href="#TB_inline?width=600&height=550&inlineId=modal-window-id" class="thickbox">
            <button class="btn button button-primary">Add fulfillment</button>
        </a>
    </p>
</div>




</div>
<?php
 include_once("add_fulfilment_modal.php"); ?>

<script src="../wp-content/plugins/fulfilment/js/fulfilment.js" type="text/javascript"></script>
<link   href="../wp-content/plugins/fulfilment/css/interface_styles.css" rel="stylesheet">

<style>
    
    .widefat th{  font-size: 12px !important;}
    .widefat td{  font-size: 12px !important;}
    
</style>