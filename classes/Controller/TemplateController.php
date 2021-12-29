<?php /** @noinspection PhpHierarchyChecksInspection */

namespace Autocampaigner\Controller;


use Autocampaigner\Model\TemplateModel;



class TemplateController extends BaseController {





	public TemplateModel $templates;


    public function __construct() {

        $this->templates = new TemplateModel();

    }





	public function list() {

		$all = $this->templates->all();

		ob_start();
		?>
        <template-list
                :templates="<?php $this->as_html_attribute( $all ); ?>"
        ></template-list>
		<?php
        
		return ob_get_clean();
	}


}