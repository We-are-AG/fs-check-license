<?php
/**
 * Admin settings section
 */

$settings = get_option('WG_CL_credentials', array());
$check_credentials = false;
if(count(array_filter($settings)) == count($settings)) {
    $check_credentials = true;
} else {
    $check_credentials = false;
}

if($settings)
if (isset($_POST['submit'])) {

	// Remove WordPress action field.
	unset($_POST['action']);

	// Get category value.
	$plugin_id = !empty($_POST['plugin_id']) ? sanitize_text_field($_POST['plugin_id']) : NULL;
	$license_url = !empty($_POST['license_url']) ? sanitize_text_field($_POST['license_url']) : NULL;

	$message = $this->check_license($plugin_id, $license_url);
}

?>

<?php
if($check_credentials == true)
{
?>
<h1 class="fs_check_title"><?php _e('Freemius License Check', 'fs-check-license') ?></h1>

<form method="POST">
    <div class="">
        <select name="plugin_id">
			<?php foreach ($plugins as $r) {
				foreach ($r as $plgn) { ?>
                    <option value=" <?php echo $plgn->id ?>" <?php if (isset($_POST['plugin_id']) && $plgn->id == $_POST['plugin_id']) {
						echo 'selected';
					} ?>>
						<?php echo $plgn->title ?> - <?php echo $plgn->id ?> </option>
					<?php
				}
			}
			?>
        </select>
        <input name="license_url" type="text" id="license_url" value="<?php if (isset($_POST['license_url'])) {
			echo $_POST['license_url'];
		} ?>"> <input type="submit" name="submit" value="Validate License" class="btn-primary">
    </div>
</form>

<div class="message" style="margin-top: 30px;">
	<?php if (isset($_POST['submit'])) {
		echo $message['message'];
	} ?>
</div>

<?php
}
else{

    echo '<h1 class="message_danger"> Error Returning Plugin List, Please check if you have inputted all credentials needed on Admin Settings </h1>';
}
?>
