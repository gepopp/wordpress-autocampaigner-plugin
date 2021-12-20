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

	}

	public function autocampainger_get_templates(){

		$this->verify_nonce();
		wp_die(( new TemplatesController())->get_as_html_attribute('list'));
	}

	public function autocampainger_upload_template(){

		$this->verify_nonce();
		wp_die( ( new TemplatesController())->create_or_update_on_cm(sanitize_text_field($_POST['template_name'])));

	}

	public function autocampainger_load_list_details() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListsController() )->stats( sanitize_text_field( $_POST['list_id'] ) ) ) );

	}

	public function autocampainger_update_used_lists() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListsController() )->toggle_used_list( $_POST['list_id'] ) ) );


	}

	public function verify_nonce(){
		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'wp_rest' ) ) {
			wp_die( 'hack', 400 );
		}
	}
}