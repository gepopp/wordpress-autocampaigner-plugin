<?php

namespace Autocampaigner\Settings;


use Autocampaigner\Templates;
use Autocampaigner\Controller\ListController;
use Autocampaigner\Controller\TemplateController;
use Autocampaigner\Settings\Sections\MainSettings;
use Autocampaigner\Controller\CampaignDraftController;



class AdminSettingsPages {





	use SettingsFieldsOutput, Templates;



	public function __construct() {

		if ( false == get_option( 'autocampaigner_general_settings' ) ) {
			add_option( 'autocampaigner_general_settings' );
		}

		add_action( 'admin_menu', [ $this, 'add_settings_page' ] );

		new MainSettings();

	}





	public function add_settings_page() {

		add_menu_page(
			__( 'Autocampaigner', 'autocampaigner' ),
			__( 'Autocampaigner', 'autocampaigner' ),
			'administrator',
			'autocampaigner_admin_page',
			[ $this, 'admin_page_content' ],
			'dashicons-email'
		);

		add_submenu_page(
			'autocampaigner_admin_page',
			__( 'Drafts', 'autocampaigner' ),
			__( 'Drafts', 'autocampaigner' ),
			'administrator',
			'autocampaigner_drafts_page',
			[ $this, 'drafts_page_content' ],
			2 );


		add_submenu_page(
			'autocampaigner_admin_page',
			__( 'Subscriber Lists', 'autocampaigner' ),
			__( 'Subscriber Lists', 'autocampaigner' ),
			'administrator',
			'autocampaigner_lists_page',
			[ $this, 'lists_page_content' ],
			3 );


		add_submenu_page(
			'autocampaigner_admin_page',
			__( 'Templates', 'autocampaigner' ),
			__( 'Templates', 'autocampaigner' ),
			'administrator',
			'autocampaigner_tepmplates_page',
			[ $this, 'tempplates_page_content' ],
			4 );

		add_submenu_page(
			'autocampaigner_admin_page',
			__( 'Autocampaigner Settings', 'autocampaigner' ),
			__( 'Autocampaigner Settings', 'autocampaigner' ),
			'administrator',
			'autocampaigner_settings_page',
			[ $this, 'settings_page_content' ],
			5 );


	}





	function drafts_page_content() {

		$this->admin_template(
			'base',
			[
				'title'   => __( 'Recent Drafts', 'autocampaigner' ),
				'content' => [ CampaignDraftController::class, 'list' ],
			] );

	}





	function tempplates_page_content() {

		$this->admin_template(
			'base',
			[
				'title'   => __( 'Templates', 'autocampaigner' ),
				'content' => [ TemplateController::class, 'list' ],
			] );
	}





	function lists_page_content() {

		$this->admin_template(
			'base',
			[
				'title'   => __( 'Subscriber lists', 'autocampaigner' ),
				'content' => [ ListController::class, 'list' ],
			] );

	}





	function admin_page_content() {

		$this->admin_template(
			'base',
			[
				'title'   => __( 'New Campaign', 'autocampaigner' ),
				'content' => [ CampaignDraftController::class, 'render' ],
			] );

	}





	public function settings_page_content() {

		?>
        <div class="wrap">
            <!-- Make a call to the WordPress function for rendering errors when settings are saved. -->
			<?php settings_errors(); ?>
            <!-- Create the form that will be used to render our options -->
            <div>
                <div class="ac-flex xs:ac-flex-col ac-justify-between ac-bg-white ac-p-5">
                    <h2 class="ac-text-2xl ac-font-semibold"><?php _e( 'Autocampaigner settings', 'autocampaigner' ) ?></h2>
                    <a href="https://poppgerhard.at">
                        <div class="ac-flex ac-flex-col ac-inline-flex">
                            <div class="ac-flex">
                                <div class="ac-bg-black ac-text-white ac-p-2 ac-text-sm ac-md:text-lg ac-lg:text-2xl ac-font-semibold ac-border ac-border-black">
                                    Popp
                                </div>
                                <div class="ac-text-black ac-p-2 ac-text-sm ac-md:text-lg ac-lg:text-2xl ac-font-semibold ac-border ac-border-black">
                                    Gerhard Hubert
                                </div>
                            </div>
                            <div class="ac-text-right ac-text-xs ac-lg:text-sm ac-text-plugin">
                                webentwicklung &amp; webdesign
                            </div>
                        </div>
                    </a>
                </div>
                <div class="ac-bg-white ac-grid ac-grid-cols-6 ac-gap-5">
                    <div class="ac-px-5">
                        <div class="ac-flex ac-flex-col ac-w-full ac-mt-10">
                            <div class="ac-tab-button ac-px-5 ac-py-3 ac-cursor-pointer" id="ac-main">
								<?php _e( 'API settings', 'autocampaigner' ) ?>
                            </div>
                        </div>
                    </div>
                    <div class="ac-col-span-5 ac-p-5">
                        <form method="post" action="options.php">
                            <div>
                                <div class="ac-p-5 ac-bg-gray-200">
                                    <div class="ac-tab" data-tab="ac-main">
                                        <h3 class="ac-text-semibold ac-text-xl"><?php _e( 'API settings', 'autocampaigner' ) ?></h3>
										<?php settings_fields( 'autocampaigner_main_settings_section' ); ?>
										<?php do_settings_sections( 'autocampaigner_main_settings_page' ); ?>
                                    </div>
                                </div>
                            </div>
                            <input type="submit" name="submit" id="submit"
                                   class="ac-block ac-w-full ac-text-center ac-bg-plugin ac-py-3 ac-text-center ac-text-white ac-my-10 ac-shadow-lg ac-hover:shadow ac-cursor-pointer"
                                   value="<?php echo __( 'save', 'autocampaigner' ) ?>">
                        </form>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}