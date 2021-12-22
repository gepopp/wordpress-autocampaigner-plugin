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

		foreach ( $editables as $editable ) {
			$parent = $editable->parentNode;

			$parent->removeChild( $editable );

			$child = $mock->createElement( 'image-editable' );
			$child->setAttribute( 'src', $editable->getAttribute( 'src' ) );
			$child->setAttribute( 'width', $editable->getAttribute( 'width' ) );
			$child->setAttribute( 'height', $editable->getAttribute( 'height' ) );

			$parent->appendChild( $child );
		}


		$document_multilines = $mock->getElementsByTagName( 'multiline' );
		foreach ( $document_multilines as $document_multiline ) {
			$parent = $document_multiline->parentNode;
			$child  = $mock->createElement( 'multiline' );

			$child->setAttribute( 'text', $document_multiline->textContent );

			$parent->replaceChild( $child, $document_multiline );
		}


		$tables      = $mock->getElementsByTagName( 'table' );
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

			$type = $post_table->getAttribute('fill');
			if(!array_key_exists($type, $offsets)){
				$offsets[$type] = 1;
			}else{
				$offsets[$type] = $offsets[$type] + 1;
			}



			$posts = get_posts( ['post_type' => $type, 'posts_per_page' => 1, 'offset' => $offsets[$type] ] );
			$post = array_shift($posts);
			$post_id = $post->ID;

			$post_image = $post_table->getElementsByTagName( 'image-editable' );
			$post_image[0]->setAttribute( 'src', get_the_post_thumbnail_url( $post_id, 'horizontal_box' ) );

			$post_table->getElementsByTagName( 'multiline' )->item( 0 )->setAttribute( 'text', get_the_title( $post_id ) );
			$post_table->getElementsByTagName( 'multiline' )->item( 1 )->setAttribute( 'text', get_the_excerpt( $post_id ) );
			$post_table->getElementsByTagName( 'multiline' )->item( 2 )->setAttribute( 'text', '<a href="' . get_the_permalink( $post_id ) . '">jetzt lesen</a>' );

		}


		return $mock->saveHTML();

	}

}