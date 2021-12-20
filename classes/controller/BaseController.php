<?php

namespace Autocampaigner\controller;

use Autocampaigner\CampaignMonitorApi;

abstract class BaseController extends CampaignMonitorApi {


	protected $itemId;
	protected $endpoints;


	public function __call( string $name, array $arguments ) {


		if ( array_key_exists( $name, $this->endpoints ) ) {

			if(!empty($arguments)){
				$this->itemId = $arguments[0];
			}

			return $this->call( $this->get_endpoint( $this->endpoints[ $name ] ) );
		}

	}

	public function get_as_html_attribute($function){
		$result = $this->{$function}();
		return htmlentities( json_encode( $result ) );
	}

	public function as_html_attribute($function){
		echo $this->get_as_html_attribute($function);
	}

}