<?php /** @noinspection PhpHierarchyChecksInspection */

namespace Autocampaigner\Controller;


use Autocampaigner\Model\TemplateModel;



class TemplateController extends BaseController {





	public TemplateModel $model;





	public function list() {

		$all = $this->model->all();

		ob_start();
		?>
        <template-list
                :templates="<?php $this->as_html_attribute( $all ); ?>"
        ></template-list>
		<?php
        
		return ob_get_clean();
	}


}