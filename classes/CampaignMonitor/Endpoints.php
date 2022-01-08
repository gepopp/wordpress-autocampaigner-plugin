<?php

namespace Autocampaigner\CampaignMonitor;


class Endpoints {





	private $endpoint_base = 'https://api.createsend.com/api/v3.2/';





	protected $endpoints = [
		'api'         => [
			'test' => 'clients/{clientid}.json',
		],
		'list'        => [
			'list'    => 'clients/{clientid}/lists.json',
			'details' => 'lists/{listid}.json',
			'stats'   => 'lists/{listid}/stats.json',
		],
		'campaignapi' => [
			'sent'       => 'clients/{clientid}/campaigns.json',
			'drafts'     => 'clients/{clientid}/drafts.json',
			'scheduled'  => 'clients/{clientid}/scheduled.json',
			'send'       => 'campaigns/{campaignapiid}/send.json',
			'create'     => 'campaigns/{clientid}.json',
			'unschedule' => 'campaigns/{campaignapiid}/unschedule.json',
			'preview'    => 'campaigns/{campaignapiid}/sendpreview.json',
			'summary'    => 'campaigns/{campaignapiid}/summary.json',
			'delete'     => 'campaigns/{campaignapiid}.json',
		],


	];





	/**
	 * @return \string
	 */
	public function get_endpoints( $model, $endpoint ) {

		return $this->endpoint_base . $this->endpoints[ $model ][ $endpoint ];
	}


}