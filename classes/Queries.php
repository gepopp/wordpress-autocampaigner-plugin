<?php

namespace Autocampaigner;

trait Queries {





	public function search_posts( $search, $post_type = 'post' ) {


		$query = new \WP_Query( [
			'post_type'      => $post_type,
			'posts_per_page' => 10,
			's'              => $search,
		] );

		$result = [];
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();

				$result[] = [
					'id'        => get_the_ID(),
					'image'     => get_the_post_thumbnail_url( get_the_ID(), 'article' ),
					'title'     => get_the_title(),
					'excerpt'   => get_the_excerpt(),
					'permalink' => get_the_permalink(),
				];
			}
		}

		return $result;
	}





	public function search_images( $search ) {

		$args = [
			'post_type'      => 'attachment',
			'posts_per_page' => 15,
			's'              => $search,
			'post_status'    => 'any',
		];

		$results = new \WP_Query( $args );

		$images = [];


		if ( $results->have_posts() ) {
			while ( $results->have_posts() ) {
				$results->the_post();

				$images[] = [
					'id'    => get_the_ID(),
					'title' => get_the_title(),
					'url'  => wp_get_attachment_image_url( get_the_ID(), sanitize_text_field($_POST['size']) ),
					'thumbnail'  => wp_get_attachment_image_url( get_the_ID(), 'thumbnail' ),

				];
			}
		}


		return $images;
	}


}