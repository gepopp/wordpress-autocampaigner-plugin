<?php

namespace Autocampaigner\controller;

class CampaignController extends BaseController {


	protected $endpoints = [
		'create' => 'https://api.createsend.com/api/v3.2/campaigns/{clientid}/fromtemplate.json'
	];


	public function default_values(){

		$sender = (array) get_option( 'autocampaigner_general_settings' );


		if(!array_key_exists('from_email', $sender)){
			$sender['from_email'] = get_option('admin_email');
		}

		if(!array_key_exists('reply_email', $sender)){
			$sender['reply_email'] = get_option('admin_email');
		}

		$fields = ['from_email', 'from_name', 'reply_email'];

		foreach ( $sender as $key => $item ) {
			if(!in_array($key, $fields)){
				unset($sender[$key]);
			}
		}

		return $sender;

	}


	public function parse_template($name){



	}

}