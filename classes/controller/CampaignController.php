<?php

namespace Autocampaigner\controller;

use Carbon\Carbon;
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
		'create'     => 'campaigns/{clientid}/fromtemplate.json',
		'send'       => 'campaigns/{campaignid}/send.json',
		'sent'       => 'clients/{clientid}/campaigns.json',
		'scheduled'  => 'clients/{clientid}/scheduled.json',
		'unschedule' => 'campaigns/{campaignid}/unschedule.json',
		'drafts'     => 'clients/{clientid}/drafts.json',
		'preview'    => 'campaigns/{campaignid}/sendpreview.json',
		'summary'    => 'campaigns/{campaignid}/summary.json',
	];


	public function unschedule(){
		return $this->call($this->get_endpoint('unschedule'), 'post');
	}



	public function determine_cm_campaign_status( $campaign_id ) {


		$stati = [ 'drafts', 'scheduled', 'sent' ];

		foreach ( $stati as $status ) {

			$all = $this->{$status}();

			if ( is_array( $all ) ) {

				$filtered = array_filter( $all, function ( $campaign ) use ( $campaign_id ) {

					if ( $campaign->CampaignID == $campaign_id ) {
						return $campaign;
					}
				} );

				if ( ! empty( $filtered ) ) {

					return [
						'status' => $status,
						'info'   => array_shift( $filtered ),
					];

				}
			}
		}

		return [
			'status' => 'new',
			'info'   => [],
		];
	}





	/**
	 * sends campaign immediatly or schedules it on given datetime
	 *
	 * @param false $date_time
	 *
	 * @return mixed|string|void|null
	 * @throws \Autocampaigner\exceptions\CmApiCallUnsuccsessfull
	 */
	public function send( $date_time = false ) {

		if ( ! $date_time || empty( $date_time ) ) {
			$date_time = 'Immediately';
		} else {
			$date_time = Carbon::parse( $date_time )->format( 'Y-m-d H:i' );
		}


		return $this->call(
			$this->get_endpoint( 'send' ),
			'post',
			[ 'ConfirmationEmail' => $this->get_confirm_email(), 'SendDate' => $date_time ]
		);
	}





	public function preview( $recipients ) {

		return $this->call( $this->get_endpoint( 'preview' ), 'post', [ 'PreviewRecipients' => explode( ',', $recipients ) ] );

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
			'from_name'     => $this->get_from_name(),
			'from_email'    => $this->get_from_email(),
			'reply_email'   => $this->get_reply_email(),
			'confirm_email' => $this->get_confirm_email(),
		];

	}





	public function get_cm_draft_details( $draft_id ) {

		$drafts = $this->drafts();

		$filtered = array_filter( $drafts, function ( $draft ) use ( $draft_id ) {

			if ( $draft_id == $draft->CampaignID ) {
				return $draft;
			}
		} );

		return array_shift( $filtered );
	}

}
