<?php

namespace Autocampaigner\controller;

use Autocampaigner\CampaignMonitorApi;

class ListsController extends BaseController {


	protected $endpoints = [
		'list'     => 'https://api.createsend.com/api/v3.2/clients/{clientid}/lists.json',
		'details' => 'https://api.createsend.com/api/v3.2/lists/{listid}.json',
		'stats'   => 'https://api.createsend.com/api/v3.2/lists/{listid}/stats.json',
	];



	public function toggle_used_list($list_id){

		return update_option('autocampaigner_used_lists', $list_id );

	}

	public function get_used_lists(){
		$saved = get_option('autocampaigner_used_lists');
		if(!is_array($saved)) $saved = [];
		return $saved;
	}




	public function get_endpoint( $endpoint ) {

		$endpoint =  parent::get_endpoint( $endpoint );

		if(str_contains($endpoint, 'listid')){
			$endpoint = str_replace('{listid}', $this->itemId, $endpoint);
		}

		return $endpoint;
	}

}