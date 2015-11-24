
<?php add_thickbox(); ?>

<div id="modal-window-id" style="display:none;">
    <h4>Add Fulfillment</h4>
    <form method="post" onsubmit="return validateForm();" id="add_fulfilment" name="add_fulfilment">    

        <div id="postbox" >
            <!-- New fulfillment Form -->
            <table>
                <tr>
                     <td><label for="online_store">Online Store:</label></td>
                     <td><input type="text" id="online_store" name="online_store" /></td>
                </tr>
                <tr>
                     <td><label for="inbound_carrier">Inbound Carrier:</label></td>
                     <td><input type="text" id ="inbound_carrier" name="inbound_carrier" /></td>
                </tr>
                <tr>
                    <td><label for="online_store">Tracking Number:</label></td>
                     <td><input type="text" name="carrier_tracking_number" id="carrier_tracking_number"/></td>
               </tr>
                <tr>
                    <td><label for="invoice_Amount">Invoice Amount:</label></td> 
                    <td><input type="text" name="invoice_amount" id="invoice_amount"/></td>
               </tr>
          </table>
            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>" />
            <?php wp_nonce_field('new-post'); ?>
           

        </div>
        <!-- Grid containing items that need to be fulfilled -->
        <div class="items_to_fulfil">
            <h4>Items be Fulfilled:</h4>
            <table width="100%" border="1" class="widefat striped wp-list-table">
                <tr>
                    <th>Product</th>
                    <th>Quantity <br>Still Required</th>
                    <th>Attributes</th>
                    <th>Purchased Quantity</th>
                    <th>Purchased Item Price</th>
                </tr>
                <?php $i = 0;
                    foreach ($items_not_fulfiled as $order_item) : ?>
                    <tr>
                        <td>
                            <input type="hidden" name="order_item_id[<?php echo $i ?>]" value="<?php echo $order_item->order_item_id; ?>">
                            <?php echo $order_item->product_name ?>
                        </td>
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

                        <td><input type="text" name="fulfilled_qty[<?php echo $i ?>]" placeholder="Enter quantity aquired"></td>
                        <td><input type="text" name="item_price[<?php echo $i ?>]" placeholder="Enter item price"></td>
                    </tr>
                <?php $i++;
                endforeach; ?>
            </table>
        </div>  
        <br>
       <input type="submit" value="Add New" tabindex="6" id="submit" name="submit" class="button button-primary" />
    </form>
</div>