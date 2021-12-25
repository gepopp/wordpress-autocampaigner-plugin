<?php

namespace Autocampaigner\Controller;



abstract class BaseController {


	public function __construct() {


		$classname = get_class($this);
		$classname = str_replace('Controller', 'Model', $classname);



		if(class_exists($classname)){
			$this->model = new $classname;
		}


	}


}