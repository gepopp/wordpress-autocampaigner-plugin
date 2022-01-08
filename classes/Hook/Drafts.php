<?php

namespace Autocampaigner\Hook;

use Autocampaigner\Hooks\Hooks;
use Autocampaigner\Editor\HtmlFileCreator;
use Autocampaigner\Model\CampaignApiModel;
use Autocampaigner\Model\CampaignDraftModel;
use Autocampaigner\Controller\CampaignDraftController;



class Drafts extends BaseHooks {





	public function __construct() {


		// saves content to given draft
		add_action( 'admin_post_autocampaigner_save_draft_content', [ $this, 'create_or_update_draft' ] );
		add_action( 'wp_ajax_autocampaigner_autosave', [ $this, 'create_or_update_draft' ] );


		// creates or updates local draft
		add_action( 'admin_post_autocampaigner_create_draft', [ $this, 'create_or_update_draft' ] );


	}


	/**
	 * Creates or updates a local draft in the db
	 */
	public function create_or_update_draft() {

		$this->verify_nonce();
		(new CampaignDraftController())->update_or_create();
		
	}

}