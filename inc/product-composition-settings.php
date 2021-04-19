<?php

add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab2' , 98 , 1 );
function add_my_custom_product_data_tab2( $product_composition_tabs ) {
    $product_composition_tabs['composition-tab'] = array(
        'label' => __( 'Composition', 'composition_info' ),
        'target' => 'ingredients_composition',
    );
    return $product_composition_tabs;
}

// This action will add custom fields to the custom product composition tab added in the Product Data metabox
add_action( 'woocommerce_product_data_panels', 'add_custom_fields_to_product_composition' );
function add_custom_fields_to_product_composition() {
    global $woocommerce, $post;
    ?>
    <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
    <div id="ingredients_composition" class="panel woocommerce_options_panel">
        <?php
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'ingredients',
            'class'       => '',
            'label'       => __('Ingredients separated by commas', 'composition_info'),
            'description' => __('Ingredients', 'composition_info'),
            'desc_tip'    => false,
            'placeholder' => __('ingredient, ingreditent', 'composition_info')
        ) );

	  woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'allergens',
            'class'       => '',
            'label'       => __('allergens separated by commas', 'composition_info'),
            'description' => __('allergens', 'composition_info'),
            'desc_tip'    => false,
            'placeholder' => __('allergens, allergens', 'composition_info')
        ) );

        ?>
    </div>
    <?php
} 

// Save custom fields data of products tab:
add_action( 'woocommerce_process_product_meta', 'woocommerce_process_product_meta_fields_save_composition' );
function woocommerce_process_product_meta_fields_save_composition( $post_id ){
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'ingredients', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'ingredients'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'allergens', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'allergens'] ) );
} ?>