<?php

namespace Autocampaigner\exceptions;

use Autocampaigner\Options;



class CmApiCallUnsuccsessfull extends \Exception {





	use Options;



	public function __construct( $result, $type, $body, $endpoint ) {

		$remote_body = json_decode( wp_remote_retrieve_body( $result ) );

		parent::__construct( wp_remote_retrieve_body( $result ), $remote_body->Code, null );

		$this->send_error_mail( $result, $type, $remote_body, $endpoint, $body );


	}





	/**
	 * @return mixed
	 */
	public function get_message() {

		return $this->message;
	}





	public function send_error_mail( $result, $type, $body, $endpoint, $sent ) {

		$subject = __( 'Autocampaigner Errror', 'autocampaigner' ) . ' - ' . $this->get_message();

		$body   = print_r( $body, true );
		$result = print_r( $result, true );
		$sent   = print_r( $sent, true );

		$line    = $this->getLine();
		$message = $this->getMessage();
		$file    = $this->getFile();

		$callstack = $this->getTraceAsString();

		$message = <<<EOM

<p>$message()</p>
<p>$file :: $line</p>
<p><strong>Endpoint: </strong>$endpoint</p>
<p><strong>Type: </strong>$type</p>
<pre>
	<code>
		$callstack
	</code>
</pre>

<h3>Answer:</h3>
<pre>
	<code>
		$body
	</code>
</pre>
<hr>
<pre>
	<code>
		$result
	</code>
</pre>
<pre>
	<code>
		$sent
	</code>
</pre>
EOM;

		wp_mail( $this->get_error_email(), $subject, $message, [ 'Content-Type: text/html; charset=UTF-8' ] );


	}


}