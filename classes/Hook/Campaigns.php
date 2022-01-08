<?php

namespace Autocampaigner\Hook;

use Autocampaigner\Model\CampaignApiModel;
use Autocampaigner\Model\CampaignDraftModel;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class Campaigns extends BaseHooks {





	public function __construct() {

		// send a preciew from cm draft
		add_action( 'wp_ajax_autocampaigner_cm_send_preview', [ $this, 'execute' ] );

		//schedule a draft on cm
		add_action( 'wp_ajax_autocampaigner_cm_schedule_campaign', [ $this, 'execute' ] );


		// reloads cm cmapaign statis info
		add_action( 'wp_ajax_autocampaigner_reload_cm_info', [ $this, 'execute' ] );

		// set scheduled campaign back to draft
		add_action( 'wp_ajax_autocampaigner_unschedule_campaign', [ $this, 'execute' ] );

		// create a draft via api
		add_action( 'wp_ajax_autocampaigner_create_draft_on_api', [ $this, 'execute' ] );

		//load info about sent campaign
		add_action( 'wp_ajax_autocampaigner_get_cm_campaigninfo', [ $this, 'execute' ] );


	}





	public function execute() {

		$this->model = new CampaignDraftModel( sanitize_text_field( $_POST['draft_id'] ) );

		parent::execute();
	}





	public function autocampaigner_cm_send_preview() {

		try {
			wp_die( $this->model->api_model->preview( 'post', [
				'PreviewRecipients' =>
					explode( ',', sanitize_text_field( $_POST['recipients'] ) ),
			] ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}

	}





	public function autocampaigner_reload_cm_info() {

		try {

			$data = $this->model->api_model->summary();
			wp_die( json_encode( $data ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}


	}





	public function autocampaigner_cm_schedule_campaign() {


		$draft = $this->model->api_model->schedule_campaign( $this->model, sanitize_text_field( $_POST['schedule'] ) );

		try {
			wp_die( json_encode( $draft ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	/**
	 * schedules a campaign on cm
	 */
	public function autocampaigner_unschedule_campaign() {

		try {

			$draft = $this->model->api_model->unschedule_campaign( $this->model );

			wp_die( json_encode( $draft ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	public function autocampaigner_create_draft_on_api() {

		try {

			$api = new CampaignApiModel();

			wp_die( json_encode( $api->create_draft( $this->model ) ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {

			wp_die( $e->getMessage(), 400 );

		}


	}


}