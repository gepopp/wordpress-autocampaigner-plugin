<?php

namespace Autocampaigner\Model;

use Carbon\Carbon;
use Autocampaigner\Template\Description;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class CampaignApiModel extends BaseModel {





	public function create_draft( $draft_id ) {


		$draft       = new CampaignDraftModel( $draft_id );
		$description = new Description( $draft->template );

		$campaign_id = $this->create( 'post', [
			'Name'            => $draft->header_data['campaign_name'],
			'Subject'         => $draft->header_data['subject'],
			'FromName'        => $draft->header_data['from_name'],
			'FromEmail'       => $draft->header_data['from_email'],
			'ReplyTo'         => $draft->header_data['reply_email'],
			'ListIDs'         => get_option( 'autocampaigner_used_lists' ),
			'TemplateID'      => $description['TemplateID'],
			'TemplateContent' => $draft->content,
		] );

		global $wpdb;
		$wpdb->update( $draft->get_table_name(), [
			'cm_id'  => $campaign_id,
			'status' => 'created',
		], [ 'id' => $draft->id ] );

		return $campaign_id;

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
	public function schedule_campaign( $date_time = false ) {

		if ( ! $date_time || empty( $date_time ) ) {
			$date_time = 'Immediately';
		} else {
			$date_time = Carbon::parse( $date_time )->format( 'Y-m-d H:i' );
		}


		return $this->send( 'post', [ 'ConfirmationEmail' => $this->get_confirm_email(), 'SendDate' => $date_time ] );
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