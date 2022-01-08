<?php

namespace Autocampaigner\Model;


use Autocampaigner\Options;



/**
 *
 */
abstract class BaseModel {





	use Options;



	/**
	 * @var bool|string|int $id
	 */
	private $id;






	/**
	 * loads data from API or db if id is given
	 *
	 *
	 * @param null|string|int $id
	 */
	public function __construct( $id = null ) {



		/**
		 * @var bool|string|int $id
		 */
		if ( ! is_null( $id ) ) {

			$this->id = $id;

			$this->load();
		}

	}





	/**
	 * refresh model data if id changes
	 *
	 * @param string $name
	 * @param        $value
	 */
	public function __set( string $name, $value ): void {

		if ( $name == 'id' ) {
			$this->id = $value;
			$this->load();

		}
	}





	/**
	 * @param string $name
	 */
	public function __get( string $name ) {

		if ( $name == 'id' ) {
			return $this->id;
		}
	}








}