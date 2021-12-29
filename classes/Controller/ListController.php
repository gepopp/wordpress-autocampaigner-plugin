<?php

namespace Autocampaigner\Controller;

use Autocampaigner\Model\ListModel;



class ListController extends BaseController {


    public $list;


    public function __construct() {

    $this->list = new ListModel();

    }





	public function list() {

        $lists = $this->list->all();

		ob_start();
		?>
        <subscriber-lists
                :lists="<?php $this->as_html_attribute( $lists ) ?>"
                :used-lists-preload="<?php $this->as_html_attribute( $this->list->get_used_lists() ) ?>"
        ></subscriber-lists>
		<?php
		return ob_get_clean();
	}

}