<div class="wrap" id="automailer-page">
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
        <div class="ac-bg-white ac-grid ac-grid-cols-6 ac-gap-5">
			<?php if ( $has_sidebar ): ?>
                <div class="ac-px-5">
                    <div class="ac-flex ac-flex-col ac-w-full ac-mt-10">
                        <div class="ac-tab-button ac-px-5 ac-py-3 ac-cursor-pointer" id="ac-main">
							<?php _e( 'API settings', 'autocampaigner' ) ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
            <div class="<?php echo $has_sidebar ? 'ac-col-span-5' : 'ac-col-span-6' ?> ac-p-5">
				<?php if ( $content ): ?>
                    <div>
						<?php echo $content ?? '' ?>
                    </div>
				<?php else: ?>

                    <div class="ac-border-red-700 ac-border-2 ac-bg-red-300 ac-bg-opacity-50 ac-p-10 ac-flex ac-items-center ac-justify-center ac-h-64">
                        <div class="ac-flex ac-space-x-10 ac-items-center">
                            <div class="ac-bg-red-700 ac-text-white ac-rounded-full ac-p-4">
                                <svg class="ac-w-20 ac-h-20" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="ac-text-red-700 ac-text-3xl"><?php _e('Pleace enter your API credentials in the settings first.', 'autocampaigner') ?></p>
                            </div>
                        </div>
                    </div>


				<?php endif; ?>
            </div>
        </div>
    </div>
</div>
