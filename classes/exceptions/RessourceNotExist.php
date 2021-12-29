<?php

namespace Autocampaigner\exceptions;

use Throwable;



class RessourceNotExist extends \Exception {





	public function __construct( $message = "", $code = 0, Throwable $previous = null ) {

		parent::__construct( $message, $code, $previous );
	}


}