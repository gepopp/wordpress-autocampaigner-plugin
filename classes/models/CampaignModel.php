<?php

namespace Autocampaigner\models;

use Autocampaigner\CampaignDrafts;
use Autocampaigner\TemplateParser;
use Autocampaigner\controller\CampaignController;
use Autocampaigner\controller\TemplatesController;

class CampaignModel extends BaseModel {


	public function __construct() {

		$this->contoller = new CampaignController();

	}

	protected function render() {

		if ( isset( $_GET['draft'] ) ) {

			$drafts = new CampaignDrafts();
			$draft  = $drafts->load( sanitize_text_field( $_GET['draft'] ) );

			$parser = new TemplateParser( $draft['template'] );
			$html   = $parser->replace_multinines();

            $settings = get_option('autocampaigner_general_settings');

			ob_start();
			?>
            <editor
                    :draft="<?php echo sanitize_text_field( $_GET['draft'] ) ?>"
                    adminurl="<?php echo admin_url( 'admin-post.php' ) ?>"
                    nonce="<?php echo wp_create_nonce( 'create_campaign' ) ?>"
                    confirm_email_setting="<?php echo $settings['confirm_email'] ?>"
            ><?php echo $html ?></editor>
			<?php
			return ob_get_clean();
		}


		$templates = new TemplatesController();
		ob_start();
		?>
        <create-campaign
                :defaults="<?php $this->contoller->as_html_attribute( 'default_values' ) ?>"
                :templates="<?php $templates->as_html_attribute( 'get_templates_with_description' ) ?>"
                adminurl="<?php echo admin_url( 'admin-post.php' ) ?>"
                nonce="<?php echo wp_create_nonce( 'create_campaign' ) ?>"
        ></create-campaign>
		<?php
		return ob_get_clean();
	}
}
