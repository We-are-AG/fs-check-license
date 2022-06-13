<?php
/**
 * Admin settings section
 */

$settings = get_option('WG_CL_credentials', array());

$settings = wp_parse_args($settings, array(
	'dev_id'     => '',
	'dev_public' => '',
	'dev_secret' => '',
));
?>

<table class="form-table">

    <tr>
        <th colspan="2" style="text-align:left;">
            <h3><?php _e('Check License on Freemius', 'fs-check-license') ?> &ndash; <?php _e('API credentials', 'fs-check-license') ?></h3>
            <p><?php printf(__("Get your developer credentials from %s Freemius Dashboard > My Profile %s", 'fs-check-license'), "<a href='https://dashboard.freemius.com/#/profile/'>", '</a>') ?></p>
            <p><?php printf(__("You Need to Fill up all this fields to use the Freemius License Check", 'fs-check-license')) ?></p>
        </th>
    </tr>

    <tr>
        <th scope="row"><?php _e('Developer ID', 'fs-check-license') ?></th>
        <td>
            <input class="regular-text" type="text" name='WG_CL_credentials[dev_id]' value="<?php echo $settings['dev_id'] ?>">
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Developer Public Key', 'fs-check-license') ?></th>
        <td>
            <input class="regular-text" type="password" name='WG_CL_credentials[dev_public]' value="<?php echo $settings['dev_public'] ?>">
        </td>
    </tr>
    <tr>
        <th scope="row"><?php _e('Developer Secret Key', 'fs-check-license') ?></th>
        <td>
            <input class="regular-text" type="password" name='WG_CL_credentials[dev_secret]' value="<?php echo $settings['dev_secret'] ?>">
        </td>
    </tr>
</table>