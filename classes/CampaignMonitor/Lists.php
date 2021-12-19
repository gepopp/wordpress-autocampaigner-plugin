<?php

namespace Autocampaigner\CampaignMonitor;

use function DeliciousBrains\WP_Offload_Media\Aws3\Aws\map;

class Lists extends \Autocampaigner\CampaignMonitorApi {


	private $endpoints = [
		'get'     => 'https://api.createsend.com/api/v3.2/clients/{clientid}/lists.json',
		'details' => 'https://api.createsend.com/api/v3.2/lists/{listid}.json',
		'stats'   => 'https://api.createsend.com/api/v3.2/lists/{listid}/stats.json'
	];

	private $listid = '';

	public function list() {

		return $this->call( $this->endpoints['get'] );

	}


	public function __call( string $name, array $arguments ) {


		if(array_key_exists($name, $this->endpoints)){

			$this->listid = $arguments[0];

			return $this->call($this->get_endpoint($this->endpoints[$name]));
		}

	}


	public function toggle_used_list($list_id){

		return update_option('autocampaigner_used_lists', implode(',', $list_id));

	}

	public function get_used_lists(){
		return explode(',', get_option('autocampaigner_used_lists'));
	}


	public function get_endpoint( $endpoint ) {

		if(str_contains($endpoint, 'listid')){
			$endpoint = str_replace('{listid}', $this->listid, $endpoint);
		}

		return parent::get_endpoint( $endpoint );
	}

}