<?php
/*
Plugin Name: Nutrition Info for WooCommerce
Plugin URI:  https://www.closemarketing.es
Description: Display nutritional information on you woocommerce product pages.
Version:     0.5
Author:      Closemarketing
Author URI:  https://www.closemarketing.es
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: nutrition-info-woocommerce
Domain Path: /languages
*/

function wni_load_textdomain() {
    load_plugin_textdomain( 'nutrition-info-woocommerce', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'wni_load_textdomain' );

define( 'WNI_BUNDLE_VERSION' , '0.0.1');
define( 'NIW_PLUGIN_PREFIX', 'niw_');
define( 'WNI_PLUGIN_PATH' , plugin_dir_path(__FILE__));
define( 'WNI_PLUGIN_URL' , plugin_dir_url(__FILE__));

include WNI_PLUGIN_PATH . 'includes/class-woo-settings.php';
include WNI_PLUGIN_PATH . 'includes/class-woo-metaproducts.php';
include WNI_PLUGIN_PATH . 'includes/template.php'; // Nutrients display function
include WNI_PLUGIN_PATH . 'includes/product-tab.php'; // Nutrients display function
include WNI_PLUGIN_PATH . 'includes/allergens.php'; // Allergens

wp_enqueue_style( 'slider', WNI_PLUGIN_URL . '/css/styles.css',false,'1.1','all');

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_product_summary' ) {
	add_action( 'woocommerce_single_product_summary', 'nutritionInfo', '45' );
	add_action( 'woocommerce_single_product_summary', 'compositionInfo', '45' );
}

if ( get_option( 'wc_nutrients_settings_tab_position' ) == 'after_add_to_cart' ) {
    add_action('woocommerce_single_product_summary', 'nutritionInfo', '35');
    add_action('woocommerce_single_product_summary', 'compositionInfo', '35');
}

if (get_option( 'wc_nutrients_settings_tab_position' ) == 'after_excerpt') {
    add_action('woocommerce_single_product_summary', 'nutritionInfo', '25');
    add_action('woocommerce_single_product_summary', 'compositionInfo', '25');
}

if (get_option( 'wc_nutrients_settings_tab_position' ) == 'after_price') {
    add_action('woocommerce_single_product_summary', 'nutritionInfo', '15');
    add_action('woocommerce_single_product_summary', 'compositionInfo', '15');
}

if (get_option( 'wc_nutrients_settings_tab_position' ) == 'in_description_tab') {
    add_filter( 'woocommerce_product_tabs', 'woo_custom_description_tab', 98 );
}

function woo_custom_description_tab( $tabs ) {

	$tabs['description']['callback'] = 'woo_custom_description_tab_content';	// Custom description callback

	return $tabs;
}

function woo_custom_description_tab_content() {
    $heading = esc_html( apply_filters( 'woocommerce_product_description_heading', __( 'Description', 'woocommerce' ) ) );
    ?>

    <?php if ( $heading ) : ?>
      <h2><?php echo $heading; ?></h2>
    <?php endif; ?>

    <?php the_content();
	nutritionInfo();
	compositionInfo();
}

/**
 * Function that adds icons of allergens in the view of the products
 */
add_action("woocommerce_after_shop_loop_item_title", "niw_add_allergens_icon", 5);
function niw_add_allergens_icon()
{
	$all_allergens = new Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	// Show activated allergens
	foreach ($all_allergens->show_allergens_name() as $key => $value) {
		
		$allergens_active = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . $value, true  );
		if( $allergens_active == "yes" )
		{
			echo $all_allergens->show_allergen_svg($value);
		}
	}
	echo "</div>";
}

/**
 * Function that adds icons of allergens in the view of each product
 */
add_action("woocommerce_single_product_summary", "niw_add_allergens_icon_single_product", 10);
function niw_add_allergens_icon_single_product()
{
	$all_allergens = new Allergens();
	echo "<div class='niw_icon_allergen_product'>";
	// Show activated allergens
	foreach ($all_allergens->show_allergens_name() as $key => $value) {
		
		$allergens_active = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . $value, true  );
		if( $allergens_active == "yes" )
		{
			echo $all_allergens->show_allergen_svg($value);
		}
	}
	echo "</div>";
}

/**
 * Feature that overlays product icons on featured image
 */
function replacing_template_loop_product_thumbnail() {
	// Adding something instead
	function wc_template_loop_product_replaced_thumb() {
		// Get outstanding image
		global $post;
		$thumbID = get_post_thumbnail_id( $post->ID );
		$img = wp_get_attachment_url( $thumbID );
		//*********************** */

		echo "<div niw_images_product>";
		// Show activated allergens
		$all_allergens = new Allergens();
		$position_icon=180;
		foreach ($all_allergens->show_allergens_name() as $key => $value) {
			
			$allergens_active = get_post_meta( get_the_ID(), NIW_PLUGIN_PREFIX . $value, true  );
			

			if( $allergens_active == 'yes' && $value == 'Lacteal' )
			{
				echo '<svg style="position:absolute;
				top: 10px;
				left : '. $position_icon .'px;
				width: auto;
				height: auto;
				z-index: 1;" width="100" height="30" viewBox="0 0 444 443" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="222.191" cy="221.529" r="221.356" fill="white"/>
				<path d="M301.849 173.923C322.676 154.328 344.775 133.54 366.794 112.812C415.663 168.935 423.891 280.205 347.756 353.239C274.086 423.929 153.335 418.941 85.9054 342.11C17.0842 263.69 27.0208 145.265 108.064 79.9618C187.895 15.6319 299.027 33.9551 354.652 98.126C333.169 118.357 311.626 138.667 290.243 158.799C273.39 141.569 266.633 118.874 271.661 92.2435C238.075 92.2435 206.238 92.2435 173.07 92.2435C173.07 102.24 173.626 111.779 172.851 121.179C172.394 126.823 170.864 132.884 168.042 137.733C158.721 153.692 148.367 169.054 138.967 184.972C136.443 189.245 134.754 194.71 134.714 199.659C134.376 237.319 134.794 274.978 134.217 312.618C134.138 318.143 131.296 324.741 127.54 328.855C119.809 337.341 110.588 344.495 100.373 353.657C106.852 358.764 110.886 361.924 115.159 365.303C116.013 364.746 117.504 364.289 118.12 363.275C128.653 346.443 144.512 344.952 162.656 345.35C207.291 346.343 251.947 345.668 296.602 345.668C300.537 345.668 304.472 345.668 309.52 345.668C309.778 341.892 310.156 338.752 310.175 335.612C310.215 290.36 310.553 245.089 309.877 199.857C309.758 192.246 305.287 184.654 301.849 173.923Z" fill="#054C48" stroke="#054C48" stroke-width="4"/>
				<path d="M288.97 193.319C289.566 196.121 291.176 200.076 291.196 204.071C291.395 241.989 291.176 279.907 291.434 317.825C291.474 324.562 289.646 327.245 282.531 327.027C268.342 326.57 254.132 326.709 239.943 326.967C234.001 327.086 231.735 324.96 231.795 318.958C232.013 297.197 231.815 275.436 231.993 253.674C232.013 250.852 232.351 247.076 234.1 245.367C252.065 227.958 270.329 210.907 288.97 193.319Z" fill="#054C48" stroke="#054C48" stroke-width="4"/>
				<path d="M154.23 205.143C173.845 205.143 192.585 205.143 213.055 205.143C209.895 222.135 220.825 241.333 202.184 252.899C198.13 250.971 195.308 249.024 192.228 248.328C189.068 247.632 185.65 248.169 182.331 248.169C165.538 248.169 165.538 248.169 167.446 266.254C170.387 266.433 173.527 266.711 176.667 266.81C179.847 266.909 183.007 266.83 188.571 266.83C175.634 278.376 164.445 288.353 154.23 297.494C154.23 268.817 154.23 237.457 154.23 205.143ZM198.687 216.65C189.187 216.65 180.979 216.829 172.772 216.59C166.909 216.411 166.114 219.989 166.75 224.281C167.366 228.494 163.63 235.271 171.917 235.669C180.701 236.086 189.525 235.768 198.687 235.768C198.687 228.932 198.687 223.466 198.687 216.65Z" fill="#054C48" stroke="#054C48" stroke-width="4"/>
				<path d="M244.178 141.867C234.877 156.732 226.669 170.047 218.164 183.183C217.329 184.455 214.765 185.171 212.997 185.191C196.561 185.33 180.126 185.27 160.948 185.27C170.508 170.206 178.934 156.772 187.599 143.476C188.274 142.423 190.48 141.946 191.971 141.926C208.664 141.827 225.358 141.867 244.178 141.867Z" fill="#054C48" stroke="#054C48" stroke-width="4"/>
				<path d="M211.763 326.39C193.121 326.39 175.116 326.39 155.461 326.39C175.573 307.849 194.671 290.261 211.763 274.481C211.763 288.293 211.763 306.776 211.763 326.39Z" fill="#054C48" stroke="#054C48" stroke-width="4"/>
				<path d="M261.486 150.353C267.368 159.832 272.337 167.841 277.762 176.606C262.698 190.875 247.972 204.826 232.908 219.095C231.815 208.025 230.921 197.254 238.413 186.9C246.482 175.751 253.199 163.608 261.486 150.353Z" fill="#054C48" stroke="#054C48" stroke-width="4"/>
				<path d="M251.61 110.904C251.61 115.137 251.61 118.357 251.61 122.311C232.074 122.311 212.877 122.311 193.182 122.311C193.182 118.416 193.182 114.958 193.182 110.904C212.658 110.904 231.597 110.904 251.61 110.904Z" fill="#054C48" stroke="#054C48" stroke-width="4"/>
				</svg>';
				$position_icon=$position_icon-30;
			}
			else if( $allergens_active == "yes" && $value == "Gluten" )
			{
				echo '<svg style="position:absolute;
				top: 10px;
				left : '. $position_icon .'px;
				width: auto;
				height: auto;
				z-index: 1;" width="100" height="30" viewBox="0 0 456 455" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="227.997" cy="227.187" r="227.014" fill="white"/>
				<path d="M368.591 93.5952C344.625 115.804 321.548 137.187 299.995 157.173C299.995 144.11 299.995 127.49 299.995 110.194C293.432 110.194 287.568 110.194 280.539 110.194C280.137 117.963 279.713 125.839 279.311 133.186C265.613 136.912 252.572 140.469 238.45 144.322C238.45 129.798 238.006 115.444 238.62 101.153C238.98 92.6213 235.084 91.3933 227.865 91.3722C220.709 91.351 217.766 93.3834 218.02 100.963C218.507 115.317 218.168 129.671 218.168 144.343C204.216 140.553 191.217 136.996 176.926 133.101C176.926 126.305 176.926 118.726 176.926 110.638C173.899 110.151 171.909 109.643 169.919 109.558C155.628 109.008 155.48 109.05 155.67 123.807C155.819 135.049 155.48 146.397 157.025 157.469C161.048 186.516 179.467 203.898 208.408 207.836C212.007 208.323 215.331 210.8 217.512 213.192C200.427 210.482 183.32 207.772 166.235 205.084C157.83 203.771 155.226 207.412 155.501 215.712C156.369 241.435 165.388 262.183 190.201 274.144C189.82 275.012 189.439 275.881 189.079 276.77C181.732 276.325 174.365 276.113 167.039 275.351C158.507 274.462 153.341 277.68 155.84 286.382C161.175 304.97 151.775 316.487 138.945 327.644C124.125 340.538 110.173 354.426 95.1837 368.484C43.8856 317.525 22.693 255.006 40.0959 183.002C59.2136 103.969 111.062 54.3223 190.772 37.8933C258.584 23.9202 317.716 44.1812 368.591 93.5952Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				<path d="M379.091 107.399C433.798 170.807 442.796 285.683 366.113 363.276C287.568 442.753 171.633 431.384 111.422 380.531C130.074 362.958 148.662 345.45 167.611 327.602C178.323 340.644 193.99 348.117 212.684 350.001C214.738 350.213 217.533 354.384 217.935 357.009C218.761 362.429 218.189 368.061 218.189 374.285C225.239 374.285 231.04 374.285 236.608 374.285C237.222 373.396 238.027 372.761 237.942 372.274C235.359 355.866 241.986 349.79 258.796 346.212C287.483 340.114 303.891 312.549 300.376 282.528C300.059 279.903 295.126 275.478 292.882 275.775C277.321 277.892 261.866 280.983 246.41 283.904C244.145 284.328 242.028 285.556 239.826 286.403C239.255 285.344 238.683 284.286 238.111 283.227C240.779 281.83 243.319 279.628 246.156 279.141C269.572 275.224 288.711 265.147 296.269 241.244C299.762 230.193 300.101 218.146 301.964 205.952C295.147 205.338 290.997 204.957 284.18 204.343C316.424 171.4 347.461 139.706 379.091 107.399Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				<path d="M216.812 186.961C195.726 187.554 181.075 176.248 179 157.808C199.918 153.934 219.48 169.029 216.812 186.961Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				<path d="M239.722 186.876C236.927 169.431 252.552 156.813 276.878 156.876C277.47 174.364 260.978 187.681 239.722 186.876Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				<path d="M239.382 328.449C237.858 309.84 254.329 297.116 276.728 298.788C277.258 315.937 262.141 328.153 239.382 328.449Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				<path d="M179.807 227.016C192.107 227.969 203.794 230.531 212.432 240.312C214.358 242.492 216.137 247.574 215.015 249.077C212.559 252.316 208.134 256.169 204.598 256.084C194.902 255.873 176.631 242.45 179.807 227.016Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				<path d="M277.49 227.482C275.945 246.473 260.088 258.795 240.017 257.207C238.323 243.954 258.415 227.652 277.49 227.482Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				<path d="M216.686 329.084C201.655 327.941 189.248 323.685 181.69 310.135C186.157 303.53 189.926 297.644 199.538 301.9C212.897 307.849 218.571 315.598 216.686 329.084Z" fill="#DBA26C" stroke="#DBA26C" stroke-width="4"/>
				</svg>';
				$position_icon=$position_icon-30;
			}
			else if( $allergens_active == "yes" && $value == "Vegan" )
			{
				echo '<svg style="position:absolute;
				top: 10px;
				left : '. $position_icon .'px;
				width: auto;
				height: auto;
				z-index: 1;" width="100" height="30" viewBox="0 0 445 445" fill="none" xmlns="http://www.w3.org/2000/svg">
				<circle cx="222.711" cy="222.462" r="222.289" fill="white"/>
				<mask id="path-2-inside-1" fill="white">
				<path d="M227.076 408.506C124.699 409.204 40.2028 325.345 39.8949 222.742C39.587 120.612 122.42 37.4087 225.372 36.4233C327.051 35.4379 411.793 117.943 412.389 218.472C413.005 324.606 331.875 407.788 227.076 408.506ZM233.707 309.866C226.481 302.229 224.879 294.469 228.616 285.088C231.366 278.17 232.537 270.636 234.446 263.389C237.238 252.858 240.378 242.388 242.637 231.734C243.129 229.373 240.502 226.335 239.311 223.625C237.176 225.37 234.569 226.786 233.05 228.942C231.756 230.769 231.736 233.458 231.1 235.757C226.912 250.99 222.724 266.201 217.961 283.528C213.383 275.952 210.448 270.204 206.65 265.072C200.881 257.23 200.06 249.306 203.304 240.171C206.362 231.508 208.826 222.557 210.407 213.525C211.125 209.357 209.113 204.738 208.333 200.304C205.747 203.671 202.154 206.668 200.779 210.466C197.576 219.314 195.441 228.552 192.711 238.221C181.748 225.801 175.775 213.319 183.144 197.738C184.294 195.316 185.3 192.401 185.033 189.855C184.725 187.063 182.878 184.415 181.707 181.705C179.408 183.696 176.288 185.257 174.994 187.782C173.126 191.436 172.552 195.767 170.786 202.172C165.695 193.591 162.492 187.186 158.243 181.561C156.744 179.57 152.721 179.467 149.867 178.503C149.703 181.233 148.553 184.415 149.559 186.611C152.557 193.14 156.395 199.278 161.138 207.777C153.85 205.888 149.354 204.225 144.735 203.732C142.58 203.507 140.178 205.477 137.879 206.463C139.213 208.824 139.993 212.313 142.005 213.319C146.213 215.413 150.996 216.973 155.677 217.363C172.182 218.739 175.261 232.144 182.385 244.954C170.088 241.793 159.967 238.795 149.662 236.784C146.952 236.25 143.647 238.816 140.629 239.966C142.682 242.203 144.345 245.59 146.891 246.473C155.266 249.347 163.827 252.591 172.531 253.35C194.086 255.26 198.931 272.237 207.574 289.05C189.2 284.226 172.818 279.689 156.293 275.829C153.07 275.07 149.21 277.02 145.659 277.718C148.389 280.448 150.647 284.698 153.911 285.663C170.909 290.733 188.051 295.455 205.356 299.314C215.231 301.531 220.856 306.725 223.196 316.415C221.882 317.236 220.835 318.16 219.603 318.652C190.761 330.005 163.642 326.309 139.521 307.3C115.995 288.762 106.531 263.676 110.042 233.951C111.561 221.1 113.634 208.29 115.502 195.48C118.499 175.033 121.558 154.607 124.781 133.011C135.046 137.055 144.017 141.058 153.337 143.973C156.416 144.938 160.378 143.091 163.929 142.536C161.569 139.744 159.742 135.823 156.724 134.386C147.178 129.87 137.119 126.421 127.388 122.295C119.608 118.99 115.646 120.119 114.394 129.624C109.959 163.414 105.238 197.163 99.8799 230.81C90.334 290.774 141.471 343.307 201.497 334.398C211.125 332.961 220.486 329.697 229.929 327.274C239.681 341.377 241.138 341.624 247.358 330.128C248.303 330.395 249.288 330.62 250.253 330.949C299.85 348.07 347.538 318.775 353.574 266.591C354.888 255.342 352.609 243.558 351.131 232.124C348.113 208.803 344.747 185.503 340.887 162.305C340.353 159.103 335.139 153.601 333.887 154.073C314.036 161.382 294.451 169.367 274.969 177.62C273.327 178.318 272.937 181.931 271.972 184.189C274.271 185.092 276.632 186.878 278.829 186.714C281.764 186.488 284.597 184.784 287.43 183.635C301.554 178.01 315.678 172.385 331.198 166.185C335.283 195.541 339.327 223.112 342.919 250.743C347.128 283.096 328.755 312.309 297.654 322.224C279.835 327.911 262.508 326.433 243.211 315.327C251.094 311.611 256.227 308.367 261.831 306.704C276.879 302.27 292.214 298.801 307.241 294.264C310.279 293.34 312.393 289.378 314.939 286.812C311.49 285.929 307.754 283.733 304.633 284.39C292.131 287.038 279.794 290.589 266.122 294.141C268.482 289.029 270.761 285.519 271.746 281.659C273.861 273.366 278.89 269.568 287.246 268.152C295.95 266.673 304.51 264.066 312.886 261.213C315.432 260.33 317.135 256.963 319.209 254.726C316.15 253.576 312.845 251.051 310.053 251.503C302.211 252.796 294.554 255.301 285.932 257.579C289.771 245.303 295.724 237.502 307.959 236.188C311.285 235.839 314.836 234.895 317.669 233.191C319.517 232.083 320.256 229.147 321.487 227.033C319.27 225.883 317.094 223.851 314.857 223.768C311.531 223.645 308.144 224.918 303.196 225.924C306.748 219.498 309.683 215.228 311.449 210.527C312.311 208.208 311.018 205.067 310.71 202.316C307.918 203.404 304.284 203.753 302.498 205.744C299.132 209.522 296.853 214.243 293.26 219.95C291.803 215.495 291.577 212.888 290.222 211.123C288.416 208.782 285.747 207.099 283.448 205.128C282.401 208.228 280.307 211.431 280.553 214.428C280.964 219.457 283.407 224.302 283.961 229.332C284.372 233.068 283.16 236.989 282.688 240.828C279.28 234.587 278.562 228.572 277.063 222.763C266.655 182.259 236.17 163.455 199.362 151.795C197.597 151.24 194.928 153.54 192.69 154.525C193.963 156.947 194.62 160.54 196.611 161.566C203.263 164.974 210.365 167.54 217.345 170.312C241.364 179.837 257.458 196.835 265.629 221.284C276.447 253.658 261.544 295.373 233.707 309.866Z"/>
				</mask>
				<path d="M227.076 408.506C124.699 409.204 40.2028 325.345 39.8949 222.742C39.587 120.612 122.42 37.4087 225.372 36.4233C327.051 35.4379 411.793 117.943 412.389 218.472C413.005 324.606 331.875 407.788 227.076 408.506ZM233.707 309.866C226.481 302.229 224.879 294.469 228.616 285.088C231.366 278.17 232.537 270.636 234.446 263.389C237.238 252.858 240.378 242.388 242.637 231.734C243.129 229.373 240.502 226.335 239.311 223.625C237.176 225.37 234.569 226.786 233.05 228.942C231.756 230.769 231.736 233.458 231.1 235.757C226.912 250.99 222.724 266.201 217.961 283.528C213.383 275.952 210.448 270.204 206.65 265.072C200.881 257.23 200.06 249.306 203.304 240.171C206.362 231.508 208.826 222.557 210.407 213.525C211.125 209.357 209.113 204.738 208.333 200.304C205.747 203.671 202.154 206.668 200.779 210.466C197.576 219.314 195.441 228.552 192.711 238.221C181.748 225.801 175.775 213.319 183.144 197.738C184.294 195.316 185.3 192.401 185.033 189.855C184.725 187.063 182.878 184.415 181.707 181.705C179.408 183.696 176.288 185.257 174.994 187.782C173.126 191.436 172.552 195.767 170.786 202.172C165.695 193.591 162.492 187.186 158.243 181.561C156.744 179.57 152.721 179.467 149.867 178.503C149.703 181.233 148.553 184.415 149.559 186.611C152.557 193.14 156.395 199.278 161.138 207.777C153.85 205.888 149.354 204.225 144.735 203.732C142.58 203.507 140.178 205.477 137.879 206.463C139.213 208.824 139.993 212.313 142.005 213.319C146.213 215.413 150.996 216.973 155.677 217.363C172.182 218.739 175.261 232.144 182.385 244.954C170.088 241.793 159.967 238.795 149.662 236.784C146.952 236.25 143.647 238.816 140.629 239.966C142.682 242.203 144.345 245.59 146.891 246.473C155.266 249.347 163.827 252.591 172.531 253.35C194.086 255.26 198.931 272.237 207.574 289.05C189.2 284.226 172.818 279.689 156.293 275.829C153.07 275.07 149.21 277.02 145.659 277.718C148.389 280.448 150.647 284.698 153.911 285.663C170.909 290.733 188.051 295.455 205.356 299.314C215.231 301.531 220.856 306.725 223.196 316.415C221.882 317.236 220.835 318.16 219.603 318.652C190.761 330.005 163.642 326.309 139.521 307.3C115.995 288.762 106.531 263.676 110.042 233.951C111.561 221.1 113.634 208.29 115.502 195.48C118.499 175.033 121.558 154.607 124.781 133.011C135.046 137.055 144.017 141.058 153.337 143.973C156.416 144.938 160.378 143.091 163.929 142.536C161.569 139.744 159.742 135.823 156.724 134.386C147.178 129.87 137.119 126.421 127.388 122.295C119.608 118.99 115.646 120.119 114.394 129.624C109.959 163.414 105.238 197.163 99.8799 230.81C90.334 290.774 141.471 343.307 201.497 334.398C211.125 332.961 220.486 329.697 229.929 327.274C239.681 341.377 241.138 341.624 247.358 330.128C248.303 330.395 249.288 330.62 250.253 330.949C299.85 348.07 347.538 318.775 353.574 266.591C354.888 255.342 352.609 243.558 351.131 232.124C348.113 208.803 344.747 185.503 340.887 162.305C340.353 159.103 335.139 153.601 333.887 154.073C314.036 161.382 294.451 169.367 274.969 177.62C273.327 178.318 272.937 181.931 271.972 184.189C274.271 185.092 276.632 186.878 278.829 186.714C281.764 186.488 284.597 184.784 287.43 183.635C301.554 178.01 315.678 172.385 331.198 166.185C335.283 195.541 339.327 223.112 342.919 250.743C347.128 283.096 328.755 312.309 297.654 322.224C279.835 327.911 262.508 326.433 243.211 315.327C251.094 311.611 256.227 308.367 261.831 306.704C276.879 302.27 292.214 298.801 307.241 294.264C310.279 293.34 312.393 289.378 314.939 286.812C311.49 285.929 307.754 283.733 304.633 284.39C292.131 287.038 279.794 290.589 266.122 294.141C268.482 289.029 270.761 285.519 271.746 281.659C273.861 273.366 278.89 269.568 287.246 268.152C295.95 266.673 304.51 264.066 312.886 261.213C315.432 260.33 317.135 256.963 319.209 254.726C316.15 253.576 312.845 251.051 310.053 251.503C302.211 252.796 294.554 255.301 285.932 257.579C289.771 245.303 295.724 237.502 307.959 236.188C311.285 235.839 314.836 234.895 317.669 233.191C319.517 232.083 320.256 229.147 321.487 227.033C319.27 225.883 317.094 223.851 314.857 223.768C311.531 223.645 308.144 224.918 303.196 225.924C306.748 219.498 309.683 215.228 311.449 210.527C312.311 208.208 311.018 205.067 310.71 202.316C307.918 203.404 304.284 203.753 302.498 205.744C299.132 209.522 296.853 214.243 293.26 219.95C291.803 215.495 291.577 212.888 290.222 211.123C288.416 208.782 285.747 207.099 283.448 205.128C282.401 208.228 280.307 211.431 280.553 214.428C280.964 219.457 283.407 224.302 283.961 229.332C284.372 233.068 283.16 236.989 282.688 240.828C279.28 234.587 278.562 228.572 277.063 222.763C266.655 182.259 236.17 163.455 199.362 151.795C197.597 151.24 194.928 153.54 192.69 154.525C193.963 156.947 194.62 160.54 196.611 161.566C203.263 164.974 210.365 167.54 217.345 170.312C241.364 179.837 257.458 196.835 265.629 221.284C276.447 253.658 261.544 295.373 233.707 309.866Z" fill="#269C8A" stroke="white" stroke-width="8" mask="url(#path-2-inside-1)"/>
				</svg>';
				$position_icon=$position_icon-30;
			}
		}
		echo "</div>";
	}
	add_action( 'woocommerce_before_shop_loop_item_title', 'wc_template_loop_product_replaced_thumb', 10 );
}
add_action( 'woocommerce_init', 'replacing_template_loop_product_thumbnail');
