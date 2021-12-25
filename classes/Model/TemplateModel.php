<?php

namespace Autocampaigner\Model;

use Autocampaigner\Options;
use Autocampaigner\Template\Description;
use Carbon\Exceptions\UnknownMethodException;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class TemplateModel extends BaseModel {





	use Options;



	protected $folder;





	public $description;



	public function set_folder($folder){

		$this->folder = $folder;
		$this->description = new Description( $folder );
		$this->id = $this->description['TemplateID'];
	}





	/**
	 * @param $folder where template lives
	 *
	 * @return string cm template id
	 */
	public function create_or_update_on_cm( $folder = false ) {

		if($folder){
			$this->set_folder($folder);
		}

		try{
			$template_details = $this->details();
			if(empty($template_details)){
				return $this->create_template();
			}
			return $this->update('put', $this->request_body());
		}catch (CmApiCallUnsuccsessfull $e){

		}


	}

	public function create_template(){

		$template_id = $this->create('post', $this->request_body() );

		$description = new Description($this->folder);
		$description['TemplateID'] = $template_id;
		$description->save();

		return $template_id;


	}





	/**
	 * @param $folder where template lives
	 *
	 * @return array
	 */
	public function     request_body(  ) {

		$htmlurl = AUTOCAMPAIGNER_URL . '/email_templates/' . $this->folder . '/index.html';
		$zipurl  = AUTOCAMPAIGNER_URL . '/email_templates/' . $this->folder . '/images.zip';


		$body = [
			'Name'        => $this->description['Name'],
			'HtmlPageURL' => $htmlurl,
		];

		if ( $this->has_zip( $this->folder ) ) {
			$body['ZipFileURL'] = $zipurl;
		}


		return $body;

	}





	/**
	 * @return array of Template Folders with description.json file contents
	 */
	public function get_templates_with_description() {

		$with_description = [];

		foreach ( $this->get_valid_template_folders() as $folder ) {
			$with_description[ $folder ] = ( new Description( $folder ) )->get_description();
		}

		return $with_description;
	}





	/**
	 * @return array of folders in the templates directory that contains min index and description
	 */
	public function get_valid_template_folders() {

		$templates_folders = [];

		foreach ( glob( $this->get_templates_folder() . '*' ) as $dir ) {

			// a teplate must have at least an index and an description file
			if ( file_exists( $dir . '/description.json' ) && file_exists( $dir . '/index.html' ) ) {
				$folder              = explode( '/', $dir );
				$templates_folders[] = array_pop( $folder );
			}
		}

		return $templates_folders;
	}





	/**
	 * @param $folder
	 *
	 * @return bool if zip file in template folder
	 */
	public function has_zip( $folder ) {

		return file_exists( $this->get_templates_folder() . $folder . '/images.zip' );

	}


}