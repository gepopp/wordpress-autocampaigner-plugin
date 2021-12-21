<?php

namespace Autocampaigner;

use phpseclib3\Exception\UnableToConnectException;
use Autocampaigner\controller\TemplatesController;

class CampaignDrafts {

	public $table_name = 'ac_campaign_drafts';





	public function create_draft(){

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'create_campaign' ) ) {
			wp_die( 'hack' );
		}

		$draft = $this->load(sanitize_text_field($_POST['draft']));

		$template = $draft['template'];

		$template_controller = new TemplatesController();

		$template_id = $template_controller->create_or_update_on_cm($template);

		wp_die(var_dump($template_id));

	}





	public function save() {

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'create_campaign' ) ) {
			wp_die( 'hack' );
		}

		$header_data = [
			'campaign_name' => sanitize_text_field( $_POST['cmpaign_name'] ),
			'subject'       => sanitize_text_field( $_POST['subject'] ),
			'from_name'     => sanitize_text_field( $_POST['from_name'] ),
			'from_email'    => sanitize_text_field( $_POST['from_email'] ),
			'reply_email'   => sanitize_text_field( $_POST['reply_email'] ),
		];

		$template_folder = sanitize_text_field( $_POST['template_name'] );


		global $wpdb;
		$wpdb->insert(
			$wpdb->prefix . $this->table_name,
			[
				'header_data' => maybe_serialize( $header_data ),
				'template'    => $template_folder,
			]
		);

		$location = $_SERVER['HTTP_REFERER'];

		$location = add_query_arg( 'draft', $wpdb->insert_id, $location );

		wp_safe_redirect( $location );
		exit;


	}

	public function save_content() {

		$id      = sanitize_text_field( $_POST['draft'] );
		$content = [
			'images'     => $_POST['images'],
			'multilines' => $_POST['multilines'],
		];

		global $wpdb;
		$update = $wpdb->update(
			$wpdb->prefix . $this->table_name,
			[
				'content' => maybe_serialize($content)
			],
			[
				'id' => $id
			]
		);

		if(is_wp_error($update)){
			wp_die('could not save', 400);
		}
		wp_die(1);
	}

	public function load( $id ) {

		global $wpdb;

		return $wpdb->get_row( sprintf( 'SELECT * FROM %s WHERE id = %d LIMIT 1', $wpdb->prefix . $this->table_name, $id ), ARRAY_A );

	}


}