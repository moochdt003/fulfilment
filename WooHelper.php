<?php


include_once('WooOrderItem.php');

class WooHelper {
    static function get_order_items($order_id) {
        $order = new WC_Order($order_id);
        $order_items = $order->get_items();
        $item_objs = array();
        
        foreach ($order_items as $order_item_id => $order_item) {
            $order_item_obj = new WooOrderItem();
            
            $order_item_obj->order_item_id = $order_item_id;
            $order_item_obj->product_id = $order_item['product_id'];
            $order_item_obj->variation_id = $order_item['variation_id'];
            $order_item_obj->quantity = $order_item['qty'];
            $order_item_obj->quantity_needed = $order_item['qty'];
            $order_item_obj->product_name = $order_item['name'];
            $order_item_obj->sale_price = $order->get_item_total($order_item);
            $order_item_obj->meta = $order->get_item_meta($order_item_obj->order_item_id);
            
            $hscode_terms = wp_get_object_terms($order_item_obj->product_id, 'hscodes');
            $parent_hscode_term = null;
            $child_hscode_term = null;
            
            for ($i = 0; $i <= sizeof($hscode_terms); $i++) {
                if ($hscode_terms[$i]->parent == 0) {
                    $parent_hscode_term = $hscode_terms[$i];
                    break;
                }
            }
            
            for ($i = 0; $i <= sizeof($hscode_terms); $i++) {
                if ($hscode_terms[$i]->parent == $parent_hscode_term->term_id) {
                    $child_hscode_term = $hscode_terms[$i];
                    break;
                }
            }
            
            $order_item_obj->hs_main_category = html_entity_decode($parent_hscode_term->name);
            $order_item_obj->hs_sub_category = html_entity_decode($child_hscode_term->name);
            $order_item_obj->hs_code =  get_option('taxonomy_'.$child_hscode_term->term_id)['custom_term_meta'];
            
            // Check if product has variation.
            if ($order_item_obj->variation_id) {
                $variation = new WC_Product_Variation($order_item_obj->variation_id);
                $order_item_obj->attributes = $variation->get_variation_attributes();
                $order_item_obj->weight = $variation->__get('weight');
                $order_item_obj->length = $variation->__get('length');
                $order_item_obj->width = $variation->__get('width');
                $order_item_obj->height = $variation->__get('height');
                $order_item_obj->product_barcode = $variation->__get('variation_barcode_upc');
            } else {
                $product = new WC_Product($order_item_obj->product_id);
                $order_item_obj->weight = $product->get_weight();
                $order_item_obj->length = $product->get_length();
                $order_item_obj->width = $product->get_width();
                $order_item_obj->height = $product->get_height();
                //$order_item_obj->product_barcode = $variation->__get('product_barcode_upc');
            }
            
            $item_objs[] = $order_item_obj;
        }
       // var_dump($item_objs);
        // Display Custom Field Value
        $david = get_post_meta( get_the_ID(), 'product_barcode_upc', true );
        var_dump($david);
        return $item_objs;
    }
}




 