<?php

namespace Autocampaigner\CampaignMonitor;


use Autocampaigner\Options;



trait Endpoints {





	use Options;



	private $endpoint_base = 'https://api.createsend.com/api/v3.2/';





	protected $endpoints = [

		'list'        => [
			'list'    => 'clients/{clientid}/lists.json',
			'details' => 'lists/{listid}.json',
			'stats'   => 'lists/{listid}/stats.json',
		],
		'template'    => [
			'list'    => 'clients/{clientid}/templates.json',
			'create'  => 'templates/{clientid}.json',
			'update'  => 'templates/{templateid}.json',
			'details' => 'templates/{templateid}.json',
		],
		'campaignapi' => [
			'sent'       => 'clients/{clientid}/campaigns.json',
			'drafts'     => 'clients/{clientid}/drafts.json',
			'scheduled'  => 'clients/{clientid}/scheduled.json',
			'send'       => 'campaigns/{campaignapiid}/send.json',
			'create'     => 'campaigns/{clientid}/fromtemplate.json',
			'unschedule' => 'campaigns/{campaignapiid}/unschedule.json',
			'preview'    => 'campaigns/{campaignapiid}/sendpreview.json',
			'summary'    => 'campaigns/{campaignapiid}/summary.json',
			'delete'     => 'campaigns/{campaignapiid}.json',
		],


	];





	/**
	 * @param $endpoint
	 *
	 * @return string
	 */
	public function get_endpoint( $model, $endpoint, $api_endpint_id ) {

		$model = str_replace( 'model', '', $model );

		$endpoint = $this->endpoint_base . $this->endpoints[ $model ][ $endpoint ];

		$model .= 'id';

		if ( str_contains( $endpoint, $model ) ) {
			$endpoint = str_replace( "{{$model}}", $api_endpint_id, $endpoint );
		}

		return str_replace( '{clientid}', $this->get_client_id(), $endpoint );

	}
}