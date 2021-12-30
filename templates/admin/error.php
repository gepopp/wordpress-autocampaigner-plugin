<div class="wrap ac-bg-white" id="automailer-page">
    <div>
        <div class="ac-flex xs:ac-flex-col ac-justify-between ac-bg-white ac-p-5">
            <h2 class="ac-text-2xl ac-font-semibold"><?php echo $title ?></h2>
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
        <div class="ac-bg-white ac-flex ac-justify-center ac-items-center ac-p-72 ac-bg-white">
            <div class="ac-flex ac-flex-col ac-items-center ac-justify-center">
                <svg class="ac-w-20 ac-h-20 ac-text-plugin" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
				<?php $error = json_decode( $error ) ?>
                <p class="ac-text-plugin ac-text-3xl"><?php _e( 'API Fehler', 'autocampaigner' ) ?></p>
                <p class="ac-text-red-700"><?php echo $error->Message ?></p>
                <p class="ac-text-red-700 ac-text-xl ac-mt-10"><?php _e( 'Überprüfe die', 'autocampaigner' ) ?></p>
                <a href="<?php echo add_query_arg( 'page', 'autocampaigner_settings_page', admin_url() ) ?>" class="ac-button ac-bloc ac-px-10"><?php _e( 'Einstellungen', 'autocampaigner' ) ?></a>
            </div>
        </div>
    </div>
</div>
