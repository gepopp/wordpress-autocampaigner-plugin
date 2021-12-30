<div class="ac-mb-10 ac-pb-10 ac-border-b ac-border-plugin">
	<ul class="ac-px-10">
		<?php use Carbon\Carbon;



		foreach ( $all_drafts as $draft ): ?>
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