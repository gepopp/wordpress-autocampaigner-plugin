<?php

namespace Autocampaigner\Settings\Sections;

use Autocampaigner\Settings\SettingsFieldsOutput;

class MainSettings {


	use SettingsFieldsOutput;


	public function __construct() {
		add_action( 'admin_init', [ $this, 'add_main_settings_section' ] );
	}

	public function add_main_settings_section() {

		add_settings_section(
			'autocampaigner_main_settings_section',
			'',
			[ $this, 'main_settings_section_content' ],
			'autocampaigner_main_settings_page'
		);


		add_settings_field(
			'campaign_monitor_api_client_id',
			__( 'Campaign Monitor API client id', 'autocampaigner' ),
			[
				$this,
				'autocampaigner_settings_input_field',
			],
			'autocampaigner_main_settings_page',
			'autocampaigner_main_settings_section',
			[
				'type'    => 'password',
				'option_name' => 'autocampaigner_general_settings',
				'name' => 'api_client_id'
			]
		);



		add_settings_field(
			'campaign_monitor_api_secret',
			__( 'Campaign Monitor API secret', 'autocampaigner' ),
			[
				$this,
				'autocampaigner_settings_input_field',
			],
			'autocampaigner_main_settings_page',
			'autocampaigner_main_settings_section',
			[
				'type'    => 'password',
				'option_name' => 'autocampaigner_general_settings',
				'name' => 'api_client_secret'
			]
		);

		add_settings_field(
			'error_report_email',
			__( 'Report Api Errors to Email', 'autocampaigner' ),
			[
				$this,
				'autocampaigner_settings_input_field',
			],
			'autocampaigner_main_settings_page',
			'autocampaigner_main_settings_section',
			[
				'type'    => 'email',
				'option_name' => 'autocampaigner_general_settings',
				'name' => 'error_report_email'
			]
		);


		register_setting(
			'autocampaigner_main_settings_section',
			'autocampaigner_general_settings'
		);
	}

	public function main_settings_section_content() {
		 _e( 'Please insert your Campaign Monitor Api credentials.', 'autocampaigner' );
	}

}