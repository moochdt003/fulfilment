<?php

class WooOrderItem {
    public $order_item_id;
    public $product_name;
    public $quantity;
    public $product_barcode;
    public $manufacturer;
    public $sale_price;
    public $attributes;
    public $meta;
    public $variation_id;
    public $quantity_needed;
    public $hs_main_category;
    public $hs_sub_category;
    public $hs_code;
    
    public $weight;
    public $width;
    public $height;
    public $length;
    public $model;
}

    
 function get_shipping_address($order_id){
     
     $order_address = array();
     $address = new WC_Order($order_id);
          
     $order_address = array(
          'company'   => $address->shipping_company,
          'person'    => $address->shipping_first_name.' '.$address->shipping_last_name,
          'tel'       => $address->billing_phone,
          'addr1'     => $address->shipping_address_1,
          'addr2'     => $address->shipping_address_2,
          'addr3'     => $address->shipping_address_3,
          'area'      => $address->shipping_city,
         'postCode'  => $address->shipping_postcode
      );
     
     var_dump($order_address);
      
    }
          
//get_shipping_address(380);




    
    
    
