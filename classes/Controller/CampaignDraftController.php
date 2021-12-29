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



	protected $campaign;





	public function __construct( $id = false ) {

		$id = sanitize_text_field( $_POST['draft'] ?? $id );

		$this->campaign = new CampaignDraftModel( $id );

	}





	public function update_or_create() {


         if( $this->campaign->id ){
             $this->campaign->validate()->update();
         }else{
             $this->campaign->validate()->create();
         }

		if ( ! wp_doing_ajax() ) {

			$action = sanitize_text_field( $_POST['action'] );

			if ( $action == 'autocampaigner_save_draft_content' ) {

				$screen = 'send';

			} else {

				$screen = 'edit';

			}

			wp_safe_redirect(
				add_query_arg( [
					'draft'  => $this->campaign->id,
					'screen' => $screen,
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

        $this->campaign->delete_on_api();

		ob_start();
		?>
        <create-campaign
                :draft-id="<?php echo (int) $this->campaign->id ?>"
                :defaults="<?php $this->as_html_attribute( $this->campaign->default_values() ) ?>"
                :templates="<?php $this->as_html_attribute( ( new TemplateModel() )->all() ); ?>"
                template="<?php echo $this->campaign->template ?>"
                :campaignnames="<?php $this->as_html_attribute( ( new CampaignApiModel() )->campaign_names() ) ?>"
        ></create-campaign>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function draft_editor() {

		$this->campaign->delete_on_api();



		$parser = new TemplateParser( $this->campaign );
		$html   = $parser->parse_template_html_for_editor();

		$restart_link = add_query_arg( [
			'page'   => 'autocampaigner_admin_page',
			'screen' => 'create',
			'draft'  => $this->campaign->id,
		], admin_url() );

		ob_start();
		?>
        <editor
                :draft="<?php echo $this->campaign->id ?>"
                back="<?php echo $restart_link ?>"
        ><?php echo $html ?></editor>
		<?php
		return ob_get_clean();
	}





	/**
	 * @return false|string
	 */
	protected function draft_preview() {


		$edit_link = add_query_arg( [
			'page'   => 'autocampaigner_admin_page',
			'screen' => 'edit',
			'draft'  => $this->campaign->id,
		], admin_url() );


        $preview_url = AUTOCAMPAIGNER_URL . 'draft_files/' . $this->campaign->html_url;


		ob_start();
		?>
        <draft-sender
                :campaign-id="<?php echo $this->campaign->id ?>"
                :campaign="<?php echo htmlentities( json_encode( $this->campaign ) ) ?>"
                preview-url="<?php echo $preview_url ?>"
                edit="<?php echo $edit_link ?>"
        ></draft-sender>
		<?php
		return ob_get_clean();
	}





	public function list() {

		$all_drafts = $this->campaign->all();

		ob_start();
		?>
        <div class="ac-mb-10 ac-pb-10 ac-border-b ac-border-plugin">
            <ul class="ac-px-10">
				<?php foreach ( $all_drafts as $draft ): ?>
					<?php if ( in_array( $draft->status, [ 'drafts', 'scheduled' ] ) ): ?>
                        <li data-v-5b67b1bd="" class="ac-p-2 ac-mb-2 ac-border-b ac-border-plugin last:ac-border-none">
                            <label class="ac-flex ac-items-center ac-space-x-4 ac-w-full">
                                <div class="ac-w-full ac-flex ac-w-full ac-justify-between">
                                    <a href="<?php echo add_query_arg( [
										'page'  => 'autocampaigner_admin_page',
										'draft' => $draft->id,
									] ) ?>"
                                       class="focus:ac-text-plugin hover:ac-text-plugin focus:ac-outline-plugin ac-font-bold"><?php echo $draft->header_data['campaign_name'] ?></a>
                                    <p class="ac-flex-none"><?php echo Carbon::parse( $draft->created_at )->diffForHumans() ?></p>
                                </div>
                            </label>
                        </li>
					<?php endif; ?>
				<?php endforeach; ?>
            </ul>
        </div>

        <div class="ac-mb-10 ac-pb-10 ac-border-b ac-border-plugin">
            <h3 class="ac-text-2xl ac-font-semibold ac-mb-20"><?php _e( 'Recent Sent Drafts', 'autocampaigner' ) ?></h3>
            <ul class="ac-px-10">
				<?php foreach ( $all_drafts as $draft ): ?>
					<?php if ( $draft->status == 'sent' ): ?>
                        <li data-v-5b67b1bd="" class="ac-p-2 ac-mb-2 ac-border-b ac-border-plugin last:ac-border-none">
                            <label class="ac-flex ac-items-center ac-space-x-4 ac-w-full">
                                <div class="ac-w-full ac-flex ac-w-full ac-justify-between">
                                    <a href="<?php echo add_query_arg( [
										'page'   => 'autocampaigner_admin_page',
										'draft'  => $draft->id,
										'screen' => 'send',
									] ) ?>"
                                       class="focus:ac-text-plugin hover:ac-text-plugin focus:ac-outline-plugin ac-font-bold"><?php echo $draft->header_data['campaign_name'] ?></a>
                                    <p class="ac-flex-none"><?php echo Carbon::parse( $draft->created_at )->diffForHumans() ?></p>
                                </div>
                            </label>
                        </li>
					<?php endif; ?>
				<?php endforeach; ?>
            </ul>
        </div>

		<?php
		return ob_get_clean();

	}

}

