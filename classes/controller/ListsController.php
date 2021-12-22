<?php

namespace Autocampaigner\controller;

class ListsController extends BaseController {




	/**
	 * @var string $endpoint_str to replace in endpoint with $item_id
	 */
	protected $endpoint_str = 'listid';





	/**
	 * @var string[] used api endpoints
	 */
	protected $endpoints = [
		'list'    => 'clients/{clientid}/lists.json',
		'details' => 'lists/{listid}.json',
		'stats'   => 'lists/{listid}/stats.json',
	];





	/**
	 * @param array $list_id
	 *
	 * @return bool if option was updated
	 */
	public function toggle_used_list( $list_ids ) {

		return update_option( 'autocampaigner_used_lists', $list_ids );

	}





	/**
	 * @return array of list ids
	 */
	public function get_used_lists() {

		$saved = get_option( 'autocampaigner_used_lists' );
		if ( ! is_array( $saved ) ) {
			$saved = [];
		}

		return $saved;
	}



}