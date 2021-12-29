<?php

namespace Autocampaigner\Model;

use Carbon\Carbon;
use Autocampaigner\Options;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class CampaignApiModel extends ApiModel {





	use Options;



	const DRAFT_STATUS_NEW = 'new';
	const DRAFT_STATUS_DRAFT = 'draft';
	const DRAFT_STATUS_SCHEDULED = 'scheduled';
	const DRAFT_STATUS_SENT = 'sent';
	const DRAFT_STATUS_DELETED = 'deleted';





	public $status;





	public $info;





	public function load() {


		$stati = [ 'drafts', 'scheduled', 'sent' ];

		foreach ( $stati as $status ) {


			$all = $this->{$status}();

			if ( is_array( $all ) ) {

				$filtered = array_filter( $all, function ( $campaign ) {

					if ( $campaign->CampaignID == $this->id ) {
						return $campaign;
					}
				} );

				if ( ! empty( $filtered ) ) {

					$this->status = $status;
					$this->info   = array_shift( $filtered );

				}
			}
		}

		if ( empty( $this->status ) ) {
			$this->status = 'deleted';
		}

	}


	public function delete() {

		try{
			$this->call( $this->get_endpoint( 'delete' ), 'delete' );
		}catch (CmApiCallUnsuccsessfull $e){}

	}



	/**
	 * sends campaign immediatly or schedules it on given datetime
	 *
	 * @param false $date_time
	 *
	 * @return mixed|string|void|null
	 * @throws \Autocampaigner\exceptions\CmApiCallUnsuccsessfull
	 */
	public function schedule_campaign( CampaignDraftModel $draft, $date_time = false ) {

		if ( ! $date_time || empty( $date_time ) ) {
			$date_time = 'Immediately';
		} else {
			$date_time = Carbon::parse( $date_time )->format( 'Y-m-d H:i' );
		}

		$this->send( 'post', [ 'ConfirmationEmail' => $this->get_confirm_email(), 'SendDate' => $date_time ] );

		if($date_time == 'Immediately'){
			$draft->status = 'sent';
		}else{
			$draft->status = 'scheduled';
		}

		$draft->scheduled = $date_time;
		$draft->update();

		return $draft;
	}


	public function unschedule_campaign(CampaignDraftModel $draft ){

		$this->call($this->get_endpoint('unschedule'), 'post');
		$draft->status = 'created';
		$draft->update();

		return $draft;

	}




	public function create_draft( CampaignDraftModel $draft ) {


		$campaign_id = $this->create( 'post', [
			'Name'      => $draft->header_data['campaign_name'],
			'Subject'   => $draft->header_data['subject'],
			'FromName'  => $draft->header_data['from_name'],
			'FromEmail' => $draft->header_data['from_email'],
			'ReplyTo'   => $draft->header_data['reply_email'],
			'ListIDs'   => get_option( 'autocampaigner_used_lists' ),
			'HtmlUrl'   => AUTOCAMPAIGNER_URL . 'draft_files/' . $draft->html_url,
		] );


		$draft->status = 'created';
		$draft->cm_id  = $campaign_id;
		$draft->update();

		return $draft;

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


}