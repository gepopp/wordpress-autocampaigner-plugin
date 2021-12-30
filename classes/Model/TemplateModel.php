<?php

namespace Autocampaigner\Model;

use Autocampaigner\Options;
use Autocampaigner\Template\Description;
use Autocampaigner\exceptions\RessourceNotExist;



class TemplateModel extends BaseModel {





	use Options;



	public Description $description_file;





	public $name;





	public $description;





	public $folder;





	public $images;





	public function load() {

		if ( $this->is_valid() ) {


			$this->description_file = new Description( $this->id );

			$this->name        = $this->description_file['Name'] ?? '';
			$this->description = $this->description_file['Description'] ?? '';
			$this->images      = $this->description_file['ImageSizes'] ?? [];
			$this->folder      = $this->id;


		} else {
			throw new RessourceNotExist( $this->id );
		}

	}





	public function update() {

		$this->description_file->save();
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
	public function all() {

		$templates_folders = [];

		foreach ( glob( $this->get_templates_folder() . '*' ) as $dir ) {

			$folder = explode( '/', $dir );
			$folder = array_pop( $folder );

			if ( $this->is_valid( $folder ) ) {

				$templates_folders[] = new TemplateModel( $folder );
			}

		}


		return $templates_folders;
	}





	public function is_valid( $folder = false ) {

		if ( ! $folder ) {
			$folder = $this->id;
		}

		$path = $this->get_templates_folder() . $folder;

		// a teplate must have at least an index and an description file
		return file_exists( $path . '/description.json' ) && file_exists( $path . '/index.html' );

	}





	/**
	 * @param $folder
	 *
	 * @return bool if zip file in template folder
	 */
	public function has_zip() {

		return file_exists( $this->get_templates_folder() . $this->id . '/images.zip' );

	}




	public function create() {
	}





	public function delete() {
	}


}