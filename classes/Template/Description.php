<?php

namespace Autocampaigner\Template;

use Autocampaigner\Options;



class Description implements \ArrayAccess {





	use Options;



	private $folder;





	private $description;





	/**
	 * @param false $folder
	 */
	public function __construct( $folder ) {

		$this->folder = $folder;

		$this->description = $this->get_description();

	}





	/**
	 *
	 * @return mixed|null decoded json file
	 */
	public function get_description() {

		return json_decode( file_get_contents( $this->get_templates_folder() . $this->folder . '/description.json' ) );

	}





	public function save() {

		$this->save_template_description( $this->description );
	}





	/**
	 * @param $description
	 */
	public function save_template_description( $description ) {

		file_put_contents( $this->get_templates_folder() . $this->folder . '/description.json', json_encode( $description ) );
	}





	public function __toString(): string {

		return htmlentities( json_encode( $this->get_description() ) );
	}





	public function offsetExists( $offset ) {

		return isset( $this->description->$offset );
	}





	public function offsetGet( $offset ) {

		return $this->description->$offset;
	}





	public function offsetSet( $offset, $value ) {

		$this->description->$offset = $value;
	}





	public function offsetUnset( $offset ) {

		unset( $this->description->$offset );
	}
}