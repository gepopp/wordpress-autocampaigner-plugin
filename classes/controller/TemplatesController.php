<?php

namespace Autocampaigner\controller;


use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;

class TemplatesController extends BaseController {


	/**
	 * @var string $template_folder where templates are stored
	 */
	protected $template_folder = AUTOCAMPAIGNER_DIR . '/email_templates/';


	/**
	 * @var string $endpoint_str to replace in endpoint with $item_id
	 */
	protected $endpoint_str = 'templateid';


	/**
	 * @var string[] used api endpoints
	 */
	protected $endpoints = [
		'list'    => 'clients/{clientid}/templates.json',
		'create'  => 'templates/{clientid}.json',
		'update'  => 'templates/{templateid}.json',
		'details' => 'templates/{templateid}.json',
	];







	/**
	 * @param $folder where template lives
	 *
	 * @return string cm template id
	 */
	public function create_or_update_on_cm( $folder ) {

		$template_id = $this->is_saved_on_cm( $folder );

		if ( $template_id ) {
			return $this->update_template_on_cm( $folder, $template_id );
		}

		$template_id = $this->create_template_on_cm( $folder );

		$description             = $this->get_template_description( $folder );
		$description->TemplateID = $template_id;
		$this->save_template_description( $folder, $description );

		return $template_id;

	}






	/**
	 * @param $folder of the local stored template
	 *
	 * @return false|mixed wether an template id exists in description and is not deleted on cm
	 */
	function is_saved_on_cm( $folder ) {

		$description = $this->get_template_description( $folder );

		if ( empty( $description->TemplateID ) ) {
			// was never uploaded to cm
			return false;
		}

		try {
			$details = $this->details( $description->TemplateID );
		} catch ( CmApiCallUnsuccsessfull $exception ) {
			return false;
		}

		// template has been deleted on cm
		if ( empty( $details->Name ) ) {
			return false;
		}

		return $description->TemplateID;

	}





	/**
	 * @param $template_name
	 *
	 * @return mixed|string|void|null
	 */
	public function create_template_on_cm( $template_name ) {

		try{
			return $this->call(
				$this->get_endpoint( 'create' ),
				'post',
				$this->request_body( $template_name )
			);
		}catch (CmApiCallUnsuccsessfull $e){
			wp_die($e->getMessage(), 400);
		}


	}






	/**
	 * @param $folder where template lives
	 * @param $template_id on cm
	 */
	public function update_template_on_cm( $folder, $template_id ) {

		$this->itemId = $template_id;

		$endpoint = $this->get_endpoint( 'update' );

		try{
			$this->call(
				$endpoint,
				'PUT',
				$this->request_body( $folder )
			);
			return $template_id;
		}catch (CmApiCallUnsuccsessfull $e){
			wp_die($e->getMessage(), 400);
		}

	}





	/**
	 * @param $folder where template lives
	 *
	 * @return array
	 */
	public function request_body( $folder ) {

		$description = $this->get_template_description( $folder );

		$body = [
			'Name'        => $description->Name,
			'HtmlPageURL' => AUTOCAMPAIGNER_URL . '/email_templates/' . $folder . '/index.html',
		];

		if ( $this->has_zip( $folder ) ) {
			$body['ZipFileURL'] = AUTOCAMPAIGNER_URL . '/email_templates/' . $folder . '/images.zip';
		}

		return $body;

	}





	/**
	 * @param $folder
	 *
	 * @return bool if zip file in template folder
	 */
	public function has_zip( $folder ) {

		return file_exists( AUTOCAMPAIGNER_DIR . '/email_templates/' . $folder . '/images.zip' );

	}




	/**
	 * @param $folder
	 *
	 * @return mixed|null decoded json file
	 */
	public function get_template_description( $folder ) {

		$json = file_get_contents( $this->template_folder . $folder . '/description.json' );

		return json_decode( $json );

	}




	/**
	 * @param $folder where template lives
	 * @param $description json
	 */
	public function save_template_description( $folder, $description ) {
		file_put_contents( $this->template_folder . $folder . '/description.json', json_encode( $description ) );
	}




	/**
	 * @return array of folders in the templates directory that contains min index and description
	 */
	public function get_existing_templates() {

		$templates_folders = [];

		foreach ( glob( $this->template_folder . '*' ) as $dir ) {

			if ( file_exists( $dir . '/description.json' ) && file_exists( $dir . '/index.html' ) ) {
				$folder              = explode( '/', $dir );
				$templates_folders[] = array_pop( $folder );
			}
		}

		return $templates_folders;
	}


	/**
	 * @return array of Template Folders with description.json file contents
	 */
	public function get_templates_with_description() {

		$with_description = [];

		foreach ( $this->get_existing_templates() as $template ) {
			$with_description[ $template ] = $this->get_template_description( $template );
		}

		return $with_description;
	}


}