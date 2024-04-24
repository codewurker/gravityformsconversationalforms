<?php

namespace Gravity_Forms\Gravity_Forms_Conversational_Forms\Settings;

use Gravity_Forms\Gravity_Forms\Settings\Fields;
use \GFCommon;
use \GFFormsModel;

defined( 'ABSPATH' ) || die();

class FileUpload extends Fields\Base {

	/**
	 * Field type.
	 *
	 * @since 2.5
	 *
	 * @var string
	 */
	public $type = 'file_upload';

	/**
	 * Allowed file types
	 *
	 * @var string[]
	 */
	public $allowed_types = array(
		'gif',
		'jpg',
		'jpeg',
		'png',
	);

	/**
	 * Max width of upload.
	 *
	 * @var string
	 */
	public $max_width = '800';

	/**
	 * Max height of upload.
	 *
	 * @var string
	 */
	public $max_height = '400';

	/**
	 * Max File Size
	 *
	 * @var int
	 */
	public $max_file_size = 0;

	public $default_value = '';

	// # RENDER METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Render field.
	 *
	 * @since 2.5
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

		// Prepare markup.
		// Display description.
		$html = $this->get_description();

		$props = array(
			'allowedFileTypes' => $this->allowed_types,
			'id'               => 'file_upload_' . esc_attr( $this->settings->get_input_name_prefix() ) . '_' . esc_attr( $this->name ),
			'maxHeight'        => $this->max_height,
			'maxWidth'         => $this->max_width,
			'name'             => esc_attr( $this->settings->get_input_name_prefix() ) . '_' . esc_attr( $this->name ),
			'fileURL'          => rgar( $value, 'file_url' ) ? $value['file_url'] : $this->default_value,
			'fileId'           => rgar( $value, 'attachment_id' ) ? $value['attachment_id'] : 0,
			'customClasses'    => $this->class ? array( $this->class ) : array(),
			'externalManager'  => true,
			'i18n'             => array(
				'click_to_upload' => __( 'Click to upload', 'gravityforms' ),
				'drag_n_drop'     => __( 'or drag and drop', 'gravityforms' ),
				'max'             => __( 'recommended size:', 'gravityforms' ),
				'or'              => __( 'or', 'gravityforms' ),
				'replace'         => __( 'Replace', 'gravityforms' ),
				'delete'          => __( 'Delete', 'gravityforms' ),
			),
		);

		$html .= sprintf(
			'<span data-js="gform-input--file-upload" data-js-props="%s"></span>',
			esc_attr( json_encode( $props ) )
		);

		// Insert after input markup.

		$html .= $this->get_error_icon();

		$html .= isset( $this->after_input ) ? $this->after_input : '';

		return $html;
	}

	// # DATA METHODS ------------------------------------------------------------------------------------------------

	/**
	 * Get the value of the field.
	 *
	 * @return array|bool|mixed|string
	 */
	public function get_value() {
		$value = parent::get_value();

		if ( ! $_POST ) {
			return $value;
		}

		if ( ! empty( $value ) ) {
			return $value;
		}

		/**
		 * If the request is a $_POST request and the page is displaying, it means there was an error
		 * in the form validation. Files have not yet populated the global $files array, so we need
		 * to grab the value from the posted array instead.
		 */
		$posted = $this->settings->get_posted_values();
		$name   = $this->get_parsed_name();

		if ( isset( $posted[ $name . '_file_url' ] ) ) {
			return $posted[ $name . '_file_url' ];
		}

		return $value;
	}

}
