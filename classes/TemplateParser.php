<?php

namespace Autocampaigner;

class TemplateParser {

	public $template_direcotry;
	public $index_path;
	public $content;
	public $folder;


	public function __construct( $folder ) {

		$this->folder             = $folder;
		$this->template_direcotry = AUTOCAMPAIGNER_DIR . '/email_templates/' . $folder;
		$this->index_path         = $this->template_direcotry . '/index.html';
		$this->content            = file_get_contents( $this->index_path );

	}


	public function replace_multinines() {

		$doc = new \DOMDocument();
		@$doc->loadHTML( $this->content, LIBXML_ERR_NONE );

		$d    = new \DOMDocument;
		$mock = new \DOMDocument;
		@$d->loadHTML( $this->content );
		$body = $d->getElementsByTagName( 'body' )->item( 0 );
		foreach ( $body->childNodes as $child ) {
			$mock->appendChild( $mock->importNode( $child, true ) );
		}

		$document_images = $mock->getElementsByTagName( 'img' );

		$editables = [];

		foreach ( $document_images as $document_image ) {

			$src  = $document_image->getAttribute( 'src' );
			$info = pathinfo( $src );
			$document_image->setAttribute( 'src', AUTOCAMPAIGNER_URL . "/email_templates/$this->folder/images/" . $info['basename'] );

			$data = [
				'src'    => $document_image->getAttribute( 'src' ),
				'width'  => $document_image->getAttribute( 'width' ),
				'height' => $document_image->getAttribute( 'height' ),
			];

			$editable = (bool) $document_image->getAttribute( 'editable' );

			if ( $editable ) {
				$editables[] = $document_image;
			}

		}

		foreach ($editables as $editable){
				$parent = $editable->parentNode;

				$parent->removeChild($editable);

				$child = $mock->createElement('image-editable');
				$child->setAttribute('src', $editable->getAttribute('src') );
				$child->setAttribute('width', $editable->getAttribute('width'));
				$child->setAttribute('height', $editable->getAttribute('height'));

				$parent->appendChild($child);
		}



		$document_multilines = $mock->getElementsByTagName( 'multiline' );
		foreach ( $document_multilines as $document_multiline ) {
			$parent = $document_multiline->parentNode;
			$child  = $mock->createElement( 'multiline' );

			$child->setAttribute( 'text', $document_multiline->textContent );

			$parent->replaceChild( $child, $document_multiline );
		}


		return $mock->saveHTML();

	}

}