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

            $brands_terms = wp_get_object_terms($order_item_obj->product_id, 'product_brand');

            if (!empty($brands_terms)) {
                $brand = reset($brands_terms);
                $order_item_obj->manufacturer = $brand->name;
            }

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
            $order_item_obj->hs_code = get_option('taxonomy_' . $child_hscode_term->term_id)['custom_term_meta'];

            // Check if product has variation.
            if ($order_item_obj->variation_id) {
                $variation = new WC_Product_Variation($order_item_obj->variation_id);
                $order_item_obj->attributes = $variation->get_variation_attributes();
                $color = $variation->get_variation_attributes();
                $order_item_obj->color =$color['attribute_pa_color']; 
                $order_item_obj->weight = $variation->__get('weight');
                $order_item_obj->length = $variation->__get('length');
                $order_item_obj->width = $variation->__get('width');
                $order_item_obj->height = $variation->__get('height');
                $order_item_obj->product_barcode = get_post_meta($order_item_obj->variation_id, 'variation_barcode_upc', true);
                $order_item_obj->model = get_post_meta($order_item_obj->variation_id, 'variation_model', true);
            } else {
                $product = new WC_Product($order_item_obj->product_id);
                $color = $product->get_variation_attributes();
                $order_item_obj->color =$color['attribute_pa_color']; 
                $order_item_obj->weight = $product->get_weight();
                $order_item_obj->length = $product->get_length();
                $order_item_obj->width = $product->get_width();
                $order_item_obj->height = $product->get_height();
                $order_item_obj->product_barcode = get_post_meta($order_item_obj->product_id, 'product_barcode_upc', true);
                $order_item_obj->model = get_post_meta($order_item_obj->product_id, 'product_model', true);
            }

            $item_objs[$order_item_id] = $order_item_obj;
        }
      
       
        return $item_objs;
    }

    static function get_shipping_address($order_id) {
        $address = new WC_Order($order_id);

        $order_address = array(
            'company' => $address->shipping_company,
            'person' => $address->shipping_first_name . ' ' . $address->shipping_last_name,
            'tel' => $address->billing_phone,
            'addr1' => $address->shipping_address_1,
            'addr2' => $address->shipping_address_2,
            'addr3' => $address->shipping_address_3,
            'area' => $address->shipping_city,
            'postCode' => $address->shipping_postcode
        );

        return $order_address;
    }

    static function get_order_notes($order_id) {
        $order = new WC_Order($order_id);

        return $order->post->post_excerpt;
    }
    
}
