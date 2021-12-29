<?php

namespace Autocampaigner\Model;

use Autocampaigner\Options;
use Autocampaigner\Editor\HtmlFileCreator;



class CampaignDraftModel extends DbModel implements ModelInterface {





	use Options;



	public $header_data;





	public $template;





	public $content;





	public $cm_id;





	public $status;





	public $scheduled;





	public $created_at;





	public $html_url;





	public CampaignApiModel $api_model;





	public function __construct( $id = false ) {

		$this->id = $id;


		if ( isset( $_GET['draft'] ) && ! empty( sanitize_text_field( $_GET['draft'] ) ) ) {
			$this->id = (int) sanitize_text_field( $_GET['draft'] );
		}


		if ( isset( $_POST['draft'] ) && ! empty( sanitize_text_field( $_POST['draft'] ) ) ) {
			$this->id = (int) sanitize_text_field( $_POST   ['draft'] );
		}

		$this->api_model = new CampaignApiModel( $this->cm_id );

	}





	public function update_status() {

		$this->status = $this->api_model->status ?? 'new';

	}





	public function create() {

		global $wpdb;

		$this->validate();
		$this->status = 'new';

		$insert = $wpdb->insert(
			$this->get_table_name(),
			[
				'header_data' => maybe_serialize( $this->header_data ),
				'content'     => maybe_serialize( $this->content ),
				'status'      => $this->status,
				'template'    => $this->template,
			],
		);

		$this->id = $wpdb->insert_id;

		return ! is_wp_error( $insert );

	}





	public function update() {

		global $wpdb;

		return $wpdb->update( $this->get_table_name(), [
			'header_data' => maybe_serialize( $this->header_data ),
			'cm_id'       => $this->cm_id,
			'html_url'    => $this->html_url,
			'content'     => maybe_serialize( $this->content ),
			'status'      => $this->status,
			'scheduled'   => $this->scheduled,
			'template'    => $this->template,
		], [ 'id' => $this->id ] );


	}





	public function delete_on_api() {

		if ( in_array( $this->status, [ 'sent', 'scheduled' ] ) ) {
			return;
		}

		$this->api_model->delete();
		$this->cm_id  = null;
		$this->status = 'new';
		$this->update();
	}





	public function validate() {

		$this->header_data = [
			'campaign_name' => sanitize_text_field( $_POST['cmpaign_name'] ?? $this->header_data['campaign_name'] ),
			'subject'       => sanitize_text_field( $_POST['subject'] ?? $this->header_data['subject'] ),
			'from_name'     => sanitize_text_field( $_POST['from_name'] ?? $this->header_data['from_name'] ),
			'from_email'    => sanitize_email( $_POST['from_email'] ?? $this->header_data['from_email'] ),
			'reply_email'   => sanitize_email( $_POST['reply_email'] ?? $this->header_data['reply_email'] ),
			'confirm_email' => sanitize_text_field( $_POST['confirm_email'] ?? $this->header_data['confirm_email'] ),
		];


		$template = sanitize_text_field( $_POST['template'] ?? '' );


		//empty content if templates changed
		if ( ! empty( $template ) && $template != $this->template ) {
			$this->content  = null;
			$this->template = $template;
		}

		$content = wp_kses_post( $_POST['content'] ?? null );

		if ( ! empty( $content ) ) {
			$this->content = json_decode( html_entity_decode( stripslashes( $content ) ) );
			new HtmlFileCreator( $this );
		}


		return $this;

	}





	/**
	 * prepopulates create form
	 *
	 * @return array
	 */
	public function default_values() {

		return array_merge( [
			'from_name'     => $this->get_from_name(),
			'from_email'    => $this->get_from_email(),
			'reply_email'   => $this->get_reply_email(),
			'confirm_email' => $this->get_confirm_email(),
		], (array) $this->header_data );

	}





	public function delete() {
	}


}
