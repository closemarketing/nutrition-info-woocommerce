<?php //This filter function will add a custom tab to the Products Data metabox
add_filter( 'woocommerce_product_data_tabs', 'add_my_custom_product_data_tab' , 99 , 1 );
function add_my_custom_product_data_tab( $product_data_tabs ) {
    $product_data_tabs['my-custom-tab'] = array(
        'label' => __( 'Nutritional Info', 'nutrition-info-woocommerce' ),
        'target' => 'my_custom_product_data',
    );
    return $product_data_tabs;
}

// This action will add custom fields to the added custom tabs under Products Data metabox
add_action( 'woocommerce_product_data_panels', 'add_my_custom_product_data_fields' );
function add_my_custom_product_data_fields() {
    global $woocommerce, $post;
    ?>
    <!-- id below must match target registered in above add_my_custom_product_data_tab function -->
    <div id="my_custom_product_data" class="panel woocommerce_options_panel">
        <?php
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'energy',
            'class'       => '',
            'label'       => __('Energy', 'nutritional_info_domain'),
            'description' => __('(KJ/kcal)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 Kj / 0 kcal', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'fat',
            'class'       => '',
            'label'       => __('Fat', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'saturated_fat',
            'class'       => '',
            'label'       => __('Saturated fatty acids', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'monounsaturated_fat',
            'class'       => '',
            'label'       => __('Monounsaturated fatty acids', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'polyunsaturated_fat',
            'class'       => '',
            'label'       => __('Polyunsaturated fatty acids', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'carb',
            'class'       => '',
            'label'       => __('Carbohydrate', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'sugar',
            'class'       => '',
            'label'       => __('Sugar', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'polyol',
            'class'       => '',
            'label'       => __('Polyols', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'starch',
            'class'       => '',
            'label'       => __('Starch', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'fiber',
            'class'       => '',
            'label'       => __('Dietary fiber', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'protein',
            'class'       => '',
            'label'       => __('Protein', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'salt',
            'class'       => '',
            'label'       => __('Salt', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('0 g', 'nutritional_info_domain')
        ) );
        woocommerce_wp_text_input( array(
            'id'          => NIW_PLUGIN_PREFIX . 'vitamin_mineral',
            'class'       => '',
            'label'       => __('Vitamins and minerals', 'nutritional_info_domain'),
            'description' => __('(gram)', 'nutritional_info_domain'),
            'desc_tip'    => false,
            'placeholder' => __('none', 'nutritional_info_domain')
        ) );

        ?>
    </div>
    <?php
}

// Save custom fields data of products tab:
add_action( 'woocommerce_process_product_meta', 'woocommerce_process_product_meta_fields_save' );
function woocommerce_process_product_meta_fields_save( $post_id ){
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'energy', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'energy'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'fat'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'saturated_fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'saturated_fat'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'monounsaturated_fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'monounsaturated_fat'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'polyunsaturated_fat', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'polyunsaturated_fat'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'carb', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'carb'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'sugar', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'sugar'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'polyol', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'polyol'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'starch', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'starch'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'fiber', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'fiber'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'protein', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'protein'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'salt', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'salt'] ) );
    update_post_meta( $post_id, NIW_PLUGIN_PREFIX . 'vitamin_mineral', stripslashes( $_POST[NIW_PLUGIN_PREFIX . 'vitamin_mineral'] ) );
} ?>
