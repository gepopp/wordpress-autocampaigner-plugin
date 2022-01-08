<?php

namespace Autocampaigner\Controller;

use Carbon\Carbon;
use Autocampaigner\Options;
use Autocampaigner\Model\TemplateModel;
use Autocampaigner\Editor\TemplateParser;
use Autocampaigner\Model\CampaignApiModel;
use Autocampaigner\Model\CampaignDraftModel;



class CampaignDraftController extends BaseController {





	use Options;



	public $campaigndraftmodel;






	public function update_or_create() {


         if( $this->campaigndraftmodel->id ){
             $this->campaigndraftmodel->validate()->update();
         }else{
             $this->campaigndraftmodel->validate()->create();
         }

		if ( ! wp_doing_ajax() ) {

			$action = sanitize_text_field( $_POST['action'] );

			wp_safe_redirect(
				add_query_arg( [
					'draft'  => $this->campaigndraftmodel->id,
					'screen' => $action == 'autocampaigner_save_draft_content' ? 'send' : 'edit',
				],
					$_SERVER['HTTP_REFERER'] ) );
			exit();
		}

	}





	/**
	 * void
	 */
	public function render() {

		$screen = '';
		if ( isset( $_GET['screen'] ) && ! empty( sanitize_text_field( $_GET['screen'] ) ) ) {
			$screen = sanitize_text_field( $_GET['screen'] );
		}


		switch ( $screen ) {

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

        $this->campaigndraftmodel->delete_on_api();

		ob_start();
		?>
        <create-campaign
                :draft-id="<?php echo (int) $this->campaigndraftmodel->id ?>"
                :defaults="<?php $this->as_html_attribute( $this->campaigndraftmodel->default_values() ) ?>"
                :templates="<?php $this->as_html_attribute( ( new TemplateModel() )->all() ); ?>"
                template="<?php echo $this->campaigndraftmodel->template ?>"
                :campaignnames="<?php $this->as_html_attribute( ( new CampaignApiModel() )->campaign_names() ) ?>"
        ></create-campaign>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function draft_editor() {

		$this->campaigndraftmodel->delete_on_api();



		$parser = new TemplateParser( $this->campaigndraftmodel );
		$html   = $parser->parse_template_html_for_editor();

		$restart_link = $this->get_link('create');

		ob_start();
		?>
        <editor
                :draft="<?php echo $this->campaigndraftmodel->id ?>"
                back="<?php echo $restart_link ?>"
        ><?php echo $html ?></editor>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function draft_preview() {


		$edit_link = $this->get_link('edit');


        $preview_url = AUTOCAMPAIGNER_URL . 'draft_files/' . $this->campaigndraftmodel->html_url;


		ob_start();
		?>
        <draft-sender
                :campaign-id="<?php echo $this->campaigndraftmodel->id ?>"
                :campaign="<?php echo htmlentities( json_encode( $this->campaigndraftmodel ) ) ?>"
                preview-url="<?php echo $preview_url ?>"
                edit="<?php echo $edit_link ?>"
        ></draft-sender>
		<?php
		return ob_get_clean();
	}





	public function list() {

		$all_drafts = $this->campaigndraftmodel->all();

		ob_start();

        include AUTOCAMPAIGNER_DIR . '/templates/admin/drafts.php';

		return ob_get_clean();

	}





	/**
	 * @return string
	 */
	protected function get_link($screen) {

		return  add_query_arg( [
			'page'   => 'autocampaigner_admin_page',
			'screen' => $screen,
			'draft'  => $this->campaigndraftmodel->id,
		], admin_url() );
	}

}

