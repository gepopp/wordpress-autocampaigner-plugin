<?php

namespace Autocampaigner\Model;

class DbModel extends BaseModel {



	public function load() {

		global $wpdb;
		$data = (array) $wpdb->get_row( sprintf( 'SELECT * FROM %s WHERE id = %d LIMIT 1', $this->get_table_name(), $this->id
		), ARRAY_A );

		foreach ( $data as $key => $value ) {
			if ( property_exists( $this, $key ) ) {
				$this->$key = maybe_unserialize($value);
			}
		}
	}


	public function all(){

		global $wpdb;
		$ids = $wpdb->get_col(sprintf('SELECT id FROM %s ORDER BY created_at DESC', $this->get_table_name()));


		$all =[];
		$classname = get_class($this);

		foreach ( $ids as $id ) {
			$all[] = ( new $classname($id) );
		}

		return $all;

	}


}