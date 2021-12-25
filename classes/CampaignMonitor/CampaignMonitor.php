<?php

namespace Autocampaigner\CampaignMonitor;


use Autocampaigner\Options;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;

class CampaignMonitor {

	use Options, Endpoints;



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
	public function call( $endpoint, $type = 'get', $body = [] ) {

		$args = [
			'method'   => strtoupper( $type ),
			'headers'  => $this->create_headers(),
			'blocking' => true,
			'timeout'  => 20
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





}