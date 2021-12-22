<?php

namespace Autocampaigner;

use Carbon\Carbon;
use Autocampaigner\controller\CampaignController;
use Autocampaigner\controller\TemplatesController;

class CampaignDrafts {

	public $table_name = 'ac_campaign_drafts';


	public function send() {

		$schedule = sanitize_text_field($_POST['cmpaign_schedule']);
		$controller = new CampaignController();
		$controller->itemId = sanitize_text_field($_POST['draft']);

		if(!empty($schedule)){
			$schedule = Carbon::parse($schedule)->format('Y-m-d H:i');
		}else{
			$schedule = false;
		}

		$sent = $controller->send($schedule);

		return $sent;

	}


	public function create_draft() {

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'create_campaign' ) ) {
			wp_die( 'hack' );
		}

		$draft = $this->load( sanitize_text_field( $_POST['draft'] ) );

		$template = $draft['template'];

		$header_data = maybe_unserialize( $draft['header_data'] );

		$template_controller = new TemplatesController();

		$template_id = $template_controller->create_or_update_on_cm( $template );

		if ( home_url() == 'https://ir.test' ) {
			$template_id = '7923400354e18f7be9d5dced1d7c8e01';
		}

		$campaign = [];

		$campaign['Name']       = $header_data['campaign_name'];
		$campaign['Subject']    = $header_data['subject'];
		$campaign['FromName']   = $header_data['from_name'];
		$campaign['FromEmail']  = $header_data['from_email'];
		$campaign['ReplyTo']    = $header_data['reply_email'];
		$campaign['ListIDs']    = get_option( 'autocampaigner_used_lists' );
		$campaign['TemplateID'] = $template_id;

		$content = maybe_unserialize( $draft['content'] );

		$campaign['TemplateContent'] = $content;


		$campaign_controller = new CampaignController();
		$campaign_id         = $campaign_controller->create( $campaign );

		if ( $campaign_id ) {

			global $wpdb;
			$wpdb->update( $wpdb->prefix . $this->table_name, [ 'cm_id' => $campaign_id ], [ 'id' => $draft['id'] ] );

			$redirect = add_query_arg( 'preview', $campaign_id, $_SERVER['HTTP_REFERER'] );
			$redirect = remove_query_arg( 'draft', $redirect );
			wp_safe_redirect( $redirect );
			exit;
		}


		return $campaign_id;

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

		global $wpdb;
		$update = $wpdb->update(
			$wpdb->prefix . $this->table_name,
			[
				'content' => maybe_serialize( $_POST['content'] ),
			],
			[
				'id' => $id,
			]
		);

		if ( is_wp_error( $update ) ) {
			wp_die( 'could not save', 400 );
		}
		wp_die( 1 );
	}

	public function load( $id ) {

		global $wpdb;
		return $wpdb->get_row( sprintf( 'SELECT * FROM %s WHERE id = %d LIMIT 1', $wpdb->prefix . $this->table_name, $id ), ARRAY_A );

	}


}