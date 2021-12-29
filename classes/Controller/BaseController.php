<?php

namespace Autocampaigner\Controller;



abstract class BaseController {



	public function get_as_html_attribute( $value ) {

		return htmlentities( json_encode( $value ) );
	}





	public function as_html_attribute( $function ) {

		echo $this->get_as_html_attribute( $function );
	}


}