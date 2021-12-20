<?php

namespace Autocampaigner\models;

use Autocampaigner\controller\TemplatesController;

class TemplatesModel extends BaseModel {


	public function __construct() {

		$this->controller = new TemplatesController();
		return $this;
	}


	protected function render() {

        ob_start();
		?>
			<template-list
                    :templates="<?php $this->controller->as_html_attribute('list') ?>"
                    :exisitng="<?php $this->controller->as_html_attribute('get_existing_templates') ?>"
            ></template-list>
		<?php

		return ob_get_clean();
	}


}