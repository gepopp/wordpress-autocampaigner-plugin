<?php

namespace Autocampaigner;

use Autocampaigner\Settings\AdminSettingsPages;

class Boot {


	private static $instance = false;


	private $boot_classes = [
		Hooks::class,
		Enqueue::class,
		AdminSettingsPages::class,
	];


	public static function getInstance(): Boot {

		if ( ! self::$instance ) {
			self::$instance = new Boot();
		}

		return self::$instance;

	}

	public function boot() {

		$this->bootHooks();

		foreach ( $this->boot_classes as $boot_class ) {

			if ( class_exists( $boot_class ) ) {
				new $boot_class();
			}
		}

	}

	public function bootHooks() {

		add_action( 'init', function () {
			load_plugin_textdomain( 'autocampaigner', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		} );
	}

	private function __construct() {
	}

	private function __clone() {
	}


}