<?php

namespace Autocampaigner\models;

use Autocampaigner\Options;
use Autocampaigner\CampaignDrafts;
use Autocampaigner\TemplateParser;
use Autocampaigner\controller\CampaignController;
use Autocampaigner\controller\TemplatesController;



class CampaignModel extends BaseModel {


    use Options;



	/**
	 * @return false|string
	 */
	protected function render() {

		if ( isset( $_GET['preview'] ) ) {
			return $this->draft_preview();
		}

		if ( isset( $_GET['draft'] ) ) {

			return $this->draft_editor();
		}


		return $this->start_render();
	}





	/**
	 * @return false|string
	 */
	protected function draft_editor() {

		$drafts = new CampaignDrafts();
		$draft  = $drafts->load( sanitize_text_field( $_GET['draft'] ) );

		$parser = new TemplateParser( $draft['template'] );
		$html   = $parser->parse_template_html_for_editor();

		ob_start();
		?>
        <editor
                :draft="<?php echo sanitize_text_field( $_GET['draft'] ) ?>"
                adminurl="<?php echo admin_url( 'admin-post.php' ) ?>"
                nonce="<?php echo wp_create_nonce( 'create_campaign' ) ?>"
                confirm_email_setting="<?php echo $this->get_confirm_email() ?>"
        ><?php echo $html ?></editor>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function draft_preview() {

		$campaign_controller = new CampaignController();
		$drafts              = $campaign_controller->drafts();


		$draft = array_filter( $drafts, function ( $draft ) {
			if ( $draft->CampaignID == $_GET['preview'] ) {
				return $draft;
			}
		} );

		$draft = array_shift( $draft );
		$draft_url = str_replace( 'http', 'https', $draft->PreviewURL );

		ob_start();
		?>
        <div class="ac-my-48 ac-flex ac-justify-center ac-items-center">
            <div class="ac-p-10 ac-border ac-border-plugin">
                <h3 class="ac-text-3xl ac-mb-10">Vorschau</h3>
                <div>
                    <iframe src="<?php echo $draft_url ?>" width="800" height="800"></iframe>
                </div>

                <form action="<?php echo admin_url( 'admin-post.php' ) ?>" method="post" class="ac-mt-10 ac-pt-10 ac-border-t ac-border-plugin">
					<?php wp_nonce_field( 'send_campaign' ) ?>
                    <input type="hidden" name="action" value="autocampaigner_sent_campaign">
                    <input type="hidden" name="nonce" value="sent_campaign">
                    <input type="hidden" name="draft" value="<?php echo $draft->CampaignID ?>">
                    <div class="ac-mb-4 ac-grid ac-grid-cols-2">
                        <div>
                            <label class="ac-label">Versand Termin</label>
                            <input class="ac-admin-input" type="datetime-local" name="cmpaign_schedule">
                            <p class="ac-text-red-900 ac-text-xs">Leer lassen zum sofort senden.</p>
                        </div>
                        <div class="ac-flex ac-items-center">
                            <button type="submit" class="ac-button">senden</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function start_render() {

		$templates = new TemplatesController();

		ob_start();
		?>
        <create-campaign
                :defaults="<?php $this->controller->as_html_attribute( 'default_values' ) ?>"
                :templates="<?php $templates->as_html_attribute( 'get_templates_with_description' ) ?>"
                adminurl="<?php echo admin_url( 'admin-post.php' ) ?>"
                nonce="<?php echo wp_create_nonce( 'create_campaign' ) ?>"
                :campaignnames="<?php $this->controller->as_html_attribute( 'campaign_names' ) ?>"
        ></create-campaign>
		<?php
		return ob_get_clean();
	}
}
