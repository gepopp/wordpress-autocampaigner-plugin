<?php

namespace Autocampaigner;

use Carbon\Carbon;
use Autocampaigner\controller\CampaignController;
use Autocampaigner\controller\TemplatesController;



class CampaignDrafts {





	public $table_name = 'ac_campaign_drafts';





	protected $draft_id = 0;





	protected $cm_draft_id = '';





	public $screen = 'create';





	public function __construct() {

		$this->draft_id    = ! isset( $_GET['draft'] ) || empty( sanitize_text_field( $_GET['draft'] ) ) ? 0 : (int) sanitize_text_field( $_GET['draft'] );
		$this->cm_draft_id = ! isset( $_GET['cmid'] ) || empty( sanitize_text_field( $_GET['cmid'] ) ) ? '' : sanitize_text_field( $_GET['cmid'] );
		$this->screen      = ! isset( $_GET['screen'] ) || empty( sanitize_text_field( $_GET['screen'] ) ) ? 'create' : sanitize_text_field( $_GET['screen'] );

	}





	public function route( $url = false ) {

		$url = $url ?: $_SERVER['HTTP_REFERER'];


		return add_query_arg( [
			'draft'  => $this->draft_id,
			'cmid'   => $this->cm_draft_id,
			'screen' => $this->screen,
		], $url );

	}





	public function send() {

		$schedule           = sanitize_text_field( $_POST['cmpaign_schedule'] );
		$controller         = new CampaignController();
		$controller->itemId = sanitize_text_field( $_POST['draft'] );

		if ( ! empty( $schedule ) ) {
			$schedule = Carbon::parse( $schedule )->format( 'Y-m-d H:i' );
		} else {
			$schedule = false;
		}

		$sent = $controller->send( $schedule );

		return $sent;

	}





	public function create_draft() {


		$this->draft_id =  sanitize_text_field( $_POST['draft'] );

		$draft = $this->load( );

		$header_data = maybe_unserialize( $draft['header_data'] );

		$template_description = ( new TemplatesController() )->get_template_description();



		if ( home_url() == 'https://ir.test' ) {
			$template_id = '54e22dfe43a92539c14432b514e57543';
		}

		$campaign = [];

		$campaign['Name']       = $header_data['campaign_name'];
		$campaign['Subject']    = $header_data['subject'];
		$campaign['FromName']   = $header_data['from_name'];
		$campaign['FromEmail']  = $header_data['from_email'];
		$campaign['ReplyTo']    = $header_data['reply_email'];
		$campaign['ListIDs']    = get_option( 'autocampaigner_used_lists' );
		$campaign['TemplateID'] = $template_description->TemplateID;

		$campaign['TemplateContent'] = maybe_unserialize( $draft['content'] );

		$campaign_controller = new CampaignController();
		$campaign_id         = $campaign_controller->create( $campaign );

		if ( $campaign_id ) {

			global $wpdb;
			$wpdb->update( $wpdb->prefix . $this->table_name, [ 'cm_id' => $campaign_id ], [ 'id' => $draft['id'] ] );

		}


		return $campaign_id;

	}





	public function save_header() {

		$header_data = [
			'campaign_name' => sanitize_text_field( $_POST['cmpaign_name'] ),
			'subject'       => sanitize_text_field( $_POST['subject'] ),
			'from_name'     => sanitize_text_field( $_POST['from_name'] ),
			'from_email'    => sanitize_text_field( $_POST['from_email'] ),
			'reply_email'   => sanitize_text_field( $_POST['reply_email'] ),
			'confrim_email' => sanitize_text_field( $_POST['confirm_email'] ),
		];

		$template_folder = sanitize_text_field( $_POST['template_name'] );


		global $wpdb;

		$insert = $wpdb->insert(
			$wpdb->prefix . $this->table_name,
			[
				'header_data' => maybe_serialize( $header_data ),
				'template'    => $template_folder,
			]
		);

		$this->draft_id = $wpdb->insert_id;
		$this->screen = 'edit';

		return !is_wp_error($insert);

	}





	public function save_content() {

		$this->draft_id = (int) sanitize_text_field($_POST['draft']);

		$content = json_decode(html_entity_decode( stripslashes( sanitize_text_field($_POST['content']))));

		global $wpdb;
		$update = $wpdb->update(
			$wpdb->prefix . $this->table_name,
			[
				'content' => maybe_serialize($content),
			],
			[
				'id' => $this->draft_id,
			]
		);

		$this->screen = 'send';


		return !is_wp_error( $update );
	}





	public function load() {

		global $wpdb;

		return $wpdb->get_row( sprintf( 'SELECT * FROM %s WHERE id = %d LIMIT 1', $wpdb->prefix . $this->table_name, $this->draft_id ), ARRAY_A );

	}


}