<?php

if ( ! function_exists( 'is_conversational_form' ) ) {
	/**
	 * Check if the current form view is a conversational form.
	 *
	 * @since 1.4.0
	 *
	 * @param array $form The form array
	 * @param bool  $cache Whether use a cached result if available.
	 *
	 * @return bool True if the current form view is a conversational form.
	 */
	function is_conversational_form( $form, $cache = true ) {
		static $cached = array();

		if ( isset( $cached[ $form['id'] ] ) && $cache ) {
			return $cached[ $form['id'] ];
		}

		global $wp_query;

		if (
			! empty( $form['gf_theme_layers']['enable'] ) &&
			! empty( $form['gf_theme_layers']['post_id'] ) &&
			$form['gf_theme_layers']['post_id'] === $wp_query->queried_object_id &&
			$wp_query->post->post_type == 'conversational_form'
		) {
			$cached[ $form['id'] ] = true;

			return $cached[ $form['id'] ];
		}

		$cached[ $form['id'] ] = false;

		return $cached[ $form['id'] ];
	}
}
