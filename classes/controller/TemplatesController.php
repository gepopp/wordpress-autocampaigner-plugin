<?php

namespace Autocampaigner\controller;


class TemplatesController extends BaseController {


	protected $endpoints = [
		'list'   => 'https://api.createsend.com/api/v3.2/clients/{clientid}/templates.json',
		'create' => 'https://api.createsend.com/api/v3.2/templates/{clientid}.json',
		'update' => 'https://api.createsend.com/api/v3.2/templates/{templateid}.json',
		'get'    => 'https://api.createsend.com/api/v3.2/templates/{templateid}.json',
	];


	public function saved_local_templates() {

		$saved = get_option( 'autocampaigner-uploaded-templates' );
		if ( ! is_array( $saved ) ) {
			$saved = [];
		}

		return $saved;

	}


	function is_saved_on_cm( $template_name ) {

		$local_templates_saved = $this->saved_local_templates();

		if ( ! array_key_exists( $template_name, $local_templates_saved ) ) {
			return false;
		}

		$template_details = $this->get( $local_templates_saved[ $template_name ] );

		if ( ! $template_details ) {
			unset( $local_templates_saved[ $template_name ] );
			$this->save_templates_on_cm( $local_templates_saved );

			return false;
		}

		return $local_templates_saved[ $template_name ];

	}


	function save_templates_on_cm( $templates ) {
		update_option( 'autocampaigner-uploaded-templates', $templates );
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

	public function create_or_update_on_cm($template_name){

		$template_id = $this->is_saved_on_cm($template_name);

		return $template_id ? $this->update_template_on_cm($template_name, $template_id) : $this->create_template_on_cm($template_name);

	}

	public function update_template_on_cm( $template_name, $template_id ) {

		$this->itemId = $template_id;

		return $this->call(
			$this->get_endpoint( $this->endpoints['update'] ),
			'PUT',
			$this->request_body( $template_name )
		);
	}


	public function create_template_on_cm( $template_name ) {

		return $this->call(
			$this->get_endpoint( $this->endpoints['create'] ),
			'post',
			$this->request_body( $template_name )
		);

	}

	public function request_body( $name ) {

		$body = [
			'Name'        => $name,
			'HtmlPageURL' => AUTOCAMPAIGNER_URL . '/email_templates/' . $name . '/index.html',
		];

		if ( $this->has_zip( $name ) ) {
			$body['ZipFileURL'] = AUTOCAMPAIGNER_URL . '/email_templates/' . $name . '/images.zip';
		}

		return $body;

	}


	public function has_zip( $template_name ) {

		return file_exists( AUTOCAMPAIGNER_DIR . '/email_templates/' . $template_name . '/images.zip' );

	}


	public function get_endpoint( $endpoint ) {

		$endpoint = parent::get_endpoint( $endpoint );

		if ( str_contains( $endpoint, 'templateid' ) ) {
			$endpoint = str_replace( '{templateid}', $this->itemId, $endpoint );
		}

		return $endpoint;
	}


}