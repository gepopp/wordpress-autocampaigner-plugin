<?php

namespace Autocampaigner\Model;

use Autocampaigner\Options;
use Autocampaigner\CampaignMonitor\CampaignMonitor;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



class ApiModel extends BaseModel {





	use Options;



	protected CampaignMonitor $driver;





	public function __construct( $id = null ) {

		$this->driver = $this->load_driver();

		parent::__construct( $id );
	}



	/**
	 * @return bool if systemtime received
	 */
	public function test_connection() {

		$this->call($this->get_endpoint('test'));

	}




	/**
	 * try to make an api call if the called function is in the drivers endpint array
	 *
	 *
	 * @param string $name
	 * @param array  $arguments
	 *
	 * @return mixed
	 */
	public function __call( string $name, array $arguments ) {

		return $this->call( $this->get_endpoint( $name ), $arguments[0] ?? 'get', $arguments[1] ?? [] );
	}





	/**
	 * @param $endpoint
	 *
	 * @return string
	 */
	public function get_endpoint( $endpoint ) {

		$model_name = $this->endpoint_model_name();


		$endpoint = $this->driver->endpoints->get_endpoints( $model_name, $endpoint );


		if ( str_contains( $endpoint, $model_name . 'id' ) ) {
			$endpoint = str_replace( '{' . $model_name . 'id}', $this->id, $endpoint );
		}

		return str_replace( '{clientid}', $this->get_client_id(), $endpoint );

	}





	public function endpoint_model_name() {

		$model_name = get_class( $this );
		$model_name = explode( '\\', $model_name );
		$model_name = array_pop( $model_name );
		$model_name = strtolower( $model_name );
		$model_name = str_replace( 'model', '', $model_name );

		return $model_name;

	}





	/**
	 * @param        $endpoint
	 * @param string $method
	 * @param array  $body
	 * @param array  $args
	 * @param array  $headers
	 *
	 * @return mixed|string|void|null
	 * @throws CmApiCallUnsuccsessfull
	 */
	public function call( $endpoint, $method = 'get', $body = [] ) {


		$args = [
			'method'   => strtoupper( $method ),
			'headers'  => $this->driver->create_headers(),
			'blocking' => true,
			'timeout'  => 20,
		];

		if ( ! empty( $body ) ) {

			$args['body'] = json_encode( $body );
		}

		$result = wp_remote_request( $endpoint, $args );

		return $this->isSuccess( $result, $method, $body, $endpoint );

	}





	/**
	 * @param $result
	 * @param $type
	 * @param $body
	 *
	 * @return mixed|string|void|null
	 * @throws CmApiCallUnsuccsessfull
	 */
	public function isSuccess( $result, $type, $body, $endpoint ) {

		$status = wp_remote_retrieve_response_code( $result );

		if ( $status < 300 && $status >= 200 ) {

			$body = json_decode( wp_remote_retrieve_body( $result ) );
			if ( empty( $body ) ) {
				$body = __( 'Request successfull', 'autocampaigner' );
			}

			return $body;
		}

		throw new CmApiCallUnsuccsessfull( $result, $type, $body, $endpoint );
	}





	/**
	 * loads the api driver class, now only one but prepared vor more
	 *
	 *
	 * @return mixed
	 */
	protected function load_driver() {

		$driver_name = 'Autocampaigner\\' . $this->get_driver() . '\\' . $this->get_driver();
		$driver      = new $driver_name;

		return $driver;
	}


}