<?php
    global $wpdb, $table_prefix;
    require_once('../../../wp-config.php');
    include_once('../../../wp-includes/wp-db.php');
    include_once('FulfilmentObj.php');
    include_once('Repository.php');
    include_once('WooHelper.php');
    $fulfilment = Repository::fetch_fulfilment($_GET['edit_fulfilment_id']);
    $order_items = WooHelper::get_order_items($fulfilment->order_id);
?>


<div id="modal-window-id-edit">
    <h4>Edit Fulfillment</h4>
    <form method="post">    
        <input type="hidden" name="fulfilmentID" value='<?php echo $fulfilment->id ?>'>
        <div id="postbox" >
            <!-- New fulfillment Form -->

            <p><label for="online_store">Online Store:</label>
                <input type="text" name="online_store" value="<?php echo $fulfilment->online_store ?>" />
            </p>
            <p><label for="inbound_carrier">Inbound Carrier:</label>
                <input type="text" name="inbound_carrier"  value="<?php echo $fulfilment->inbound_carrier ?>"/></p>
            <p><label for="online_store">Tracking Number:</label>
                <input type="text" name="carrier_tracking_number" value="<?php echo $fulfilment->carrier_tracking_number ?>" />
            </p>
            <p><label for="invoice_Amount">Invoice Amount:</label>
                <input type="text" name="invoice_amount" value="<?php echo $fulfilment->invoice_amount ?>"/>
            </p>
        </div>
        <!-- Grid containing items that need to be fulfilled -->
        <div class="items_to_fulfil">
            <h4>Items be Fulfilled:</h4>
            <table width="100%" border="1" class="widefat">
                <tr>
                    <th>Product</th>
                    <th>Quantity <br>Still Required</th>
                    <th>Attributes</th>
                    <th> Edit purchase quantity</th>
                    <th>Edit purchase price</th>
                </tr>
              <?php $i = 0;
                    foreach ($fulfilment->items as $fulfilment_item) : 
                        $order_item = $order_items[$fulfilment_item->order_item_id]; ?>
                    <tr>
                        <td>
                            <input type="hidden" name="item_id[<?php echo $i ?>]" value="<?php echo $fulfilment_item->id; ?>">
                            <input type="hidden" name="order_item_id[<?php echo $i ?>]" value="<?php echo $fulfilment_item->order_item_id; ?>">
                            <?php echo $order_item->product_name ?>
                        </td>
                        <td><?php echo $fulfilment_item->quantity ?></td>
                        <td>
                            <?php if ($order_item->attributes) : ?>
                            <ul>
                                <?php foreach ($order_item->attributes as $key => $value) : ?>
                                    <li class="moa_attributes"><?php echo str_replace("attribute_pa_","","$key:$value") ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php endif; ?>
                        </td>

                        <td><input type="text" name="fulfilled_qty[<?php echo $i ?>]" value="<?php echo $fulfilment_item->quantity ?>" placeholder="Enter quantity aquired"></td>
                        <td><input type="text" name="item_price[<?php echo $i ?>]" value="<?php echo $fulfilment_item->item_price ?>" placeholder="Enter item price"></td>
                    </tr>
                 <?php $i++;
                endforeach; ?>
            </table>
        </div>  
        <br>
        
         <input type="submit" value="Update" tabindex="6" id="submit" name="update" class="button button-primary" onclick="return confirm('Are you sure you want to update fulfilment?')"/>
    </form>
</div>