<?php

namespace Autocampaigner\Editor;

use Carbon\Carbon;
use Autocampaigner\Options;
use Symfony\Component\DomCrawler\Crawler;
use Autocampaigner\Model\CampaignDraftModel;



class HtmlFileCreator {





	use Options;



	protected CampaignDraftModel $draft;





	protected $template_document;





	private $html;





	public function __construct( CampaignDraftModel $draft ) {


		$this->draft = $draft;

		$this->html = file_get_contents( $this->get_templates_folder() . $this->draft->template . '/index.html' );

		$this->template_document = new \DOMDocument();

		$file = $this->get_templates_folder() . $this->draft->template . '/index.html';

		@$this->template_document->loadHTMLFile( $this->get_templates_folder() . $this->draft->template . '/index.html' );

		$this->parse();

		$this->write_file();

	}





	public function parse() {

		$repeaters = $this->template_document->getElementsByTagName( 'repeater' );

		$repeaters_content = $this->draft->content->Repeaters;


		for ( $i = 0; $i < count( $repeaters ); $i ++ ) {
			$this->parse_repeater_content( $repeaters[ $i ], $repeaters_content[ $i ] );
		}


	}





	public function parse_repeater_content( \DOMElement $repeater, $content ) {


		$layouts = $repeater->getElementsByTagName( 'layout' );

		$layouts_content = $content->Items;


		/**
		 * @var \DOMElement $layout
		 */
		for ( $i = 0; $i < count( $layouts ); $i ++ ) {
			$this->parse_layout_content( $layouts[ $i ], $layouts_content[ $i ] );
		}


	}





	public function parse_layout_content( $layout, $content ) {

		$images = new Crawler( $layout );
		$images = $images->filterXPath( '//img' );

		foreach ( $images as $i => $image ) {
			$this->parse_image_editable( $image, $content->Images[ $i ] );
		}



		$singlelines = new Crawler( $layout );
		$singlelines = $singlelines->filterXPath( '//singleline' );

		foreach ($singlelines as $i => $singleline){
			$this->parse_singleline( $singleline, $content->Singlelines[ $i ] );
		}


		$crawler = new Crawler( $layout );
		$crawler = $crawler->filterXPath( '//multiline' );

		$parents = [];
		foreach ( $crawler as $index => $multiline ) {
			$this->parse_multiline($multiline, $content->Multilines[$index]->Content);
		}

	}





	public function parse_multiline( \DOMElement $multiline, $content ) {

		$multiline->parentNode->replaceChild( $this->template_document->createCDATASection( $content ), $multiline);

	}





	public function parse_singleline( \DOMElement $singleline, $content ) {

		/**
		 * @var \DOMElement $parent
		 */
		$parent = $singleline->parentNode;

		$link = false;
		if ( isset( $content->Href ) ) {
			$link = $this->template_document->createElement( 'a' );
			$link->setAttribute( 'href', $content->Href );
		}

		$new_singleline            = $this->template_document->createElement( 'div' );
		$new_singleline->nodeValue = $content->Content;

		if ( $link ) {
			$link->appendChild( $new_singleline );
			$parent->replaceChild( $link, $singleline );
		} else {
			$parent->replaceChild( $new_singleline, $singleline );
		}

	}





	public function parse_image_editable( \DOMElement $image_editable, $content ) {


		/**
		 * @var \DOMElement $parent
		 */
		$parent = $image_editable->parentNode;


		$link = false;
		if ( !empty( $content->Href ) ) {
			$link = $this->template_document->createElement( 'a' );
			$link->setAttribute( 'href', $content->Href );
		}

		$image = $this->template_document->createElement( 'img' );


		$image->setAttribute( 'src', $content->Content );
		$image->setAttribute( 'alt', $content->Alt );
		$image->setAttribute( 'width', $image_editable->getAttribute( 'width' ) );
		$image->setAttribute( 'height', $image_editable->getAttribute( 'height' ) );

		if ( $link ) {
			$link->appendChild( $image );
			$parent->replaceChild( $link, $image_editable );
		} else {
			$parent->replaceChild( $image, $image_editable );
		}


	}





	public function write_file() {

		$draft_dir = AUTOCAMPAIGNER_DIR . '/draft_files';

		if ( ! is_dir( $draft_dir ) ) {
			mkdir( $draft_dir );
		}

		$file = $this->draft->html_url;
		if ( empty( $file ) ) {
			$this->draft->html_url = Carbon::now()->format( 'Y_m_d_H_i' ) . '_' . sanitize_title( $this->draft->header_data['campaign_name'] ) . '.html';
		}

		return $this->template_document->saveHTMLFile( trailingslashit( $draft_dir ) . $this->draft->html_url );

	}

}