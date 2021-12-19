<?php

namespace Autocampaigner\Pages;

use Autocampaigner\CampaignMonitor\Lists;
use Autocampaigner\CampaignMonitor\Templates;

class TemplatesListPage extends Pages {

	private $templates;
    public  $templatesModel;

	public function __construct() {

        $this->templatesModel = new Templates();

		$this->load_lists();

		return $this;

	}


	public function load_lists() {
		$this->lists = $this->templatesModel->list();
	}


	public function render() {
		$templates = htmlentities( json_encode( $this->templatesModel->list() ) );
        $existing = htmlentities( json_encode( $this->templatesModel->get_existing_templates() ));
        $saved = htmlentities( json_encode( $this->templatesModel->saved_local_templates() ));
		ob_start();
		?>
			<template-list
                    :templates="<?php echo $templates ?>"
                    :exisitng="<?php echo $existing ?>"
            ></template-list>
		<?php
		return ob_get_clean();
	}


}