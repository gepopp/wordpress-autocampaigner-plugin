<?php

namespace Autocampaigner\models;

use Autocampaigner\Options;use Autocampaigner\CampaignDrafts;use Autocampaigner\TemplateParser;use Autocampaigner\controller\TemplatesController;



class CampaignModel extends BaseModel {


    use Options;


    protected $draft;


    public function __construct() {

        parent::__construct();

        $this->draft = new CampaignDrafts();

    }





	/**
	 * void
	 */
	protected function render() {

        switch ($this->draft->screen){

            case 'create':
                return $this->render_header_form();
                break;
            case 'edit':
                return $this->draft_editor();
                break;
            case 'send':
                return $this->draft_preview();
                break;
            default:
                return $this->render_header_form();

        }

	}


	/**
	 * @return false|string
	 */
	protected function render_header_form() {

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

        $campaign =  $this->draft->load();

        $statusinfo = $this->controller->determine_cm_campaign_status($campaign['cm_id']);


		ob_start();
		?>
          <draft-sender
                  :campaign="<?php echo htmlentities( json_encode($campaign)) ?>"
                  :info="<?php echo htmlentities( json_encode( $statusinfo)) ?>"
          ></draft-sender>
		<?php
		return ob_get_clean();
	}


}
