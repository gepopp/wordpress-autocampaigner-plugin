<?php

namespace Autocampaigner\CampaignMonitor;


use Autocampaigner\Options;
use Autocampaigner\Model\ListModel;
use Autocampaigner\Model\DraftApiModel;



class CampaignMonitor {





	use Options;


	public $endpoints;



	public function __construct() {

		$this->endpoints = new Endpoints();

	}





	/**
	 * @return bool if systemtime received
	 */
	public function test_connection() {

		return 'https://api.createsend.com/api/v3.2/systemdate.json';

	}



	/**
	 * @param string $method
	 *
	 * @return string[]
	 */
	public function create_headers( $method = '' ) {

		return [
			'authorization' => 'Basic ' . base64_encode( $this->get_api_key() ),
			'Content-Type'  => 'application/json',
		];
	}





	public function translate_draft( DraftApiModel $model, object $data ) {


		$model->id          = $data->CampaignID;
		$model->name        = $data->Name;
		$model->subject     = $data->Subject;
		$model->from_name   = $data->FromName;
		$model->from_email  = $data->FromEmail;
		$model->reply_email = $data->ReplyTo;
		$model->preview_url = $data->PreviewUrl;


	}





	public function translate_list( ListModel $model, object $data ) {

		$model->id   = $data->ListID;
		$model->name = $data->Name;


	}


}