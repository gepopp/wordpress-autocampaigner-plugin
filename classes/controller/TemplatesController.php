<?php

namespace Autocampaigner\controller;


class TemplatesController extends BaseController {

	protected $template_folder = AUTOCAMPAIGNER_DIR . '/email_templates/';

	protected $endpoints = [
		'list'   => 'https://api.createsend.com/api/v3.2/clients/{clientid}/templates.json',
		'create' => 'https://api.createsend.com/api/v3.2/templates/{clientid}.json',
		'update' => 'https://api.createsend.com/api/v3.2/templates/{templateid}.json',
		'details'    => 'https://api.createsend.com/api/v3.2/templates/{templateid}.json',
	];



	function is_saved_on_cm( $template_name ) {

		$description = $this->template_description($template_name);

		if( empty($description->TemplateID)) return false;

		$details = $this->details();

		var_dump($details);


	}

	public function template_description($name){

		$json = file_get_contents( $this->template_folder . $name .'/description.json' );
		return json_decode($json);

	}

	

	public function get_existing_templates() {

		$templates = [];

		$dirs = glob( AUTOCAMPAIGNER_DIR . '/email_templates/*' );

		foreach ( $dirs as $dir ) {
			if ( file_exists( $dir . '/index.html' && file_exists($dir . 'description.json') ) ) {
				$name        = explode( '/', $dir );
				$templates[] = array_pop( $name );
			}
		}

		return $templates;
	}

	public function create_or_update_on_cm($template_name){

		$template_id = $this->is_saved_on_cm($template_name);

		if($template_id){
			return  $this->update_template_on_cm($template_name, $template_id) ;
		}

		return $this->create_template_on_cm($template_name);

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

		$template_id = $this->call(
			$this->get_endpoint( $this->endpoints['create'] ),
			'post',
			$this->request_body( $template_name )
		);

		return $template_id;
		
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