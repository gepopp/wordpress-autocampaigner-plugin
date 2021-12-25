<?php

namespace Autocampaigner\Model;

use Autocampaigner\Options;



class CampaignDraftModel extends BaseModel {





	use Options, HasDbTable;



	public $header_data;





	public $template;





	public $content;





	public $cm_id;




	public $status;




	public $created_at;





	public function __construct($id = false) {

		parent::__construct($id);

		if ( isset( $_GET['draft'] ) && ! empty( sanitize_text_field( $_GET['draft'] ) ) ) {
			$this->id = (int) sanitize_text_field( $_GET['draft'] );
		}

		if ( isset( $_POST['draft'] ) && ! empty( sanitize_text_field( $_POST['draft'] ) ) ) {
			$this->id = (int) sanitize_text_field( $_POST['draft'] );
		}

		$this->details();

	}


	public function update_or_create(){
		if($this->id){
			return $this->update_header();
		}else{
			return $this->create();
		}
	}



	public function create() {

		global $wpdb;

		$insert = $wpdb->insert(
			$this->get_table_name(),
			$this->validate_header()
		);

		$this->id = $wpdb->insert_id;
		$this->status = 'new';
		$this->update_status();

		return ! is_wp_error( $insert );

	}





	public function update_header() {

		global $wpdb;
		$insert = $wpdb->update(
			$this->get_table_name(),
			$this->validate_header(),
			[ 'id' => $this->id ]
		);

		return !is_wp_error($insert);

	}


	public function update_status(){
		global $wpdb;
		$wpdb->update($this->get_table_name(), ['status' => $this->status], ['id' => $this->id]);

	}



	public function validate_header() {

		$header_data = [
			'campaign_name' => sanitize_text_field( $_POST['cmpaign_name'] ),
			'subject'       => sanitize_text_field( $_POST['subject'] ),
			'from_name'     => sanitize_text_field( $_POST['from_name'] ),
			'from_email'    => sanitize_email( $_POST['from_email'] ),
			'reply_email'   => sanitize_email( $_POST['reply_email'] ),
			'confrim_email' => sanitize_text_field( $_POST['confirm_email'] ),
		];

		$template_folder = sanitize_text_field( $_POST['template_name'] );

		return [
			'header_data' => maybe_serialize( $header_data ),
			'template'    => $template_folder,
		];
	}





	public function save_content() {

		$content = json_decode( html_entity_decode( stripslashes( wp_kses_post( $_POST['content'] ) ) ) );

		global $wpdb;
		$update = $wpdb->update(
			$this->get_table_name(),
			[
				'content' => maybe_serialize( $content ),
			],
			[
				'id' => $this->id,
			]
		);

		return ! is_wp_error( $update );
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

}
