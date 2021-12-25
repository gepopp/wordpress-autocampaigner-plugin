<?php

namespace Autocampaigner\Controller;

use Carbon\Carbon;
use Autocampaigner\Options;
use Autocampaigner\TemplateParser;
use Autocampaigner\Model\TemplateModel;
use Autocampaigner\Model\CampaignApiModel;
use Autocampaigner\Model\CampaignDraftModel;



class CampaignDraftController extends BaseController {





	use Options;



	public CampaignDraftModel $model;





	public function list() {

		$all = $this->model->all();

		/**
		 * @var $all \Autocampaigner\Model\CampaignDraftModel[]
		 */
		foreach ( $all as $item ) {
			$status       = ( new CampaignApiModel() )->determine_cm_campaign_status( $item->cm_id );
			$item->status = $status['status'];
			$item->update_status();
		}

		ob_start();
		?>
        <ul>
			<?php foreach ( $all as $draft ): ?>
				<?php if ( $draft->status == 'new' ): ?>
                    <li data-v-5b67b1bd="" class="ac-p-2 ac-mb-2 ac-border-b ac-border-plugin">
                        <label class="ac-flex ac-items-center ac-space-x-4 ac-w-full">
                            <div class="ac-w-full ac-flex ac-w-full ac-justify-between">
                                <a href="<?php echo add_query_arg(['page' => 'autocampaigner_admin_page', 'draft' => $draft->id]) ?>"
                                   class="focus:ac-text-plugin hover:ac-text-plugin focus:ac-outline-plugin ac-font-bold"><?php echo $draft->header_data['campaign_name'] ?></a>
                                <p class="ac-flex-none">IN BEARBEITUNG</p>
                                <p class="ac-flex-none"><?php echo Carbon::parse($item->created_at)->diffForHumans() ?></p>
                            </div>
                        </label>
                    </li>
				<?php endif; ?>
	        <?php endforeach; ?>
	        <?php foreach ( $all as $draft ): ?>
				<?php if ( in_array($draft->status, ['drafts','scheduled']) ): ?>
                    <li data-v-5b67b1bd="" class="ac-p-2 ac-mb-2 ac-border-b ac-border-plugin">
                        <label class="ac-flex ac-items-center ac-space-x-4 ac-w-full">
                            <div class="ac-w-full ac-flex ac-w-full ac-justify-between">
                                <a href="<?php echo add_query_arg(['page' => 'autocampaigner_admin_page', 'draft' => $draft->id, 'screen' => 'send']) ?>"
                                   class="focus:ac-text-plugin hover:ac-text-plugin focus:ac-outline-plugin ac-font-bold"><?php echo $draft->header_data['campaign_name'] ?></a>
                                <p class="ac-flex-none">BEREITS HOCHGELADEN</p>
                                <p class="ac-flex-none"><?php echo Carbon::parse($item->created_at)->diffForHumans() ?></p>
                            </div>
                        </label>
                    </li>
				<?php endif; ?>
			<?php endforeach; ?>
        </ul>

		<?php
		return ob_get_clean();

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


		$templates = htmlentities( json_encode( ( new TemplateModel() )->get_templates_with_description() ) );
		ob_start();
		?>
        <create-campaign
                :draft-id="<?php echo (int) $this->model->get_id() ?>"
                :defaults="<?php $this->model->as_html_attribute( 'default_values' ) ?>"
                :templates="<?php echo $templates ?>"
                template="<?php echo $this->model->template ?>"
                :campaignnames="<?php ( new CampaignApiModel() )->as_html_attribute( 'campaign_names' ) ?>"
        ></create-campaign>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function draft_editor() {


		$parser = new TemplateParser( $this->model->template, $this->model->content );
		$html   = $parser->parse_template_html_for_editor();

		ob_start();
		?>
        <editor
                :draft="<?php echo $this->model->get_id() ?>"
        ><?php echo $html ?></editor>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function draft_preview() {

		$statusinfo = ( new CampaignApiModel() )->determine_cm_campaign_status( $this->model->cm_id );

		ob_start();
		?>
        <draft-sender
                :campaign="<?php echo htmlentities( json_encode( $this->model ) ) ?>"
                :info="<?php echo htmlentities( json_encode( $statusinfo ) ) ?>"
                template-preview="<?php echo $this->get_templates_folder_url() . $this->model->template . '/index.html' ?>"
        ></draft-sender>
		<?php
		return ob_get_clean();
	}


}

