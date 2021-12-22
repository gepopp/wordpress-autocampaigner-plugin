<?php

namespace Autocampaigner;


use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;

class CampaignMonitorApi {

	use Options;

	/**
	 * @var string $endpoint_str the string to replace in the endpoint
	 */
	protected $endpoint_str;


	/**
	 * @var string base api url
	 */
	protected $endpoint_base = 'https://api.createsend.com/api/v3.2/';


	/**
	 * @var array holds used endpoints in controller
	 */
	protected $endpoints = [];


	/**
	 * @return bool if systemtime received
	 */
	public function test_connection() {

		$endpoint = 'https://api.createsend.com/api/v3.2/systemdate.json';
		return (bool) $this->call( $endpoint );

	}


	/**
	 * @param        $endpoint
	 * @param string $type
	 * @param array  $body
	 * @param array  $args
	 * @param array  $headers
	 *
	 * @return mixed|string|void|null
	 * @throws CmApiCallUnsuccsessfull
	 */
	public function call( $endpoint, $type = 'get', $body = [], $args = [], $headers = [] ) {

		$args = [
			'method'   => strtoupper( $type ),
			'headers'  => $this->create_headers(),
			'blocking' => true,
		];

		if ( ! empty( $body ) ) {
			$args['body'] = json_encode( $body );
		}

		$result = wp_remote_request( $endpoint, $args );


		return $this->isSuccess( $result, $type, $body, $endpoint );

	}


	/**
	 * @param string $method
	 *
	 * @return string[]
	 */
	public function create_headers( $method = '' ) {

		return [
			'authorization' => 'Basic ' . base64_encode( $this->get_api_key() ),
			'Content-Type'  => 'application/json',
		];
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

		throw new CmApiCallUnsuccsessfull( $result, $type, $body, $endpoint);
	}








	/**
	 * @param $endpoint
	 *
	 * @return string
	 */
	public function get_endpoint( $endpoint ) {

		$endpoint = $this->endpoint_base . $this->endpoints[$endpoint];

		if ( str_contains( $endpoint, $this->endpoint_str ) ) {
			$endpoint = str_replace( "{{$this->endpoint_str}}", $this->itemId, $endpoint );
		}

		return str_replace( '{clientid}', $this->get_client_id(), $endpoint );

	}

}