<?php

namespace Autocampaigner;

class CampaignMonitorApi {

	private $client_id;
	private $api_key;
	private $options;


	public function __construct() {

		$this->options = (array) get_option( 'autocampaigner_general_settings' );

		$this->load_keys();

		return $this;

	}


	public function test_connection() {

		$endpoint = 'https://api.createsend.com/api/v3.2/systemdate.json';

		return (bool) $this->call( $endpoint );

	}


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


		return $this->isSuccess( $result, $endpoint, $args, $type );

	}


	public function create_headers( $method = '' ) {
		return [
			'authorization' => 'Basic ' . base64_encode( $this->api_key ),
			'Content-Type'  => 'application/json',
		];
	}


	public function load_keys() {

		$client_id = '';
		$api_key   = '';

		if ( defined( 'AUTOCAMPAIGNER_CLIENT_ID' ) && defined( 'AUTOCAMPAIGNER_KEY' ) ) {
			$client_id = AUTOCAMPAIGNER_CLIENT_ID;
			$api_key   = AUTOCAMPAIGNER_KEY;
		}

		if ( empty( $client_id ) || empty( $api_key ) ) {

			$client_id = array_key_exists( 'api_client_id', $this->options ) ? $this->options['api_client_id'] : '';
			$api_key   = array_key_exists( 'api_client_secret', $this->options ) ? $this->options['api_client_secret'] : '';
		}

		$this->client_id = $client_id;
		$this->api_key   = $api_key;

	}

	public function isSuccess( $result, $endpoint, $request, $type ) {

		$status = wp_remote_retrieve_response_code( $result );
		if ( $status < 300 && $status >= 200 ) {

			return json_decode( wp_remote_retrieve_body( $result ) );

		} else {

			$report_to = array_key_exists( 'error_report_email', $this->options )
				? $this->options['error_report_email']
				: get_option( 'admin_email' );

			$answer  = wp_remote_retrieve_body( $result );
			$request = print_r( $request, true );

			$message = <<<EOM
<p>Type = $type</p>
<p>Endpoint = $endpoint</p>
<hr>
<p>$answer</p>
<hr>
<p>$request</p>
<hr>
EOM;

			wp_mail( $report_to, __( 'Autocampaigner Errror', 'autocampaigner' ), $message, [ 'Content-Type: text/html; charset=UTF-8' ] );

			return false;
		}
	}


	public function get_endpoint( $endpoint ) {

		return str_replace( '{clientid}', $this->client_id, $endpoint );
	}

}