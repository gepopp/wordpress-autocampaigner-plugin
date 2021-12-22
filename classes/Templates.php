<?php

namespace Autocampaigner;

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

		$args = $this->setup_args( $args );

		extract( $args );

		include AUTOCAMPAIGNER_DIR . "/templates/admin/{$template_name}.php";


	}


}