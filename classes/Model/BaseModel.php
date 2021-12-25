<?php

namespace Autocampaigner\Model;


use Autocampaigner\Options;



abstract class BaseModel {





	use Options;



	public $id;





	public function __construct( $id = false ) {

		$this->id = $id;

	}





	public function get_id() {

		return $this->id;
	}





	public function __call( string $name, array $arguments ) {

		$driver = $this->getDriver();

		$model = get_class( $this );

		$model = explode( '\\', $model );
		$model = array_pop( $model );
		$model = strtolower( str_replace( 'model', '', $model ) );

		$endpoint = $driver->get_endpoint( $model, $name, $this->id );

		return $driver->call( $endpoint, $arguments[0] ?? 'get', $arguments[1] ?? [] );

	}





	public function get_as_html_attribute( $function ) {

		$result = $this->{$function}();

		return htmlentities( json_encode( $result ) );
	}





	public function as_html_attribute( $function ) {

		echo $this->get_as_html_attribute( $function );
	}





	/**
	 * @return mixed
	 */
	protected function getDriver() {

		$driver_name = 'Autocampaigner\\' . $this->get_driver() . '\\' . $this->get_driver();
		$driver      = new $driver_name;

		return $driver;
	}

}