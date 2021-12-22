<?php

namespace Autocampaigner\models;

use Autocampaigner\CampaignMonitorApi;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;

abstract class BaseModel {

	public $controller;

	abstract protected function render();


	public function __construct() {

		$classname = get_class($this);
		$classname = str_replace('Model', 'Controller', $classname);
		$classname = str_replace('models', 'controller', $classname);

		if(class_exists($classname)){
			$this->controller = new $classname;
		}

	}


	protected function connected(){
		return  ( new CampaignMonitorApi() )->test_connection();
	}


	public function __call( string $name, array $arguments ) {

		if($name == 'render'){

			try{
				$this->connected();
			}catch (CmApiCallUnsuccsessfull $exception){

				$error = '<span>' . $exception->getMessage() . '</span><br><span class="ac-text-sm">'. $exception->getFile() . '::' . $exception->getLine() .'</span>';
				ob_start();
				include AUTOCAMPAIGNER_DIR . '/templates/admin/error.php';
				return ob_get_clean();
			}

			return $this->render();

		}

	}



}