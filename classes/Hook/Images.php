<?php

namespace Autocampaigner\Hook;


use Autocampaigner\Model\TemplateModel;



class Images extends BaseHooks {





	public function __construct() {

// ads image sizes from tempalte descriptions
		add_action( 'after_setup_theme', [ $this, 'update_images_sizes' ] );
	}





	public function update_images_sizes() {

		add_theme_support( 'post-thumbnails' );


		$template_model = new TemplateModel();

		$templates = $template_model->all();


		foreach ( $templates as $template ) {

			/**
			 * @var $template TemplateModel
			 */
			if ( isset( $template->images ) ) {

				foreach ( $template->images as $image ) {
					add_image_size(
						$image->Name,
						$image->width,
					);
				}
			}
		}
	}

}