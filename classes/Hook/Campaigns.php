<?php

namespace Autocampaigner\Hook;

use Autocampaigner\Model\CampaignApiModel;
use Autocampaigner\Model\CampaignDraftModel;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class Campaigns extends BaseHooks {





	public function __construct() {

		// send a preciew from cm draft
		add_action( 'wp_ajax_autocampaigner_cm_send_preview', [ $this, 'cm_send_preview' ] );

		//schedule a draft on cm
		add_action( 'wp_ajax_autocampaigner_cm_schedule_campaign', [ $this, 'cm_schedule_draft' ] );


		// reloads cm cmapaign statis info
		add_action( 'wp_ajax_autocampaigner_reload_cm_info', [ $this, 'load_cm_draft_status' ] );

		// set scheduled campaign back to draft
		add_action( 'wp_ajax_autocampaigner_unschedule_campaign', [ $this, 'unschedule_campaign' ] );

		// create a draft via api
		add_action( 'wp_ajax_autocampaigner_create_draft_on_api', [ $this, 'create_draft_on_cm' ] );

		//load info about sent campaign
		add_action( 'wp_ajax_autocampaigner_get_cm_campaigninfo', [ $this, 'get_campaign_info' ] );




	}

	public function get_campaign_info(){

		$this->verify_nonce();

		$model = new CampaignDraftModel( sanitize_text_field( $_POST['draft_id'] ) );

		try {

			$data = $model->api_model->summary();
			wp_die(json_encode($data));

		}catch ( CmApiCallUnsuccsessfull $e ) {
				wp_die( $e->getMessage(), 400 );
			}


	}




	public function cm_send_preview() {

		$this->verify_nonce();

		$model = new CampaignApiModel( sanitize_text_field( $_POST['cm_id'] ) );
		try {
			wp_die( $model->preview( 'post', [
				'PreviewRecipients' =>
					explode( ',', sanitize_text_field( $_POST['recipients'] ) ),
			] ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}

	}





	public function cm_schedule_draft() {


		$this->verify_nonce();

		$draft = new CampaignDraftModel( sanitize_text_field( $_POST['draft_id'] ) );
		$draft = $draft->api_model->schedule_campaign( $draft, sanitize_text_field( $_POST['schedule'] ) );

		try {
			wp_die( json_encode( $draft ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	/**
	 * schedules a campaign on cm
	 */
	public function unschedule_campaign() {

		$this->verify_nonce();

		try {

			$draft = new CampaignDraftModel( sanitize_text_field( $_POST['draft_id'] ) );

			$draft = $draft->api_model->unschedule_campaign( $draft );


			wp_die( json_encode($draft) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	public function create_draft_on_cm() {

		$this->verify_nonce();

		try {

			$draft = new CampaignDraftModel( sanitize_text_field( $_POST['draft_id'] ) );

			$api = new CampaignApiModel();

			wp_die( json_encode( $api->create_draft( $draft ) ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {

			wp_die( $e->getMessage(), 400 );

		}


	}


}