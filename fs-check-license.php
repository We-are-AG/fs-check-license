<?php
/*
Plugin Name: AG Freemius Check Licence
Description: We are AG Validating License
Version: 1.0.0
Plugin URI: https://weareag.co.uk/plugins/
Author: We are AG
Author URI: http://weareag.co.uk/
Domain: weareag-validation
@developer support@weareag.co.uk
*/

class FS_Check_License {

	private static $_instance;

	/**
	 * FS_Check_License constructor.
	 */
	public function __construct() {
		$this->plugin_name = plugin_basename(__FILE__);

		add_action('admin_menu', array($this, 'FS_menu'), 9);

		add_action('admin_init', array($this, 'admin'));

		add_action('admin_enqueue_scripts', array($this, 'FS_check_css'));
	}
	/**
	 * @return FS_Check_License
	 */
	public static function instance() {
		if (!self::$_instance) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}


	public function FS_check_css() {
		wp_enqueue_style('FS_check_css', plugin_dir_url(__FILE__) . 'assets/css/style.css', FALSE, '1.0');

	}

	/**
	 * @return void
	 */
	public function FS_menu() {
		add_menu_page(
			$this->plugin_name,
			'FS License Check',
			'edit_others_pages',
			'fs_check_license',
			array($this, 'admin_index'),
			'dashicons-chart-area',
			26
		);
	}

	/**
	 * @return void
	 */
	public function admin_index() {
		$plugins = $this->get_plugins('test');
		require_once plugin_dir_path(__FILE__) . 'core/admin_index.php';
	}

	/**
	 * @return false|mixed|object|string
	 */
	public function get_plugins() {

		if (!class_exists('Freemius_API')) {
			include 'inc/freemius/Freemius.php';
		}

		$settings = get_option('WG_CL_credentials', array());

		if (!$settings) {
			return (object)array(
				'error' => array('message' => 'API credentials not set.',)
			);
		}

		// Init SDK.
		$api = new Freemius_Api(
			'developer',
			$settings['dev_id'],
			$settings['dev_public'],
			$settings['dev_secret']
		);

		return $api->Api("/plugins.json?");
	}

	/**
	 * @return void
	 */
	public function admin() {

		register_setting('general', 'WG_CL_credentials');

		add_settings_section(
			'fstm_general_section',
			'',
			array($this, 'admin_section_render'),
			'general'
		);

	}

	public function admin_section_render() {
		include "core/admin-section.php";
	}


	/**
	 * @param $params
	 *
	 * @return false|string
	 */
	/**
	 * @param $plugin_id
	 * @param $url
	 *
	 * @return array|object
	 * @throws Exception
	 */
	public function check_license($plugin_id, $url) {

		$license_id = NULL;
		$message = NULL;
		$status = '1';
		$settings = get_option('WG_CL_credentials', array());

		if (!class_exists('Freemius_API')) {
			include 'inc/freemius/Freemius.php';
		}

		if (!$settings) {
			return (object)array(
				'error' => array('message' => 'API credentials not set.',)
			);
		}

		// Init SDK.
		$api = new Freemius_Api(
			'developer', //scope
			$settings['dev_id'],
			$settings['dev_public'],
			$settings['dev_secret']
		);

		// fields
		$results = $api->Api("/plugins/$plugin_id/installs.json?search=" . urlencode($url));

		if (!empty($results->installs)) {
			$install = $results->installs[0];
			$license_id = $install->license_id;
		} else {
			$message = '<span class="message_danger">Double check the website URL. Make sure to add https/www.</span>';
		}

		if (is_numeric($license_id)) {
			$license_checked = $api->Api("/plugins/$plugin_id/licenses/$license_id.json?");

			$expiration = new \DateTime($license_checked->expiration, new \DateTimeZone('UTC'));
			$now = new \DateTime('now', new \DateTimeZone('UTC'));

			if (empty($license_checked->id)) {
				$message = '<span class="message_danger">Licence ID not found.</span>';
			}

			if (isset($license_checked->activated) && $license_checked->activated === 0 && $expiration >= $now) {
				$message = '<span class="message_success"><strong>License is Valid</strong> but Deactivated.<br />Expiration Date: ' . date_format(date_create($license_checked->expiration), "d-m-Y") . '<br />License ID: ' . $license_checked->id . '<br />Licence key: ' . $license_checked->secret_key . '</span>';
			}
			if (isset($license_checked->activated) && $license_checked->activated === 1 && $expiration >= $now) {
				$message = '<span class="message_success"> <strong>License is Valid</strong> and Activated<br/>Expiration Date: ' . date_format(date_create($license_checked->expiration), "d-m-Y") . '<br /> License ID: ' . $license_checked->id . '<br />Licence key: ' . $license_checked->secret_key . '</span>';
			}
			if ($expiration <= $now) {
				$message = '<span class="message_danger"> <strong>License is Not Valid</strong><br />Expiration Date: ' . date_format(date_create($license_checked->expiration), "d-m-Y") . '<br />License ID: ' . $license_checked->id . '<br />Licence key: ' . $license_checked->secret_key . '</span>';
			}
		} else {
			$message = ' <span class="message_danger">Website has not purchased this plugin / not found, double check on Freemius.</span> ';
		}

		return [
			'message' => $message,
			'status'  => $status
		];
	}


}

FS_Check_License::instance();
