<?php

namespace Autocampaigner;


use Autocampaigner\controller\ListsController;
use Autocampaigner\controller\TemplatesController;



class Hooks {





	use Queries;



	public function __construct() {


		add_action( 'after_setup_theme', [ $this, 'update_images_sizes' ] );


		add_action( 'wp_ajax_autocampainger_load_list_details', [ $this, 'autocampainger_load_list_details' ] );
		add_action( 'wp_ajax_autocampainger_update_used_lists', [ $this, 'autocampainger_update_used_lists' ] );
		add_action( 'wp_ajax_autocampainger_upload_template', [ $this, 'autocampainger_upload_template' ] );
		add_action( 'wp_ajax_autocampainger_get_templates', [ $this, 'autocampainger_get_templates' ] );
		add_action( 'wp_ajax_autocampaigner_safe_draft', [ $this, 'autocampaigner_safe_draft' ] );
		add_action( 'wp_ajax_autocampaigner_search_image', [ $this, 'autocampaigner_search_image' ] );
		add_action( 'wp_ajax_autocampaigner_load_tempalte_html', [ $this, 'autocampaigner_load_tempalte_html' ] );
		add_action( 'wp_ajax_autocampaigner_save_content', [ $this, 'autocampaigner_save_content' ] );
		add_action( 'wp_ajax_autocampaigner_search_posts', [ $this, 'autocampaigner_search_posts' ] );
		add_action( 'admin_post_autocampaigner_create_campaign', [ $this, 'autocampaigner_create_campaign' ] );
		add_action( 'admin_post_autocampaigner_schedule_campagin', [ $this, 'autocampaigner_schedule_campagin' ] );
		add_action( 'admin_post_autocampaigner_sent_campaign', [ $this, 'autocampaigner_sent_campaign' ] );

	}





	function update_images_sizes() {

		add_theme_support( 'post-thumbnails' );


		$template_controller = new TemplatesController();

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





	public function autocampaigner_search_posts() {

		$this->verify_nonce();
		$results = $this->search_posts(
			sanitize_text_field( $_POST['search'] ),
			sanitize_text_field( $_POST['type'] ) == '' ? 'post' : sanitize_text_field( $_POST['type'] )
		);
		wp_die( json_encode( $results ) );

	}





	public function autocampaigner_sent_campaign() {

//		$this->verify_nonce( 'sent_campaign' );
		wp_die( ( new CampaignDrafts() )->send() );
	}





	public function autocampaigner_schedule_campagin() {

		$this->verify_nonce( 'create_campaign' );
		wp_die( ( new CampaignDrafts() )->create_draft() );

	}





	public function autocampaigner_save_content() {

		$this->verify_nonce();
		wp_die( ( new CampaignDrafts() )->save_content() );
	}





	public function autocampaigner_load_tempalte_html() {

		$this->verify_nonce();
		$parser = new TemplateParser( sanitize_text_field( $_POST['folder'] ) );
		wp_die( $parser->parse_template_html_for_editor() );

	}





	public function autocampaigner_search_image() {

		$this->verify_nonce();
		wp_die( json_encode( $this->search_images( sanitize_text_field( $_POST['search'] ) ) ) );
	}





	public function autocampaigner_create_campaign() {

		( new CampaignDrafts() )->save();

	}





	public function autocampainger_get_templates() {

		$this->verify_nonce();
		wp_die( json_encode( ( new TemplatesController() )->list() ) );
	}





	public function autocampainger_upload_template() {

		$this->verify_nonce();
		wp_die( ( new TemplatesController() )->create_or_update_on_cm( sanitize_text_field( $_POST['template_name'] ) ) );

	}





	public function autocampainger_load_list_details() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListsController() )->stats( sanitize_text_field( $_POST['list_id'] ) ) ) );

	}





	public function autocampainger_update_used_lists() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListsController() )->toggle_used_list( $_POST['lists'] ) ) );


	}





	public function verify_nonce( $nonce = false ) {

		$nonce = ! $nonce ? 'wp_rest' : $nonce;

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), $nonce ) ) {
			wp_die( 'hack', 400 );
		}
	}
}