<?php

namespace Autocampaigner\controller;

use function Clue\StreamFilter\fun;

class CampaignController extends BaseController {


	protected $endpoints = [
		'create'    => 'https://api.createsend.com/api/v3.2/campaigns/{clientid}/fromtemplate.json',
		'send'      => 'https://api.createsend.com/api/v3.2/campaigns/{campaignid}/send.json',
		'sent'      => 'https://api.createsend.com/api/v3.2/clients/{clientid}/campaigns.json',
		'scheduled' => 'https://api.createsend.com/api/v3.2/clients/{clientid}/scheduled.json',
		'drafts'    => 'https://api.createsend.com/api/v3.2/clients/{clientid}/drafts.json',
	];


	public function send($date_time = false){


		$settings = get_option('autocampaigner_general_settings');

		$confirm_email = isset($settings['confirm_email']) ? $settings['confirm_email'] : get_option('admin_email');

		if(!$date_time){
			$date_time = 'Immediately';
		}

		$endpoint = $this->get_endpoint($this->endpoints['send']);


		return $this->call($this->get_endpoint($this->endpoints['send']), 'post', ['ConfirmationEmail' => $confirm_email, 'SendDate' => $date_time]);
	}


	public function create( $content ) {

		return $this->call( $this->get_endpoint( $this->endpoints['create'] ), 'post', $content );

	}

	public function campaign_names(){


		$drafts = $this->drafts();
		$scheduled = $this->scheduled();
		$sent = $this->sent();

		$all = array_merge($drafts, $scheduled, $sent);

		return array_map( function ($campaign){
			return $campaign->Name;
		}, $all);
	}


	public function default_values() {

		$sender = (array) get_option( 'autocampaigner_general_settings' );


		if ( ! array_key_exists( 'from_email', $sender ) ) {
			$sender['from_email'] = get_option( 'admin_email' );
		}

		if ( ! array_key_exists( 'reply_email', $sender ) ) {
			$sender['reply_email'] = get_option( 'admin_email' );
		}

		$fields = [ 'from_email', 'from_name', 'reply_email' ];

		foreach ( $sender as $key => $item ) {
			if ( ! in_array( $key, $fields ) ) {
				unset( $sender[ $key ] );
			}
		}

		return $sender;

	}


	public function get_endpoint( $endpoint ) {

		$endpoint = parent::get_endpoint( $endpoint );

		if ( str_contains( $endpoint, 'campaignid' ) ) {
			$endpoint = str_replace( '{campaignid}', $this->itemId, $endpoint );
		}

		return $endpoint;
	}

}