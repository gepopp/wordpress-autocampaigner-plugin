<?php

namespace Autocampaigner\Controller;


use Autocampaigner\Model\TemplateModel;



class TemplateController extends BaseController {





	public TemplateModel $templatemodel;






	public function list() {


		$all = $this->templatemodel->all();

		ob_start();
		?>
        <template-list
                :templates="<?php $this->as_html_attribute( $all ); ?>"
        ></template-list>
		<?php
        
		return ob_get_clean();
	}


}