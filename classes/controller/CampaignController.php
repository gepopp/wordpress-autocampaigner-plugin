<?php

namespace Autocampaigner\controller;

use Autocampaigner\Options;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;

class CampaignController extends BaseController {


	use Options;


	/**
	 * @var string to replace in endpoint
	 */
	protected $endpoint_str = 'campaignid';


	/**
	 * @var string[] used endpoints
	 */
	protected $endpoints = [
		'create'    => 'campaigns/{clientid}/fromtemplate.json',
		'send'      => 'campaigns/{campaignid}/send.json',
		'sent'      => 'clients/{clientid}/campaigns.json',
		'scheduled' => 'clients/{clientid}/scheduled.json',
		'drafts'    => 'clients/{clientid}/drafts.json',
	];





	/**
	 * sends campaign immediatly or schedules it on given datetime
	 *
	 * @param false $date_time
	 *
	 * @return mixed|string|void|null
	 * @throws \Autocampaigner\exceptions\CmApiCallUnsuccsessfull
	 */
	public function send( $date_time = false ) {

		if ( ! $date_time ) {
			$date_time = 'Immediately';
		}


		return $this->call(
			$this->get_endpoint( 'send' ),
			'post',
			[ 'ConfirmationEmail' => $this->get_confirm_email(), 'SendDate' => $date_time ]
		);
	}





	/**
	 * creates draft on cm
	 *
	 * @param $content
	 *
	 * @return mixed|string|void|null
	 * @throws \Autocampaigner\exceptions\CmApiCallUnsuccsessfull
	 *
	 */
	public function create( $content ) {

		return $this->call( $this->get_endpoint( 'create' ), 'post', $content );

	}





	/**
	 * return array of campaign names on cm to avoid duplicate error
	 *
	 *
	 * @return array
	 */
	public function campaign_names() {

		try {
			return array_map( function ( $campaign ) {

				if ( isset( $campaign->Name ) ) {
					return $campaign->Name;
				}
			}, array_merge( (array) $this->drafts(), (array) $this->scheduled(), (array) $this->sent() ) );

		} catch ( CmApiCallUnsuccsessfull $e ) {
			return [];
		}

	}





	/**
	 * prepopulates create form
	 *
	 * @return array
	 */
	public function default_values() {

		return [
			'from_name'   => $this->get_from_name(),
			'from_email'  => $this->get_from_email(),
			'reply_email' => $this->get_reply_email(),
		];

	}

}