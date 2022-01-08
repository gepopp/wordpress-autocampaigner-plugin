<?php

namespace Autocampaigner\Controller;


abstract class BaseController {





	public function __construct( $id = null ) {

		$classname = get_class( $this );

		$model_path = str_replace( 'Controller', 'Model', $classname );


		if ( class_exists( $model_path ) ) {


			$var_name = explode( '\\', $model_path );
			$var_name = array_pop( $var_name );
			$var_name = strtolower( $var_name );

			$this->{$var_name} = new $model_path( $id );


		}


	}





	public function get_as_html_attribute( $value ) {

		return htmlentities( json_encode( $value ) );
	}





	public function as_html_attribute( $function ) {

		echo $this->get_as_html_attribute( $function );
	}


}