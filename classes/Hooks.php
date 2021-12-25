<?php

namespace Autocampaigner;


use Autocampaigner\Model\ListModel;
use Autocampaigner\Model\TemplateModel;
use Autocampaigner\Model\CampaignApiModel;
use Autocampaigner\Model\CampaignDraftModel;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class Hooks {





	use Queries;



	public function __construct() {

		// ads image sizes from tempalte descriptions
		add_action( 'after_setup_theme', [ $this, 'update_images_sizes' ] );


		// loads the details from subscriber list
		add_action( 'wp_ajax_autocampainger_load_list_details', [ $this, 'autocampainger_load_list_details' ] );

		// updates the option of lists to send campaigns
		add_action( 'wp_ajax_autocampainger_update_used_lists', [ $this, 'autocampainger_update_used_lists' ] );

		//uploads or updates templates on cm
		add_action( 'wp_ajax_autocampaigner_update_template_on_cm', [ $this, 'autocampainger_upload_template' ] );

		// gets the templates from cm
		add_action( 'wp_ajax_autocampainger_get_templates', [ $this, 'autocampainger_get_templates' ] );

		// image search for editor
		add_action( 'wp_ajax_autocampaigner_search_image', [ $this, 'autocampaigner_search_image' ] );

		// search posts for editor
		add_action( 'wp_ajax_autocampaigner_search_posts', [ $this, 'autocampaigner_search_posts' ] );

		// saves content from editor
		add_action( 'wp_ajax_autocampaigner_save_content', [ $this, 'autocampaigner_save_content' ] );


		// saves content to given draft
		add_action( 'admin_post_autocampaigner_save_draft_content', [ $this, 'autocampaigner_save_draft_content' ] );


		// creates a cm draft by given local draft id
		add_action( 'wp_ajax_autocampaigner_create_draft_on_cm', [ $this, 'create_draft_on_cm' ] );

		// loads draft details by campaing id
		add_action( 'wp_ajax_autocampaigner_load_cm_draft_details', [ $this, 'load_cm_draft_details' ] );


		// send a preciew from cm draft
		add_action( 'wp_ajax_autocampaigner_cm_send_preview', [ $this, 'cm_send_preview' ] );

		//schedule a draft on cm
		add_action( 'wp_ajax_autocampaigner_cm_schedule_campaign', [ $this, 'cm_schedule_draft' ] );

		//get details of sent campaign
		add_action( 'wp_ajax_autocampaigner_get_cm_campaigninfo', [ $this, 'get_cm_campaigninfo' ] );

		// reloads cm cmapaign statis info
		add_action( 'wp_ajax_autocampaigner_reload_cm_info', [ $this, 'load_cm_draft_status' ] );

		// set scheduled campaign back to draft
		add_action( 'wp_ajax_autocampaigner_unschedule_campaign', [ $this, 'unschedule_campaign' ] );


		add_action( 'admin_post_autocampaigner_sent_campaign', [ $this, 'autocampaigner_sent_campaign' ] );

		// creates or updates local draft
		add_action( 'admin_post_autocampaigner_create_draft', [ $this, 'create_draft' ] );

	}





	/**
	 * Creates or updates a local draft in the db
	 */
	public function create_draft() {

		$this->verify_nonce();
		$model = new CampaignDraftModel();

		if ( $model->update_or_create() ) {
			wp_safe_redirect(
				add_query_arg( [
					'draft'  => $model->get_id(),
					'screen' => 'edit',
				],
					$_SERVER['HTTP_REFERER'] ) );
			exit();
		}

		global $wpdb;
		wp_die( $wpdb->last_error );
	}





	public function unschedule_campaign() {

		$this->verify_nonce();
		try {
			wp_die( ( new CampaignApiModel(sanitize_text_field($_POST['cm_id'])) )->unschedule('post') );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	public function load_cm_draft_status() {

		$this->verify_nonce();
		try {

			wp_die( json_encode( ( new CampaignApiModel() )->determine_cm_campaign_status( sanitize_text_field( $_POST['cm_id'] ) ) ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	public function get_cm_campaigninfo() {

		$this->verify_nonce();

		try {

			wp_die( json_encode( ( new CampaignApiModel( sanitize_text_field() ) )->summary() ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	public function cm_schedule_draft() {


		$this->verify_nonce();
		try {
			wp_die( (new CampaignApiModel(sanitize_text_field($_POST['cm_id'])))
				->schedule_campaign( sanitize_text_field( $_POST['schedule'] ) ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}
	}





	public function cm_send_preview() {

		$this->verify_nonce();

		try {
			wp_die( ( new CampaignApiModel( sanitize_text_field( $_POST['cm_id'] ) ) )
				->preview( 'post', [
					'PreviewRecipients' =>
						explode( ',', sanitize_text_field( $_POST['recipients'] ) ),
				] ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}

	}





	public function load_cm_draft_details() {

		$this->verify_nonce();

		try {

			wp_die( json_encode( ( new CampaignApiModel() )->get_cm_draft_details( $_POST['cm_id'] ) ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {

			wp_die( $e->getMessage(), 400 );

		}


	}





	public function create_draft_on_cm() {

		$this->verify_nonce();

		try {

			wp_die( ( new CampaignApiModel() )->create_draft( sanitize_text_field( $_POST['draft'] ) ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {

			wp_die( $e->getMessage(), 400 );

		}


	}





	public function update_images_sizes() {

		add_theme_support( 'post-thumbnails' );


		$template_controller = new TemplateModel();

		$templates = $template_controller->get_templates_with_description();


		foreach ( $templates as $description ) {

			if ( isset( $description->ImageSizes ) ) {

				foreach ( $description->ImageSizes as $image_size ) {
					add_image_size(
						$image_size->Name,
						$image_size->width,
						$image_size->height,
						$image_size->crop
					);
				}
			}
		}
	}





	public function autocampaigner_sent_campaign() {

//		$this->verify_nonce( 'sent_campaign' );
		wp_die( ( new CampaignDrafts() )->send() );
	}





	public function autocampaigner_save_draft_content() {

		$this->verify_nonce();
		$draft = new CampaignDraftModel();

		if ( $draft->save_content() ) {
			wp_safe_redirect( add_query_arg( 'screen', 'send', $_SERVER['HTTP_REFERER'] ) );
			exit;
		}

		global $wpdb;
		wp_die( $wpdb->last_error );

	}





	public function autocampainger_upload_template() {

		$this->verify_nonce();
		try {
			wp_die( ( new TemplateModel() )->create_or_update_on_cm( sanitize_text_field( $_POST['template_name'] ) ) );
		} catch ( CmApiCallUnsuccsessfull $e ) {
			wp_die( $e->getMessage(), 400 );
		}

	}





	public function autocampainger_load_list_details() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListModel( sanitize_text_field( $_POST['list_id'] ) ) )->stats() ) );

	}





	public function autocampainger_update_used_lists() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListModel() )->toggle_used_list( $_POST['lists'] ) ) );


	}





	public function autocampaigner_search_image() {

		$this->verify_nonce();
		wp_die( json_encode( $this->search_images( sanitize_text_field( $_POST['search'] ) ) ) );
	}





	public function autocampaigner_search_posts() {

		$this->verify_nonce();
		$results = $this->search_posts(
			sanitize_text_field( $_POST['search'] ),
			sanitize_text_field( $_POST['type'] ) == '' ? 'post' : sanitize_text_field( $_POST['type'] )
		);
		wp_die( json_encode( $results ) );

	}





	public function verify_nonce( $nonce = false ) {

		$nonce = ! $nonce ? 'wp_rest' : $nonce;

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), $nonce ) ) {
			wp_die( 'hack', 400 );
		}
	}
}