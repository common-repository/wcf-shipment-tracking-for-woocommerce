<?php
/*
Plugin Name: WCF Shipment Tracking  for WooCommerce
Plugin URI: http://wecodefuture.com
Description: WCF Tracking Order plugin is a client side shipping status plugin.
Version: 1.0
Author: WeCodeFuture
Author URI: http://wecodefuture.com
*/

register_activation_hook(__FILE__, 'wcf_tracking_activate');

function wcf_tracking_activate()
{
    //do nothing
    
}
register_deactivation_hook(__FILE__, 'wcf_tracking_deactivate');

function wcf_tracking_deactivate()
{
    //do nothing
    
}

// Add the data to the custom columns for the order post type:
add_action('manage_shop_order_posts_custom_column', 'wcf_tracking_custom_shop_order_column', 10, 2);
function wcf_tracking_custom_shop_order_column($column, $post_id)
{
    switch ($column)
    {

        case 'wcf_tracking_custom_column':
            echo esc_html(get_post_meta($post_id, 'wcf_tracking_custom_column', true));

        break;

    }
}

// For display and saving in order details page.
add_action('add_meta_boxes', 'wcf_tracking_add_shop_order_meta_box');
function wcf_tracking_add_shop_order_meta_box()
{

    add_meta_box('wcf_tracking_custom_column', __('WCF Shipping Tracking System', 'wcf_tracking') , 'wcf_tracking_shop_order_display_callback', 'shop_order');

}

// For displaying.
function wcf_tracking_shop_order_display_callback($post)
{

    $tracking_id = get_post_meta($post->ID, 'tracking_id', true);

    $tracking_url = get_post_meta($post->ID, 'tracking_url', true);

    $shipping_cmpny = get_post_meta($post->ID, 'shipping_company', true);

    $value = get_post_meta($post->ID, 'shipping_message', true);

    echo '<label>Company Name: </label>';
    echo "<br>";
    echo '<input type="text" placeholder="Enter Shipping Company Name" style="width:100%" id="shipping_company" name="shipping_company" value= ' . esc_attr($shipping_cmpny) . '>';

    echo "<br>";
    echo "<br>";
    echo '<label>Tracking No.: </label>';
    echo "<br>";
    echo '<input type="text" placeholder="Enter Tracking No." style="width:100%" id="tracking_id" name="tracking_id" value=' . esc_attr($tracking_id) . '>';

    echo "<br>";
    echo "<br>";
    echo '<label>Company URL: </label>';
    echo "<br>";
    echo '<input type="text" placeholder="Enter Shipping Company URL: https://example.com" style="width:100%" id="tracking_url" name="tracking_url" value=' . esc_attr($tracking_url) . '>';

    echo "<br>";
    echo "<br>";
    echo '<label>Status display Message:</label>';
    echo "<br>";
    echo '<textarea placeholder="Enter Shipping Status" style="width:100%" rows="4" cols="50" id="shipping_message" name="shipping_message">' . esc_attr($value) . '</textarea>';
}

// For saving.
function wcf_tracking_save_shop_order_meta_box_data($post_id)
{

    // If this is an autosave, our form has not been submitted, so we don't want to do anything.
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    {
        return;
    }

    // Check the user's permissions.
    if (isset($_POST['post_type']) && 'shop_order' == $_POST['post_type'])
    {
        if (!current_user_can('edit_shop_order', $post_id))
        {
            return;
        }
    }

    // Make sure that it is set.
    if (!isset($_POST['shipping_company']))
    {
        return;
    }
    if (!isset($_POST['tracking_id']))
    {
        return;
    }
    if (!isset($_POST['tracking_url']))
    {
        return;
    }
    if (!isset($_POST['shipping_message']))
    {
        return;
    }

    // Sanitize user input.
    $ship_cmpny = sanitize_text_field($_POST['shipping_company']);
    $track_no = sanitize_text_field($_POST['tracking_id']);
    $ship_check_url = sanitize_text_field($_POST['tracking_url']);
    $status_msg = sanitize_text_field($_POST['shipping_message']);

    // Update the meta field in the database.
    update_post_meta($post_id, 'shipping_company', $ship_cmpny);
    update_post_meta($post_id, 'tracking_id', $track_no);
    update_post_meta($post_id, 'tracking_url', $ship_check_url);
    update_post_meta($post_id, 'shipping_message', $status_msg);
}

add_action('save_post', 'wcf_tracking_save_shop_order_meta_box_data');

function wcf_tracking_shipping_detais()
{
?>

	<form action="" method="post">

		<input type"text" style="margin-left:150px"; name="wcfsm_oredr_id" placeholder="Enter your order id...">

		<input type="submit" name="wcf_submt" value="Search">

	</form>

	<?php
    if (isset($_POST['wcf_submt']))
    {

        $order_id = sanitize_text_field($_POST['wcfsm_oredr_id']);

        $shipping_cmpny = get_post_meta($order_id, 'shipping_company', true);

        $tracking_id = get_post_meta($order_id, 'tracking_id', true);

        $tracking_url = get_post_meta($order_id, 'tracking_url', true);

        $shipping_status = get_post_meta($order_id, 'shipping_message', true);

        if (is_null(get_post($order_id)))
        {
            echo "Order id is not valid. Please Check again!";
        }
?>
		<br>
		<table>
		<tr>
		<td>Tracking Id:</td>
		<td><?php echo esc_html($tracking_id); ?></td>
		</tr>
		<tr>
		<td>Shipping Company:</td>
		<td><?php echo esc_html($shipping_cmpny); ?></td>
		</tr>
		<tr>
		<td>Tracking URL:</td>
		<td><a href="<?php echo esc_html($tracking_url); ?>" target="_blank" ><?php echo esc_html($tracking_url); ?></a></td>
		</tr>
		<tr>
		<td>Shipping Status:</td>
		<td><?php echo esc_html($shipping_status); ?></td>
		</tr>
		</table>
	<?php
    }
}
add_shortcode('wcf_order_tracking_status', 'wcf_tracking_shipping_detais');
?>