<?php

namespace Autocampaigner\Controller;

class ListController extends BaseController {





	public function list() {

		ob_start();
		?>
        <subscriber-lists
                :lists="<?php $this->model->as_html_attribute( 'list' ) ?>"
                :used-lists-preload="<?php $this->model->as_html_attribute( 'get_used_lists' ) ?>"
        ></subscriber-lists>
		<?php
		return ob_get_clean();
	}


}