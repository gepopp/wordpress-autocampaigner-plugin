<?php

namespace Autocampaigner\Controller;


class ListController extends BaseController {





	public $listmodel;





	public function list() {

		$lists = $this->listmodel->all();

		ob_start();
		?>
        <subscriber-lists
                :lists="<?php $this->as_html_attribute( $lists ) ?>"
                :used-lists-preload="<?php $this->as_html_attribute( $this->listmodel->get_used_lists() ) ?>"
        ></subscriber-lists>
		<?php
		return ob_get_clean();
	}

}