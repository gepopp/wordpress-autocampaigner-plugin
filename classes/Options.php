<?php

namespace Autocampaigner;

trait Options {





	private $client_id = false;





	private $api_key = false;





	private $templates_folder = 'email_templates';





	private $table_names = [

		'CampaignDraft' => 'ac_campaign_drafts',

	];





	public function get_driver() {

		return 'CampaignMonitor';

	}





	/**
	 * @return string
	 */
	public function get_client_id() {

		if ( ! $this->client_id ) {
			$this->set_client_id();
		}

		return $this->client_id;
	}





	/**
	 * @return string
	 */
	public function get_api_key() {

		if ( ! $this->api_key ) {
			$this->set_api_key();
		}


		return $this->api_key;
	}





	/**
	 * @param mixed $api_key
	 */
	public function set_api_key() {

		if ( defined( 'AUTOCAMPAIGNER_KEY' ) ) {
			$this->api_key = AUTOCAMPAIGNER_KEY;
		} else {
			$this->api_key = $this->get_general_setting( 'api_client_secret' );
		}
	}





	/**
	 * @param mixed $client_id
	 */
	public function set_client_id() {

		if ( defined( 'AUTOCAMPAIGNER_CLIENT_ID' ) ) {
			$this->client_id = AUTOCAMPAIGNER_CLIENT_ID;
		} else {
			$this->client_id = $this->get_general_setting( 'api_client_id' );
		}

	}





	/**
	 * @param string $setting api_key|client_id
	 *
	 * @return mixed
	 */
	public function get_general_setting( $setting ) {

		$admin_email = get_option( 'admin_email' );

		$defaults = [
			'api_client_secret'  => '',
			'api_client_id'      => '',
			'error_report_email' => $admin_email,
			'from_email'         => $admin_email,
			'from_name'          => '',
			'reply_email'        => $admin_email,
			'confirm_email'      => $admin_email,
		];


		$options = get_option( 'autocampaigner_general_settings' );
		$options = ! is_array( $options ) ? [] : $options;

		$settings = array_merge( $defaults, $options );

		return $settings[ $setting ];


	}





	public function get_error_email() {

		return $this->get_general_setting( 'error_report_email' );
	}





	public function get_from_email() {

		return $this->get_general_setting( 'from_email' );
	}





	public function get_from_name() {

		return $this->get_general_setting( 'from_name' );
	}





	public function get_reply_email() {

		return $this->get_general_setting( 'reply_email' );
	}





	public function get_confirm_email() {

		return $this->get_general_setting( 'confirm_email' );
	}





	public function get_templates_folder() {

		return trailingslashit( AUTOCAMPAIGNER_DIR . '/' . $this->templates_folder );
	}

	public function get_templates_folder_url() {

		return trailingslashit( AUTOCAMPAIGNER_URL . '/' . $this->templates_folder );
	}





	public function get_table_name() {


		$model = get_class( $this );

		$model = explode( '\\', $model );

		global $wpdb;

		return $wpdb->prefix . $this->table_names[ str_replace( 'Model', '', array_pop( $model ) ) ];


	}

}