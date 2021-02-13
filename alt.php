<?php
/*
    Plugin Name: Quick Alt Editor
    Plugin URI: https://arsmoon.com
    Description: Edit your alt very quick without reloading
    Version: 1.0.3
    Author: Arsmoon
    Author URI: https://arsmoon.com
*/

/*  Copyright 2019  Arsmoon  (email: info@arsmoon.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*
    Add column to Media Page
*/

add_action( 'manage_media_custom_column', 'wpa_media_field_input' );
add_filter( 'manage_media_columns', 'wpa_media_display_column' );

function wpa_media_field_input( $column ) {
	if($column == 'wpa_media-column') {
		global $post;
		?>
		<div id="wrapper-<?php echo $post->ID; ?>" class="tt-m-alt">
			<input type="hidden" name="_wpnonce-<?php echo absint($post->ID); ?>" value="<?php echo wp_create_nonce('nonce-' . absint($post->ID)) ?>"/>
			<input type="text" name="wpa_mc_qtx" class="large-text wpa_mc_qtx" id="wpa_mc_<?php echo absint($post->ID); ?>" value="<?php echo sanitize_text_field(wp_strip_all_tags(__(get_post_meta($post->ID, '_wp_attachment_image_alt', true)))); ?>"/>
			<img class="waiting" src="<?php echo esc_url(admin_url("images/loading.gif")); ?>" style="display: none"/>
		</div>
		<?php
	}
}

function wpa_media_display_column( $columns ) {
	// Register the column to display
	$columns['wpa_media-column'] = 'Alternative Text';
	return $columns;
}

/*
    Add plugin script
*/

add_action( 'admin_enqueue_scripts', 'wpa_alt_add_js' );

function wpa_alt_add_js($hook) {
	// Check if we are on upload.php and enqueue script
	if($hook != 'upload.php')
		return;
	wp_enqueue_script('wpa_alt_js', plugin_dir_url(__FILE__) . 'alt.js', array('jquery'), 1.0, true);
}

/*
    Update values via Ajax
*/

function wpa_media_update() {
	$wpa_media_post_id  = absint(filter_input(INPUT_POST, 'post_id'));
	$wpa_media_alt_text = wp_strip_all_tags(filter_input(INPUT_POST, 'alt_text'));
	if( ! wp_verify_nonce(filter_input(INPUT_POST, '_wpnonce'), 'nonce-' . $wpa_media_post_id)) {
		wp_send_json_error('Security check');
	}

	if( ! empty($wpa_media_alt_text)) {
		update_post_meta($wpa_media_post_id, '_wp_attachment_image_alt', sanitize_text_field(wp_strip_all_tags($wpa_media_alt_text)));
		wp_send_json_success('0');
	}
}
add_action( 'wp_ajax_wpa_media_alt_update' , 'wpa_media_update' );
