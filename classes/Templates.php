<?php

namespace Autocampaigner;

use Autocampaigner\Model\ApiModel;
use WPMailSMTP\Providers\Sendinblue\Api;
use Autocampaigner\exceptions\CmApiCallUnsuccsessfull;



trait Templates {





	public $defaults;





	public function setup_args( $args ) {

		$this->defaults = [
			'title'       => __( 'Autocampaigner', 'autocampaigner' ),
			'has_sidebar' => false,
			'content'     => '',
		];

		return array_merge( $this->defaults, $args );

	}





	public function admin_template( $template_name, $args = [] ) {


		try{

			( new ApiModel() )->test_connection();


			$args['content'] = ( new $args['content'][0]() )->{$args['content'][1]}();


		}catch (CmApiCallUnsuccsessfull $e){

			$args['error'] = $e->getMessage();
			$template_name = 'error';

		}

		$args = $this->setup_args( $args );


		extract( $args );

		include AUTOCAMPAIGNER_DIR . "/templates/admin/{$template_name}.php";


	}


}