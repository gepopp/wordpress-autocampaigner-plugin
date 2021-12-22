<?php

namespace Autocampaigner\models;

use Autocampaigner\controller\ListsController;

class ListsModel extends BaseModel {



	protected function render() {

		ob_start();
		?>
            <subscriber-lists
                    :lists="<?php $this->controller->as_html_attribute('list') ?>"
                    :used-lists-preload="<?php $this->controller->as_html_attribute('get_used_lists') ?>"
            ></subscriber-lists>
		<?php
		return ob_get_clean();
	}
}