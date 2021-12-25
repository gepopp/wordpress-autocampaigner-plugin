<?php

namespace Autocampaigner;

use Carbon\Carbon;
use Autocampaigner\controller\CampaignDraftController;
use Autocampaigner\controller\TemplatesController;



class CampaignDrafts {





	public function __construct() {

		$this->cm_draft_id = ! isset( $_GET['cmid'] ) || empty( sanitize_text_field( $_GET['cmid'] ) ) ? '' : sanitize_text_field( $_GET['cmid'] );
		$this->screen      = ! isset( $_GET['screen'] ) || empty( sanitize_text_field( $_GET['screen'] ) ) ? 'create' : sanitize_text_field( $_GET['screen'] );

	}





	public function route( $url = false ) {

		$url = $url ?: $_SERVER['HTTP_REFERER'];


		return add_query_arg( [
			'draft'  => $this->draft_id,
			'cmid'   => $this->cm_draft_id,
			'screen' => $this->screen,
		], $url );

	}

























}