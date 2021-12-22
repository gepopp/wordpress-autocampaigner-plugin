<?php

namespace Autocampaigner\controller;


class TemplatesController extends BaseController {

	protected $template_folder = AUTOCAMPAIGNER_DIR . '/email_templates/';

	protected $endpoints = [
		'list'    => 'https://api.createsend.com/api/v3.2/clients/{clientid}/templates.json',
		'create'  => 'https://api.createsend.com/api/v3.2/templates/{clientid}.json',
		'update'  => 'https://api.createsend.com/api/v3.2/templates/{templateid}.json',
		'details' => 'https://api.createsend.com/api/v3.2/templates/{templateid}.json',
	];


	function is_saved_on_cm( $template_name ) {

		$description = $this->template_description( $template_name );

		if ( empty( $description->TemplateID ) ) {
			return false;
		}

		$details = $this->details($description->TemplateID);

		if ( empty( $details->Name ) ) {
			return false;
		}

		return $description;

	}


	public function create_or_update_on_cm( $template_name ) {

		$description = $this->is_saved_on_cm( $template_name );

		if ( $description ) {
			return $this->update_template_on_cm( $template_name, $description->TemplateID );
		}

		$description = $this->template_description($template_name);
		$template_id = $this->create_template_on_cm( $template_name );

		if($template_id){

			$description->TemplateID = $template_id;
			file_put_contents( $this->template_folder . $template_name . '/description.json', json_encode( $description ) );
		}

		return $template_id;

	}

	public function create_template_on_cm( $template_name ) {

		return $this->call(
			$this->get_endpoint( $this->endpoints['create'] ),
			'post',
			$this->request_body( $template_name )
		);

	}

	public function update_template_on_cm( $template_name, $template_id ) {

		$this->itemId = $template_id;

		$endpoint = $this->get_endpoint($this->endpoints['update']);

		$call = $this->call(
			$endpoint,
			'PUT',
			$this->request_body( $template_name )
		);

		if($call !== false){
			return $template_id;
		}else{
			return false;
		}
	}


	public function request_body( $name ) {

		$description = $this->template_description($name);

		$body = [
			'Name'        => $description->Name,
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

	public function template_description( $name ) {

		$json = file_get_contents( $this->template_folder . $name . '/description.json' );

		return json_decode( $json );

	}


	public function get_existing_templates() {

		$templates = [];

		$dirs = glob( AUTOCAMPAIGNER_DIR . '/email_templates/*' );

		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . '/description.json' ) && file_exists( $dir . '/index.html' ) ) {
				$name        = explode( '/', $dir );
				$templates[] = array_pop( $name );
			}
		}

		return $templates;
	}


	public function get_templates_with_description(){

		$templates = $this->get_existing_templates();
		$with_description = [];

		foreach ($templates as $template){
			$with_description[$template] = $this->template_description($template);
		}

		return $with_description;
	}


	public function get_endpoint( $endpoint ) {

		$endpoint = parent::get_endpoint( $endpoint );

		if ( str_contains( $endpoint, 'templateid' ) ) {
			$endpoint = str_replace( '{templateid}', $this->itemId, $endpoint );
		}

		return $endpoint;
	}


}