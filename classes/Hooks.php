<?php

namespace Autocampaigner;


use Autocampaigner\controller\ListsController;
use Autocampaigner\controller\TemplatesController;

class Hooks {


	public function __construct() {

		add_action( 'wp_ajax_autocampainger_load_list_details', [ $this, 'autocampainger_load_list_details' ] );
		add_action( 'wp_ajax_autocampainger_update_used_lists', [ $this, 'autocampainger_update_used_lists' ] );
		add_action( 'wp_ajax_autocampainger_upload_template', [ $this, 'autocampainger_upload_template' ] );
		add_action( 'wp_ajax_autocampainger_get_templates', [ $this, 'autocampainger_get_templates' ] );
		add_action( 'wp_ajax_autocampaigner_safe_draft', [ $this, 'autocampaigner_safe_draft' ] );
		add_action( 'wp_ajax_autocampaigner_search_image', [ $this, 'autocampaigner_search_image' ] );
		add_action( 'wp_ajax_autocampaigner_load_tempalte_html', [ $this, 'autocampaigner_load_tempalte_html' ] );
		add_action( 'wp_ajax_autocampaigner_save_content', [ $this, 'autocampaigner_save_content' ] );
		add_action( 'admin_post_autocampaigner_create_campaign', [ $this, 'autocampaigner_create_campaign' ] );
		add_action( 'admin_post_autocampaigner_schedule_campagin', [ $this, 'autocampaigner_schedule_campagin' ] );
		add_action( 'admin_post_autocampaigner_sent_campaign', [ $this, 'autocampaigner_sent_campaign' ] );

	}


	public function autocampaigner_sent_campaign(){
		$draft = new CampaignDrafts();
		wp_die($draft->send());
	}


	public function autocampaigner_schedule_campagin(){
		$draft = new CampaignDrafts();
		wp_die($draft->create_draft());
	}



	public function autocampaigner_save_content(){

		$this->verify_nonce();
		$draft = new CampaignDrafts();
		wp_die($draft->save_content());
	}


	public function autocampaigner_load_tempalte_html() {

		$this->verify_nonce();

		$parser = new TemplateParser( sanitize_text_field( $_POST['folder'] ) );

		wp_die($parser->replace_multinines());

	}


	public function autocampaigner_search_image() {

		$this->verify_nonce();

		$args = [
			'post_type'      => 'attachment',
			'posts_per_page' => 15,
			's'              => $_POST['search'],
			'post_status'    => 'any',
		];

		$results = new \WP_Query( $args );

		$titles = [];

		if ( $results->have_posts() ) {
			while ( $results->have_posts() ) {
				$results->the_post();
				$titles[] = [
					'id'        => get_the_ID(),
					'title'     => get_the_title(),
					'thumbnail' => wp_get_attachment_thumb_url( get_the_ID(), 'thumbnail' ),
					'full'      => wp_get_attachment_image_url( get_the_ID(), 'full' ),
				];
			}
		}
		wp_die( json_encode( $titles ) );
	}

	public function autocampaigner_create_campaign() {

		$draft = new CampaignDrafts();
		wp_die($draft->save());

	}

	public function autocampainger_get_templates() {

		$this->verify_nonce();

		$controller = new TemplatesController();

		$list = $controller->list();

		wp_die( json_encode( $list ) );
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

	public function verify_nonce() {
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'wp_rest' ) ) {
			wp_die( 'hack', 400 );
		}
	}
}