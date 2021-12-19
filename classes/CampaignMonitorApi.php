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

		$content['headers'] = $this->auth_header( $headers );

		if($type == 'PUT'){
			$content['headers']['method'] = 'PUT';
			$type = 'post';
		}

		if($type == 'DELETE'){
			$content['headers']['method'] = 'DELETE';
			$type = 'post';
		}

		if ( ! empty( $body ) ) {
			$content['body'] = json_encode( $body );
		}

		$funciton = 'wp_remote_' . strtolower( $type );

		$result = $funciton( $this->get_endpoint($endpoint), $content );

		wp_die(wp_remote_retrieve_body($result));

		return $this->isSuccess( $result );


	}


	public function auth_header( $headers = [] ) {
		return $headers[] = [
			'authorization' => 'Basic ' . base64_encode( $this->api_key ),
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

			$client_id = array_key_exists('api_client_id', $this->options)? $this->options['api_client_id'] :'';
			$api_key   = array_key_exists('api_client_secret', $this->options)? $this->options['api_client_secret'] :'';
		}

		$this->client_id = $client_id;
		$this->api_key   = $api_key;

	}

	public function isSuccess( $result ) {

		$status = wp_remote_retrieve_response_code( $result );
		if ( $status < 300 && $status > 199 ) {

			return json_decode( wp_remote_retrieve_body( $result ) );

		} else {

			$report_to = array_key_exists('error_report_email', $this->options)
				? $this->options['error_report_email']
				: get_option('admin_email');

			wp_mail( $report_to, __( 'Autocampaigner Errror', 'autocampaigner' ), print_r( wp_remote_retrieve_body( $result ), true ) );

			return false;
		}
	}

	public function get_endpoint($endpoint){
		return str_replace('{clientid}', $this->client_id, $endpoint);
	}

}