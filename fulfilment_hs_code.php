<?php
//hook into the init action and call create_book_taxonomies when it fires
add_action('init', 'create_hs_category_taxonomy', 0);

//create a custom taxonomy name it topics for your posts

function create_hs_category_taxonomy() {

// Add new taxonomy, make it hierarchical like categories
//first do the translations part for GUI

    $labels = array(
        'name' => _x('HS Category', 'taxonomy general name'),
        'singular_name' => _x('HS Code', 'taxonomy singular name'),
        'search_items' => __('Search HS Code'),
        'all_items' => __('All HS'),
        'parent_item' => __('Parent HS Code'),
        'parent_item_colon' => __('Parent HS Code:'),
        'edit_item' => __('Edit HS Code'),
        'update_item' => __('Update HS Code'),
        'add_new_item' => __('Add New HS Code'),
        'new_item_name' => __('New HS Code Name'),
        'menu_name' => __('HS Code'),
    );

// Now register the taxonomy
    register_taxonomy('hscodes', array('product'), array(
        'hierarchical' => true,
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'hscode'),
    ));
}

// Add term page
function hscode_taxonomy_add_new_meta_field() {
    // this will add the custom meta field to the add new term page
    ?>
    <div class="form-field">
        <label for="term_meta[custom_term_meta]"><?php _e('HS Code', 'hscode'); ?></label>
        <input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" value="">
        <p class="description"><?php _e('Enter a hs code value', 'hscode'); ?></p>
    </div>
    <?php
}

add_action('hscodes_add_form_fields', 'hscode_taxonomy_add_new_meta_field', 10, 2);



// Edit term page
function hscode_taxonomy_edit_meta_field($term) {

    // put the term ID into a variable
    $t_id = $term->term_id;

    // retrieve the existing value(s) for this meta field. This returns an array
    $term_meta = get_option("taxonomy_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="term_meta[custom_term_meta]"><?php _e('HS Code', 'hscode'); ?></label></th>
        <td>
            <input type="text" name="term_meta[custom_term_meta]" id="term_meta[custom_term_meta]" 
                   value="<?php echo esc_attr($term_meta['custom_term_meta']) ? esc_attr($term_meta['custom_term_meta']) : ''; ?>">
            <p class="description"><?php _e('Enter a hs code value', 'hscode'); ?></p>
        </td>
    </tr>
    <?php
}

add_action('hscodes_edit_form_fields', 'hscode_taxonomy_edit_meta_field', 10, 2);

// Save extra taxonomy fields callback function.
function hscode_save_taxonomy_custom_meta($term_id) {
    if (isset($_POST['term_meta'])) {
        $t_id = $term_id;
        $term_meta = get_option("taxonomy_$t_id");
        $cat_keys = array_keys($_POST['term_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['term_meta'][$key])) {
                $term_meta[$key] = $_POST['term_meta'][$key];
            }
        }
        // Save the option array.
        update_option("taxonomy_$t_id", $term_meta);
    }
}

add_action('edited_hscodes', 'hscode_save_taxonomy_custom_meta', 10, 2);
add_action('create_hscodes', 'hscode_save_taxonomy_custom_meta', 10, 2);

function add_hscodes_columns($columns) {
    $columns['hscode'] = 'HS code';
    return $columns;
}
add_filter('manage_edit-hscodes_columns', 'add_hscodes_columns');


//add column data
function add_hscode_column_content($content, $column_name, $term_id) {
    $term = get_option("taxonomy_$term_id");

    switch ($column_name) {
        case 'hscode':
            //do your stuff here with $term or $term_id
            //$data = maybe_unserialize($term->custom_term_meta);
            $content .= $term['custom_term_meta'];
            break;
        default:
            break;
    }
    return $content;
}

add_filter('manage_hscodes_custom_column', 'add_hscode_column_content', 10, 3);