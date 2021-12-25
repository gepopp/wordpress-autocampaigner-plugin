<?php /** @noinspection PhpHierarchyChecksInspection */

namespace Autocampaigner\Controller;


use Autocampaigner\Model\TemplateModel;



class TemplateController extends BaseController {



    public TemplateModel $model;



	public function list() {

		ob_start();
		?>
        <template-list
                :exisitng="<?php $this->model->as_html_attribute( 'get_valid_template_folders' ) ?>"
                :templates="<?php $this->model->as_html_attribute('list');  ?>"
        ></template-list>
		<?php

		return ob_get_clean();
	}


}