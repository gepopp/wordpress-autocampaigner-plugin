<?php

namespace Autocampaigner\Model;


class ListModel extends ApiModel implements ModelInterface {





	public $name;





	public bool $in_use;





	public function load() {


		$filtered = array_filter( $this->list(), function ( $list ) {

			if ( $this->id == $list->ListID ) {
				return $list;
			}
		} );

		if ( ! count( $filtered ) ) {
			return false;
		}

		$list = array_shift( $filtered );
		$this->driver->translate_list( $this, $list );

		$this->in_use = in_array( $this->id, $this->get_used_lists() );

		return $this;


	}





	public function all() {

		$all = [];
		foreach ( $this->list() as $list ) {
			$all[] = new ListModel( $list->ListID );
		}

		return $all;

	}





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





	public function create() {
		// TODO: Implement create() method.
	}





	public function update() {
		// TODO: Implement update() method.
	}





	public function delete() {
		// TODO: Implement delete() method.
	}


}