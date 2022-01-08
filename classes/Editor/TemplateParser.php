<?php

namespace Autocampaigner\Editor;

use Autocampaigner\Options;
use Autocampaigner\Model\CampaignDraftModel;



class TemplateParser {





	use Options;



	public $template_direcotry;





	public $index_path;





	public $content;





	public $saved_content;





	public $folder;





	public $multilines;





	public function __construct( CampaignDraftModel $campaign ) {


		$this->saved_content = $campaign->content;

		$this->folder = $campaign->template;

		$this->template_direcotry = $this->get_templates_folder() . $this->folder;

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
		$this->fill_images_with_content( $document );

		$this->replace_repeaters( $document );

		$this->make_post_tables_editable( $document );

		$this->fill_multiliens_with_content( $document );
		$this->fill_singlelines_with_content( $document );

		$repeaters = $document->getElementsByTagName( 'repeater' );
		for ( $repeater_index = 0; $repeater_index < count( $repeaters ); $repeater_index ++ ) {

			$layouts = $repeaters[ $repeater_index ]->getElementsByTagName( 'layout' );

			for ( $layout_index = 0; $layout_index < count( $layouts ); $layout_index ++ ) {

				$this->fill_images_with_content( $layouts[ $layout_index ], true, $repeater_index, $layout_index );
				$this->fill_multiliens_with_content( $layouts[ $layout_index ], true, $repeater_index, $layout_index );
				$this->fill_singlelines_with_content( $layouts[ $layout_index ], true, $repeater_index, $layout_index );

			}
		}


		return $document->saveHTML();

	}





	public function fill_singlelines_with_content( $document, $is_repeater = false, $repeater_index = 0, $layout_index = 0 ) {

		if ( ! isset( $this->saved_content->Singlelines ) ) {
			return $document;
		}


		$to_remove    = [];
		$single_lines = $document->getElementsByTagName( 'singleline' );


		if ( ! $is_repeater ) {

			for ( $i = 0; $i < count( $single_lines ); $i ++ ) {

				$singleline = $single_lines[ $i ];

				while ( $singleline->parentNode ) {

					if ( $singleline->tagName == 'repeater' ) {
						$to_remove[] = $i;
					}
					$singleline = $singleline->parentNode;
				}
			}
		}

		$set = 0;
		for ( $i = 0; $i < count( $single_lines ); $i ++ ) {

			if ( in_array( $i, $to_remove ) ) {
				continue;
			}

			if ( ! $is_repeater ) {
				$single_lines[ $i ]->setAttribute( 'link', $this->saved_content->Singlelines[ $i ]->Href ?? '' );

			} else {
				$single_lines[ $i ]->setAttribute( 'link', $this->saved_content->Repeaters[ $repeater_index ]->Items[ $layout_index ]->Singlelines[ $i ]->Href ?? '' );
				$single_lines[ $i ]->setAttribute( 'text', $this->saved_content->Repeaters[ $repeater_index ]->Items[ $layout_index ]->Singlelines[ $i ]->Content ?? '' );
			}
			$set ++;
		}


	}





	public function fill_multiliens_with_content( $document, $is_repeater = false, $repeater_index = 0, $layout_index = 0 ) {

		if ( ! isset( $this->saved_content->Images ) ) {
			return $document;
		}


		$to_remove       = [];
		$document_multilines = $document->getElementsByTagName( 'multiline' );


		if ( ! $is_repeater ) {

			for ( $i = 0; $i < count( $document_multilines ); $i ++ ) {

				$image = $document_multilines[ $i ];

				while ( $image->parentNode ) {

					if ( $image->tagName == 'repeater' ) {
						$to_remove[] = $i;
					}
					$image = $image->parentNode;
				}
			}
		}

		$set = 0;
		for ( $i = 0; $i < count( $document_multilines ); $i ++ ) {

			if ( in_array( $i, $to_remove ) ) {
				continue;
			}

			if ( ! $is_repeater ) {

				$document_multilines[ $i ]->setAttribute( 'text', $this->saved_content->Multilines[ $set ]->Content ?? '' );
			} else {
				$document_multilines[ $i ]->setAttribute( 'text', $this->saved_content->Repeaters[ $repeater_index ]->Items[ $layout_index ]->Multilines[ $set ]->Content ?? '' );
			}
			$set ++;
		}


	}





	public function fill_images_with_content( $document, $is_repeater = false, $repeater_index = 0, $layout_index = 0 ) {


		if ( ! isset( $this->saved_content->Images ) ) {
			return $document;
		}

		$to_remove       = [];
		$document_images = $document->getElementsByTagName( 'image-editable' );


		if ( ! $is_repeater ) {


			for ( $i = 0; $i < count( $document_images ); $i ++ ) {

				$image = $document_images[ $i ];

				while ( $image->parentNode ) {

					if ( $image->tagName == 'repeater' ) {
						$to_remove[] = $i;
					}
					$image = $image->parentNode;
				}
			}
		}

		$set = 0;
		for ( $i = 0; $i < count( $document_images ); $i ++ ) {

			if ( in_array( $i, $to_remove ) ) {
				continue;
			}



			$old_src = $document_images[$i]->getAttribute('src');

			if ( ! $is_repeater ) {

				$src = $this->saved_content->Images[ $i ]->Content;

				$document_images[ $i ]->setAttribute( 'src',  !empty($src) ? $src : $old_src );
				$document_images[ $i ]->setAttribute( 'alt', $this->saved_content->Images[ $i ]->Alt ?? '' );
				$document_images[ $i ]->setAttribute( 'link', $this->saved_content->Images[ $i ]->Href ?? '' );
			} else {

				$src = $this->saved_content->Repeaters[ $repeater_index ]->Items[ $layout_index ]->Images[ $i ]->Content;

				$document_images[ $i ]->setAttribute( 'src',  !empty($src) ? $src : $old_src );
				$document_images[ $i ]->setAttribute( 'alt', $this->saved_content->Repeaters[ $repeater_index ]->Items[ $layout_index ]->Images[ $i ]->Alt ?? '' );
				$document_images[ $i ]->setAttribute( 'link', $this->saved_content->Repeaters[ $repeater_index ]->Items[ $layout_index ]->Images[ $i ]->Href ?? '' );
			}
			$set ++;
		}

	}





	public function make_images_editable( $document ) {

		$document_images = $document->getElementsByTagName( 'img' );

		$editables = [];

		foreach ( $document_images as $document_image ) {

			$src  = $document_image->getAttribute( 'src' );
			$info = pathinfo( $src );
			$document_image->setAttribute( 'src', AUTOCAMPAIGNER_URL . "email_templates/$this->folder/images/" . $info['basename'] );


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
			$child->setAttribute( 'style', $editable->getAttribute( 'style' ) );

			$parent->appendChild( $child );
		}

	}





	public function replace_repeaters( $document ) {

		$document_multilines = $document->getElementsByTagName( 'multiline' );
		foreach ( $document_multilines as $document_multiline ) {
			$parent = $document_multiline->parentNode;
			$child  = $document->createElement( 'multiline' );

			$child->setAttribute( 'text', $document_multiline->textContent );

			if($document_multiline->hasAttribute('meta')){
				$child->setAttribute('meta', $document_multiline->getAttribute('meta'));
			}


			$parent->replaceChild( $child, $document_multiline );
		}

	}





	public function make_singlelines_editable( $document ) {

		$singlelines  = $document->getElementsByTagName( 'singleline' );
		$single_lines = [];


		foreach ( $single_lines as $single_line ) {

			$single_line->setAttribute( 'text', $single_line->textContent );
			$single_line->textContent = '';

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

			$post_image[0]->setAttribute( 'src', get_the_post_thumbnail_url( $post_id, $post_image[0]->getAttribute( 'size' ) ) );
			$post_image[0]->setAttribute( 'href', get_the_permalink( $post_id ) );


			$post_table->getElementsByTagName( 'multiline' )->item( 0 )->setAttribute( 'text', get_the_title( $post_id ) );
			$post_table->getElementsByTagName( 'multiline' )->item( 1 )->setAttribute( 'text', get_the_excerpt( $post_id ) );

			$singlelines = $post_table->getElementsByTagName( 'singleline' );

			for($i = 0; $i < count($singlelines); $i++){

				if($post_table->getElementsByTagName( 'singleline' )->item( $i )->hasAttribute('meta')){

					$post_table->getElementsByTagName( 'singleline' )->item( $i )->setAttribute( 'text', get_post_meta( $post_id, $post_table->getElementsByTagName( 'singleline' )->item( $i )->getAttribute('meta'), true ) );
					$post_table->getElementsByTagName( 'singleline' )->item( $i )->setAttribute( 'link', get_the_permalink( $post_id ) );
					$post_table->getElementsByTagName( 'singleline' )->item( $i )->setAttribute( 'meta', $post_table->getElementsByTagName( 'singleline' )->item( $i )->getAttribute('meta') );
					$post_table->getElementsByTagName( 'singleline' )->item( $i )->textContent = '';

				}else{

					$post_table->getElementsByTagName( 'singleline' )->item( $i )->setAttribute( 'link', get_the_permalink( $post_id ) );
					$post_table->getElementsByTagName( 'singleline' )->item( $i )->setAttribute( 'meta', 'link' );
					$post_table->getElementsByTagName( 'singleline' )->item( $i )->setAttribute( 'text', trim( $post_table->getElementsByTagName( 'singleline' )->item( $i )->textContent ) );
					$post_table->getElementsByTagName( 'singleline' )->item( $i )->textContent = '';
				}

			}

		}

	}



}