<?php

namespace Gravity_Forms\Gravity_Forms_Conversational_Forms\Settings;

use Gravity_Forms\Gravity_Forms_Conversational_Forms\GF_Conversational_Forms;
use Gravity_Forms\Gravity_Forms\Settings\Fields;
use \GFFormsModel;

defined( 'ABSPATH' ) || die();

class Permalink extends Fields\Base {

	/**
	 * Field type.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $type = 'permalink';

	/**
	 * Input type.
	 *
	 * @since 1.0
	 *
	 * @var string
	 */
	public $input_type = 'text';

	public $input_prefix;

	public $input_suffix;

	public $action_button;

	public $action_button_icon = 'eye';

	public $action_button_icon_prefix = 'gform-common-icon';

	public $action_button_text = false;


	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Render field.
	 *
	 * @since 1.0
	 *
	 * @return string
	 */
	public function markup() {

		// Get value.
		$value = $this->get_value();

		// Prepare after_input.
		// Dynamic after_input content should use a callable to render.
		if ( isset( $this->after_input ) && is_callable( $this->after_input ) ) {
			$this->after_input = call_user_func( $this->after_input, $value, $this );
		}

		// If we do not have a post_id, disable the input.
		$attributes = $this->get_attributes();
		$form_id    = rgget( 'id' );
		$form_meta  = \GFFormsModel::get_form_meta( $form_id );
		if ( rgars( $form_meta, 'gf_theme_layers/post_id' ) ) {
			unset( $attributes['disabled'] );
		} else {
			$attributes['disabled'] = 'disabled';
		}

		// Prepare markup.
		// Display description.
		$html = $this->get_description();

		$html .= sprintf(
			'<span class="%1$s">%10$s %12$s<input data-js="permalink-input-value" class="gform-input gform-input--text" type="%2$s" name="%3$s_%4$s" value="%5$s" %6$s %7$s />%13$s %14$s %11$s %8$s %9$s</span>',
			esc_attr( $this->get_container_classes() ),
			esc_attr( $this->input_type ),
			esc_attr( $this->settings->get_input_name_prefix() ),
			esc_attr( $this->name ),
			$value ? esc_attr( htmlspecialchars( $value, ENT_QUOTES ) ) : '',
			$this->get_describer() ? sprintf( 'aria-describedby="%s"', $this->get_describer() ) : '',
			implode( ' ', $attributes ),
			isset( $this->append ) ? sprintf( '<span class="gform-settings-field__text-append">%s</span>', esc_html( $this->append ) ) : '',
			$this->get_error_icon(),
			$this->get_addon_wrapper_open(),
			$this->get_addon_wrapper_close(),
			$this->get_prefix(),
			$this->get_suffix(),
			$this->get_action_button()
		);

		// Insert after input markup.

		$html .= isset( $this->after_input ) ? $this->after_input : '';

		return $html;

	}

	/**
	 * Determine if this field needs the addon wrapper markup.
	 *
	 * @since 1.0
	 *
	 * @return bool
	 */
	private function needs_addon_wrapper() {
		return $this->input_prefix || $this->input_suffix || $this->action_button;
	}

	/**
	 * Get the opening markup for the addon wrapper.
	 *
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public function get_addon_wrapper_open() {
		if ( ! $this->needs_addon_wrapper() ) {
			return null;
		}

		return sprintf(
			'<div class="gform-input-add-on-wrapper %s %s %s">',
			$this->input_prefix ? 'gform-input-add-on-wrapper--prefix' : null,
			$this->input_suffix ? 'gform-input-add-on-wrapper--suffix' : null,
			$this->action_button ? 'gform-input-add-on-wrapper--action-button' : null
		);
	}

	/**
	 * Get the closing markup for the addon wrapper.
	 *
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public function get_addon_wrapper_close() {
		if ( ! $this->needs_addon_wrapper() ) {
			return null;
		}

		return '</div>';
	}

	public function get_value() {
		$form      = rgget( 'id' );
		$form_meta = \GFFormsModel::get_form_meta( $form );

		if( rgars( $form_meta, 'gf_theme_layers/post_id' ) ) {
			$post = get_post( $form_meta['gf_theme_layers']['post_id'] );
			return $post->post_name;
		} else {
			if ( $this->default_value ) {
				return $this->default_value;
			}
		}

		return '';
	}

	/**
	 * Get the markup for the input prefix.
	 *
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public function get_prefix() {
		if ( ! $this->input_prefix ) {
			return null;
		}

		return sprintf( '<div class="gform-input__add-on gform-input__add-on--prefix">%s</div>', esc_html( $this->input_prefix ) );
	}

	/**
	 * Get the markup for the input suffix.
	 *
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public function get_suffix() {
		if ( ! $this->input_suffix ) {
			return null;
		}

		return sprintf( '<div class="gform-input__add-on gform-input__add-on--suffix">%s</div>', esc_html( $this->input_suffix ) );
	}

	/**
	 * Get the markup for the action button. (By default used to open the permalink in a new tab).
	 *
	 * @since 1.0
	 *
	 * @return string|null
	 */
	public function get_action_button() {
		if ( ! $this->action_button ) {
			return null;
		}

		$form      = rgget( 'id' );
		$form_meta = \GFFormsModel::get_form_meta( $form );

		if( rgars( $form_meta, 'gf_theme_layers/post_id' ) ) {
			$post = get_post( $form_meta['gf_theme_layers']['post_id'] );
		} else {
			return '';
		}

		return sprintf(
			'<button data-js="permalink-action-button" data-js-root="%3$s" class="gform-button gform-button--size-r gform-button--white gform-button--active-type-loader gform-button--icon-leading gform-input__add-on--action-button" data-saved-value="%5$s">
				<i class="gform-button__icon %1$s %1$s--%2$s" data-js="button-icon"></i>
				%4$s
			</button>',
			$this->action_button_icon_prefix,
			$this->action_button_icon,
			$this->input_prefix,
			$this->action_button_text ? sprintf( '<span class="gform-button__text gform-button__text--inactive" data-js="button-active-text">%s</span>', $this->action_button_text ) : null,
			$post->post_name,
		);
	}

}
