<?php

namespace Autocampaigner\Hook;

use Autocampaigner\Model\ListModel;



class SubscriberList extends BaseHooks {





	public function __construct() {

		// loads the details from subscriber list
		add_action( 'wp_ajax_autocampainger_load_list_details', [ $this, 'autocampainger_load_list_details' ] );

		// updates the option of lists to send campaigns
		add_action( 'wp_ajax_autocampainger_update_used_lists', [ $this, 'autocampainger_update_used_lists' ] );


	}





	public function autocampainger_load_list_details() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListModel( sanitize_text_field( $_POST['list_id'] ) ) )->stats() ) );

	}





	public function autocampainger_update_used_lists() {

		$this->verify_nonce();
		wp_die( json_encode( ( new ListModel() )->toggle_used_list( $_POST['lists'] ) ) );


	}


}