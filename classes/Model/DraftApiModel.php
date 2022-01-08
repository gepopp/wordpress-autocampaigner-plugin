<?php

namespace Autocampaigner\Model;

use Autocampaigner\CampaignMonitor\CampaignMonitor;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class DraftApiModel extends BaseModel {





	public $name;





	public $subject;





	public $from_name;





	public $from_email;





	public $reply_email;





	public $preview_url;





	public $created_at;





	public $driver;





	public function __construct( $id = null ) {

		parent::__construct( $id );


		$this->driver = new CampaignMonitor();

	}





	/**
	 * loads all drafts from api anf filters them by given id
	 *
	 * @param $draft_id
	 *
	 * @return mixed|null
	 */
	public function load( $draft_id ) {

		//load all drafts from api
		$drafts = $this->drafts();

		$filtered = array_filter( $drafts, function ( $draft ) use ( $draft_id ) {

			if ( $draft_id == $draft->CampaignID ) {
				return $draft;
			}
		} );

		if ( ! count( $filtered ) ) {
			return false;
		}

		$draft = array_shift( $filtered );
		$this->driver->translate_draft( $this, $draft );

		return $this;

	}





	public function create_draft( CampaignDraftModel $draft ) {


		if ( ! empty( $draft->cm_id ) ) {
			try {

				$this->id = $draft->cm_id;
				$this->delete( 'delete' );
				$draft->cm_id = null;
				$draft->update_field( 'cm_id' );

			} catch ( CmApiCallUnsuccsessfull $e ) {

			}
		}

		$data = [
			'Name'      => $draft->header_data['campaign_name'],
			'Subject'   => $draft->header_data['subject'],
			'FromName'  => $draft->header_data['from_name'],
			'FromEmail' => $draft->header_data['from_email'],
			'ReplyTo'   => $draft->header_data['reply_email'],
			'HtmlUrl'   => AUTOCAMPAIGNER_URL . 'draft_files/' . trim( $draft->html_url ),
			'ListIDs'   => get_option( 'autocampaigner_used_lists' ),
		];

		$campaign_id = $this->create( 'post', $data );

		$draft->cm_id = $campaign_id;
		$draft->update_field( 'cm_id' );

		return $campaign_id;

	}


}