<?php

namespace Autocampaigner\models;

use Autocampaigner\CampaignMonitorApi;

abstract class BaseModel {

	public $contoller;

	abstract protected function render();

	protected function connected(){
		return  ( new CampaignMonitorApi() )->test_connection();
	}


	public function __call( string $name, array $arguments ) {

		if($name == 'render'){

			if(!$this->connected()){
				return false;
			}

			return $this->render();

		}

	}



}