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
                    <div>
						<?php echo $content ?? '' ?>
                    </div>
            </div>
        </div>
    </div>
</div>
