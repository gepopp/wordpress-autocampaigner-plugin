<?php

namespace Autocampaigner\Controller;

class ListController extends BaseController {





	public function list() {

        $lists = $this->model->all();

		ob_start();
		?>
        <subscriber-lists
                :lists="<?php $this->as_html_attribute( $lists ) ?>"
                :used-lists-preload="<?php $this->as_html_attribute( $this->model->get_used_lists() ) ?>"
        ></subscriber-lists>
		<?php
		return ob_get_clean();
	}

}