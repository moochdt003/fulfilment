<?php
// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woo_add_barcode_input_general_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woo_add_barcode_input_general_fields_save');

// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woo_add_model_input_general_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woo_add_model_input_general_fields_save');

add_action('woocommerce_product_after_variable_attributes', 'variable_fields', 10, 3);
//add_action( 'woocommerce_product_after_variable_attributes_js', 'variable_fields_js' );
add_action('woocommerce_save_product_variation', 'save_variable_fields', 10, 1);
add_action('woocommerce_save_product_variation', 'save_variable_model_fields', 10, 1);

function woo_add_barcode_input_general_fields() {

    global $woocommerce, $post;

    echo '<div class="options_group">';

    // Barcode/UPC code field
    woocommerce_wp_text_input(
            array(
                'id' => 'product_barcode_upc',
                'label' => __('Barcode/UPC', 'woocommerce'),
                'placeholder' => 'Enter Barcode/UPC',
                'desc_tip' => 'true',
                'description' => __('Enter the Barcode/UPC code.', 'woocommerce')
            )
    );

    echo '</div>';
}

function woo_add_barcode_input_general_fields_save($post_id) {

    // Barcode/UPC Text Field
    $woocommerce_text_field = $_POST['product_barcode_upc'];
    if (!empty($woocommerce_text_field)) {
        update_post_meta($post_id, 'product_barcode_upc', esc_attr($woocommerce_text_field));
    }
}

function woo_add_model_input_general_fields() {

    global $woocommerce, $post;

    echo '<div class="options_group">';

    // Barcode/UPC code field
    woocommerce_wp_text_input(
            array(
                'id' => 'product_model',
                'label' => __('Model', 'woocommerce'),
                'placeholder' => 'Enter model name',
                'desc_tip' => 'true',
                'description' => __('Enter the model.', 'woocommerce')
            )
    );

    echo '</div>';
}

function woo_add_model_input_general_fields_save($post_id) {

    // Barcode/UPC Text Field
    $woocommerce_text_field = $_POST['product_model'];
    if (!empty($woocommerce_text_field)) {
        update_post_meta($post_id, 'product_model', esc_attr($woocommerce_text_field));
    }
}

//Display Fields
//JS to add fields for new variations
//Save variation fields

/**
 * Create new fields for variations
 *
 */
function variable_fields($loop, $variation_data, $variation) {
    ?>


    <tr>
        <td>
            <?php
            // Text Field
            woocommerce_wp_text_input(
                    array(
                        'id' => 'variation_barcode_upc[' . $loop . ']',
                        'label' => __('Barcode/UPC:', 'woocommerce'),
                        'placeholder' => 'Barcode/UPC',
                        'desc_tip' => 'true',
                        'description' => __('Enter the Barcode/UPC value here.', 'woocommerce'),
                        'value' => get_post_meta($variation->ID, 'variation_barcode_upc', true)
                    )
            );
            ?>
        </td>
    </tr>

    <tr>
        <td>
            <?php
            // Text Field
            woocommerce_wp_text_input(
                    array(
                        'id' => 'variation_model[' . $loop . ']',
                        'label' => __('Model:', 'woocommerce'),
                        'placeholder' => 'Model name',
                        'desc_tip' => 'true',
                        'description' => __('Enter the Model here.', 'woocommerce'),
                        'value' => get_post_meta($variation->ID, 'variation_model', true)
                    )
            );
            ?>
        </td>
    </tr>


    <?php
}

/**
 * Save new fields for variations 
 *
 */
function save_variable_fields($post_id) {
    if (isset($_POST['variable_sku'])) :
        $variable_sku = $_POST['variable_sku'];
        $variable_post_id = $_POST['variable_post_id'];

        $barcode_upc = reset($_POST['variation_barcode_upc']);
        $variation_id = (int) reset($variable_post_id);
        update_post_meta($variation_id, 'variation_barcode_upc', stripslashes($barcode_upc));

    endif;
}

function save_variable_model_fields($post_id) {
    if (isset($_POST['variable_sku'])) :
        $variable_sku = $_POST['variable_sku'];
        $variable_post_id = $_POST['variable_post_id'];

        $model = reset($_POST['variation_model']);
        $variation_id = (int) reset($variable_post_id);
        update_post_meta($variation_id, 'variation_model', stripslashes($model));
    endif;
}
