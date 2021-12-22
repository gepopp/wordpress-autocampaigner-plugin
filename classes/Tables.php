<?php


namespace Autocampaigner;


class Tables {

	const DRAFTS_TABLE  = 'ac_campaign_drafts';

	public function __construct() {
		add_action( 'plugins_loaded', [$this, 'create_update_tables'] );
	}


	public function create_update_tables() {


		$installed_ver = get_option( "autocampaigner-version" );

		if ( $installed_ver == AUTOCAMPAIGNER_VERSION ) {
			return;
		}

		global $wpdb;

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );


		$charset_collate = $wpdb->get_charset_collate();

		$table_name = $wpdb->prefix . self::DRAFTS_TABLE;

		$sql        = "CREATE TABLE $table_name (
		id BIGINT NOT NULL AUTO_INCREMENT,
		header_data TEXT NULL,
		template VARCHAR(255) NULL,		
		content TEXT NULL,
		cm_id VARCHAR(255) NULL,
		created_at TIMESTAMP NOT NULL,
		PRIMARY KEY  (id)
	) $charset_collate;";


		dbDelta( $sql );


		update_option( 'autocampaigner-version', AUTOCAMPAIGNER_VERSION );

	}

}