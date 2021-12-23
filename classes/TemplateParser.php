<?php

namespace Autocampaigner;

class TemplateParser {





	use Options;



	public $template_direcotry;





	public $index_path;





	public $content;





	public $folder;





	public function __construct( $folder ) {

		$this->folder = $folder;

		$this->template_direcotry = AUTOCAMPAIGNER_DIR . '/email_templates/' . $folder;

		$this->index_path = $this->template_direcotry . '/index.html';

		$this->content = file_get_contents( $this->index_path );

	}





	public function parse_template_html_for_editor() {


		$raw_document = new \DOMDocument();
		@$raw_document->loadHTML( $this->content, LIBXML_ERR_NONE );

		$body = $raw_document->getElementsByTagName( 'body' )->item( 0 );

		$document = new \DOMDocument();

		foreach ( $body->childNodes as $child ) {
			$document->appendChild( $document->importNode( $child, true ) );
		}

		$this->make_images_editable( $document );
		$this->replace_repeaters( $document );
		$this->make_post_tables_editable( $document );


		return $document->saveHTML();

	}





	public function make_images_editable( $document ) {

		$document_images = $document->getElementsByTagName( 'img' );

		$editables = [];

		foreach ( $document_images as $document_image ) {

			$src  = $document_image->getAttribute( 'src' );
			$info = pathinfo( $src );
			$document_image->setAttribute( 'src', AUTOCAMPAIGNER_URL . "/email_templates/$this->folder/images/" . $info['basename'] );


			$editable = (bool) $document_image->getAttribute( 'editable' );

			if ( $editable ) {
				$editables[] = $document_image;
			}

		}

		foreach ( $editables as $editable ) {
			$parent = $editable->parentNode;

			$parent->removeChild( $editable );

			$child = $document->createElement( 'image-editable' );
			$child->setAttribute( 'src', $editable->getAttribute( 'src' ) );
			$child->setAttribute( 'width', $editable->getAttribute( 'width' ) );
			$child->setAttribute( 'height', $editable->getAttribute( 'height' ) );
			$child->setAttribute( 'size', $editable->getAttribute( 'size' ) );

			$parent->appendChild( $child );
		}

	}





	public function replace_repeaters( $document ) {

		$document_multilines = $document->getElementsByTagName( 'multiline' );
		foreach ( $document_multilines as $document_multiline ) {
			$parent = $document_multiline->parentNode;
			$child  = $document->createElement( 'multiline' );

			$child->setAttribute( 'text', $document_multiline->textContent );

			$parent->replaceChild( $child, $document_multiline );
		}

	}





	public function make_post_tables_editable( $document ) {

		$tables      = $document->getElementsByTagName( 'table' );
		$post_tables = [];


		$post_types = get_post_types();


		foreach ( $tables as $table ) {
			$fill = $table->getAttribute( 'fill' );
			if ( in_array( $fill, $post_types ) ) {
				$post_tables[] = $table;
			}
		}


		$offsets = [];

		foreach ( $post_tables as $index => $post_table ) {

			$type = $post_table->getAttribute( 'fill' );
			if ( ! array_key_exists( $type, $offsets ) ) {
				$offsets[ $type ] = 1;
			} else {
				$offsets[ $type ] = $offsets[ $type ] + 1;
			}


			$posts   = get_posts( [ 'post_type' => $type, 'posts_per_page' => 1, 'offset' => $offsets[ $type ] ] );
			$post    = array_shift( $posts );
			$post_id = $post->ID;

			$post_image = $post_table->getElementsByTagName( 'image-editable' );

			$post_image[0]->setAttribute( 'src', get_the_post_thumbnail_url( $post_id, $post_image[0]->getAttribute('size') ) );

			$post_table->getElementsByTagName( 'multiline' )->item( 0 )->setAttribute( 'text', get_the_title( $post_id ) );
			$post_table->getElementsByTagName( 'multiline' )->item( 1 )->setAttribute( 'text', get_the_excerpt( $post_id ) );
			$post_table->getElementsByTagName( 'singleline' )->item( 0 )->setAttribute( 'link', get_the_permalink( $post_id ) );
			$post_table->getElementsByTagName( 'singleline' )->item( 0 )->setAttribute( 'text', trim( $post_table->getElementsByTagName( 'singleline' )->item( 0 )->textContent ) );
			$post_table->getElementsByTagName( 'singleline' )->item( 0 )->textContent = '';

		}

	}

}