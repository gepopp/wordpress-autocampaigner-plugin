<?php

namespace Autocampaigner\Model;


class ListModel  extends BaseModel {







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