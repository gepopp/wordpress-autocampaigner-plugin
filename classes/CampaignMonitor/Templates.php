<?php

namespace Autocampaigner\CampaignMonitor;

class Templates extends \Autocampaigner\CampaignMonitorApi {


	private $endpoints = [
		'get'    => 'https://api.createsend.com/api/v3.2/clients/{clientid}/templates.json',
		'create' => 'https://api.createsend.com/api/v3.2/templates/{clientid}.json',
		'update' => 'https://api.createsend.com/api/v3.2/templates/{templateid}.json',
	];

	private $templateID = '';

	public function list() {

		return $this->call( $this->endpoints['get'] );

	}


	public function __call( string $name, array $arguments ) {


		if ( array_key_exists( $name, $this->endpoints ) ) {

			$this->templateID = $arguments[0];

			return $this->call( $this->get_endpoint( $this->endpoints[ $name ] ) );
		}

	}

	public function get_endpoint( $endpoint ) {

		if ( str_contains( $endpoint, 'templateid' ) ) {
			$endpoint = str_replace( '{templateid}', $this->templateID, $endpoint );
		}

		return parent::get_endpoint( $endpoint );
	}

	public function saved_local_templates() {

		$saved = get_option( 'autocampaigner-uploaded-templates' );
		if ( !is_array($saved) ) {
			$saved = [];
		}

		return  $saved;


	}

	public function get_existing_templates() {

		$templates = [];

		$dirs = glob( AUTOCAMPAIGNER_DIR . '/email_templates/*' );

		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . '/index.html' ) ) {
				$name        = explode( '/', $dir );
				$templates[] = array_pop( $name );
			}
		}

		return $templates;

	}


	public function save_template_on_cm( $template_name ) {

		$saved = $this->saved_local_templates();


		if ( array_key_exists( $template_name, $saved ) ) {

			$this->templateID = $saved[$template_name];

			$route  = $this->get_endpoint( $this->endpoints['update'] );
			$method = 'PUT';
		} else {

			$route  = $this->get_endpoint( $this->endpoints['create'] );
			$method = 'post';
		}

		$index_url = AUTOCAMPAIGNER_URL . '/email_templates/' . $template_name . '/index.html';

		if ( file_exists( AUTOCAMPAIGNER_DIR . '/email_templates/' . $template_name . '/images.zip' ) ) {
			$assets = AUTOCAMPAIGNER_URL . '/email_templates/' . $template_name . '/images.zip';
		} else {
			$assets = '';
		}

		$id = $this->call( $route, $method, [ 'Name' => $template_name, 'HtmlPageURL' => $index_url, 'ZipFileURL' => $assets ] );


		if(!$id) return false;


		$saved[$template_name] = $id;


		update_option('autocampaigner-uploaded-templates', $saved);

		return $id;

	}
}