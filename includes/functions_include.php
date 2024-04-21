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

		global $wp;

		$is_plain  = get_option( 'permalink_structure' ) == '';
		$query_var = \Gravity_Forms\Gravity_Forms_Conversational_Forms\GF_Conversational_Forms::QUERY_VAR;

		if ( $is_plain && isset( $wp->query_vars[ $query_var ] ) ) {
			$slug = strtolower( $wp->query_vars[ $query_var ] );
		} else {
			$slug = strtolower( $wp->request );
		}

		if (
			! empty( $form['gf_theme_layers']['enable'] ) &&
			! empty( $form['gf_theme_layers']['form_full_screen_slug'] ) &&
			$form['gf_theme_layers']['form_full_screen_slug'] === $slug
		) {
			$cached[ $form['id'] ] = true;

			return $cached[ $form['id'] ];
		}

		$cached[ $form['id'] ] = false;

		return $cached[ $form['id'] ];
	}
}