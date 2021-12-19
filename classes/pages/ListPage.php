<?php

namespace Autocampaigner\Pages;

use Autocampaigner\CampaignMonitor\Lists;

class ListPage extends Pages {

	private $lists;
    public  $listModel;

	public function __construct() {

        $this->listModel = new Lists();

		$this->load_lists();

		return $this;

	}


	public function load_lists() {
		$this->lists = $this->listModel->list();
	}


	public function render() {
		ob_start();
		?>
            <subscriber-lists
                    :lists="<?php echo htmlentities( json_encode($this->lists)) ?>"
                    :used-lists-preload="<?php echo htmlentities( json_encode( $this->listModel->get_used_lists() )) ?>"
            ></subscriber-lists>
		<?php
		return ob_get_clean();
	}
}