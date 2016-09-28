<?php
/**
 * HTML elements
 *
 * @package     Give
 * @subpackage  Classes/Give_HTML_Elements
 * @copyright   Copyright (c) 2016, WordImpress
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Give_HTML_Elements Class
 *
 * A helper class for outputting common HTML elements, such as product drop downs.
 *
 * @since 1.0
 */
class Give_HTML_Elements {

	/**
	 * Donations Dropdown
	 *
	 * Renders an HTML Dropdown of all the donations.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array  $args Arguments for the dropdown.
	 *
	 * @return string       Donations dropdown.
	 */
	public function transactions_dropdown( $args = array() ) {

		$defaults = array(
			'name'        => 'transactions',
			'id'          => 'transactions',
			'class'       => '',
			'multiple'    => false,
			'selected'    => 0,
			'chosen'      => false,
			'number'      => 30,
			'placeholder' => esc_html__( 'Select a transaction', 'give' )
		);

		$args = wp_parse_args( $args, $defaults );


		$payments = new Give_Payments_Query( array(
			'number' => $args['number']
		) );

		$payments = $payments->get_payments();

		$options = array();

		//Provide nice human readable options.
		if ( $payments ) {
			$options[0] = esc_html__( 'Select a donation', 'give' );
			foreach ( $payments as $payment ) {

				$options[ absint( $payment->ID ) ] = esc_html( '#' . $payment->ID . ' - ' . $payment->email . ' - ' . $payment->form_title);

			}
		} else {
			$options[0] = esc_html__( 'No donations found.', 'give' );
		}


		$output = $this->select( array(
			'name'             => $args['name'],
			'selected'         => $args['selected'],
			'id'               => $args['id'],
			'class'            => $args['class'],
			'options'          => $options,
			'chosen'           => $args['chosen'],
			'multiple'         => $args['multiple'],
			'placeholder'      => $args['placeholder'],
			'select_atts'      => $args['select_atts'],
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}

	/**
	 * Give Forms Dropdown
	 *
	 * Renders an HTML Dropdown of all the Give Forms.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $args Arguments for the dropdown.
	 *
	 * @return string      Give forms dropdown.
	 */
	public function forms_dropdown( $args = array() ) {

		$defaults = array(
			'name'        => 'forms',
			'id'          => 'forms',
			'class'       => '',
			'multiple'    => false,
			'selected'    => 0,
			'chosen'      => false,
			'number'      => 30,
			'placeholder' => esc_attr__( 'Select a Form', 'give' )
		);

		$args = wp_parse_args( $args, $defaults );

		$forms = get_posts( array(
			'post_type'      => 'give_forms',
			'orderby'        => 'title',
			'order'          => 'ASC',
			'posts_per_page' => $args['number']
		) );

		$options = array();

		if ( $forms ) {
			$options[0] = esc_attr__( 'Select a Form', 'give' );
			foreach ( $forms as $form ) {
				$options[ absint( $form->ID ) ] = esc_html( $form->post_title );
			}
		} else {
			$options[0] = esc_html__( 'No forms found.', 'give' );
		}

		// This ensures that any selected forms are included in the drop down
		if ( is_array( $args['selected'] ) ) {
			foreach ( $args['selected'] as $item ) {
				if ( ! in_array( $item, $options ) ) {
					$options[ $item ] = get_the_title( $item );
				}
			}
		} elseif ( is_numeric( $args['selected'] ) && $args['selected'] !== 0 ) {
			if ( ! in_array( $args['selected'], $options ) ) {
				$options[ $args['selected'] ] = get_the_title( $args['selected'] );
			}
		}

		$output = $this->select( array(
			'name'             => $args['name'],
			'selected'         => $args['selected'],
			'id'               => $args['id'],
			'class'            => $args['class'],
			'options'          => $options,
			'chosen'           => $args['chosen'],
			'multiple'         => $args['multiple'],
			'placeholder'      => $args['placeholder'],
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}

	/**
	 * Donors Dropdown
	 *
	 * Renders an HTML Dropdown of all customers.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $args Arguments for the dropdown.
	 *
	 * @return string      Donors dropdown.
	 */
	public function donor_dropdown( $args = array() ) {

		$defaults = array(
			'name'        => 'customers',
			'id'          => 'customers',
			'class'       => '',
			'multiple'    => false,
			'selected'    => 0,
			'chosen'      => true,
			'placeholder' => esc_attr__( 'Select a Donor', 'give' ),
			'number'      => 30
		);

		$args = wp_parse_args( $args, $defaults );

		$customers = Give()->customers->get_customers( array(
			'number' => $args['number']
		) );

		$options = array();

		if ( $customers ) {
			$options[0] = esc_html__( 'No donor attached', 'give' );
			foreach ( $customers as $customer ) {
				$options[ absint( $customer->id ) ] = esc_html( $customer->name . ' (' . $customer->email . ')' );
			}
		} else {
			$options[0] = esc_html__( 'No donors found.', 'give' );
		}

		if ( ! empty( $args['selected'] ) ) {

			// If a selected customer has been specified, we need to ensure it's in the initial list of customers displayed.

			if ( ! array_key_exists( $args['selected'], $options ) ) {

				$customer = new Give_Customer( $args['selected'] );

				if ( $customer ) {

					$options[ absint( $args['selected'] ) ] = esc_html( $customer->name . ' (' . $customer->email . ')' );

				}

			}

		}

		$output = $this->select( array(
			'name'             => $args['name'],
			'selected'         => $args['selected'],
			'id'               => $args['id'],
			'class'            => $args['class'] . ' give-customer-select',
			'options'          => $options,
			'multiple'         => $args['multiple'],
			'chosen'           => $args['chosen'],
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}

	/**
	 * Categories Dropdown
	 *
	 * Renders an HTML Dropdown of all the Categories.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  string $name     Name attribute of the dropdown. Default is 'give_forms_categories'.
	 * @param  int    $selected Category to select automatically. Default is 0.
	 *
	 * @return string           Categories dropdown.
	 */
	public function category_dropdown( $name = 'give_forms_categories', $selected = 0 ) {
		$categories = get_terms( 'give_forms_category', apply_filters( 'give_forms_category_dropdown', array() ) );
		$options    = array();

		foreach ( $categories as $category ) {
			$options[ absint( $category->term_id ) ] = esc_html( $category->name );
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => esc_html__( 'All Categories', 'give' ),
			'show_option_none' => false
		) );

		return $output;
	}

	/**
	 * Years Dropdown
	 *
	 * Renders an HTML Dropdown of years.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  string $name         Name attribute of the dropdown. Default is 'year'.
	 * @param  int    $selected     Year to select automatically. Default is 0.
	 * @param  int    $years_before Number of years before the current year the dropdown should start with. Default is 5.
	 * @param  int    $years_after  Number of years after the current year the dropdown should finish at. Default is 0.
	 *
	 * @return string               Years dropdown.
	 */
	public function year_dropdown( $name = 'year', $selected = 0, $years_before = 5, $years_after = 0 ) {
		$current    = date( 'Y' );
		$start_year = $current - absint( $years_before );
		$end_year   = $current + absint( $years_after );
		$selected   = empty( $selected ) ? date( 'Y' ) : $selected;
		$options    = array();

		while ( $start_year <= $end_year ) {
			$options[ absint( $start_year ) ] = $start_year;
			$start_year ++;
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}

	/**
	 * Months Dropdown
	 *
	 * Renders an HTML Dropdown of months.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  string $name     Name attribute of the dropdown. Default is 'month'.
	 * @param  int    $selected Month to select automatically. Default is 0.
	 *
	 * @return string           Months dropdown.
	 */
	public function month_dropdown( $name = 'month', $selected = 0 ) {
		$month    = 1;
		$options  = array();
		$selected = empty( $selected ) ? date( 'n' ) : $selected;

		while ( $month <= 12 ) {
			$options[ absint( $month ) ] = give_month_num_to_name( $month );
			$month ++;
		}

		$output = $this->select( array(
			'name'             => $name,
			'selected'         => $selected,
			'options'          => $options,
			'show_option_all'  => false,
			'show_option_none' => false
		) );

		return $output;
	}

	/**
	 * Dropdown
	 *
	 * Renders an HTML Dropdown.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $args Arguments for the dropdown.
	 *
	 * @return string      The dropdown.
	 */
	public function select( $args = array() ) {
		$defaults = array(
			'options'          => array(),
			'name'             => null,
			'class'            => '',
			'id'               => '',
			'selected'         => 0,
			'chosen'           => false,
			'placeholder'      => null,
			'multiple'         => false,
			'select_atts'      => false,
			'show_option_all'  => esc_html__( 'All', 'give' ),
			'show_option_none' => esc_html__( 'None', 'give' )
		);

		$args = wp_parse_args( $args, $defaults );

		if ( $args['multiple'] ) {
			$multiple = ' MULTIPLE';
		} else {
			$multiple = '';
		}

		if ( $args['chosen'] ) {
			$args['class'] .= ' give-select-chosen';
		}

		if ( $args['placeholder'] ) {
			$placeholder = $args['placeholder'];
		} else {
			$placeholder = '';
		}


		$output = '<select name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( sanitize_key( str_replace( '-', '_', $args['id'] ) ) ) . '" class="give-select ' . esc_attr( $args['class'] ) . '"' . $multiple . ' ' . $args['select_atts'] . ' data-placeholder="' . $placeholder . '">';

		if ( $args['show_option_all'] ) {
			if ( $args['multiple'] ) {
				$selected = selected( true, in_array( 0, $args['selected'] ), false );
			} else {
				$selected = selected( $args['selected'], 0, false );
			}
			$output .= '<option value="all"' . $selected . '>' . esc_html( $args['show_option_all'] ) . '</option>';
		}

		if ( ! empty( $args['options'] ) ) {

			if ( $args['show_option_none'] ) {
				if ( $args['multiple'] ) {
					$selected = selected( true, in_array( - 1, $args['selected'] ), false );
				} else {
					$selected = selected( $args['selected'], - 1, false );
				}
				$output .= '<option value="-1"' . $selected . '>' . esc_html( $args['show_option_none'] ) . '</option>';
			}

			foreach ( $args['options'] as $key => $option ) {

				if ( $args['multiple'] && is_array( $args['selected'] ) ) {
					$selected = selected( true, in_array( $key, $args['selected'] ), false );
				} else {
					$selected = selected( $args['selected'], $key, false );
				}

				$output .= '<option value="' . esc_attr( $key ) . '"' . $selected . '>' . esc_html( $option ) . '</option>';
			}
		}

		$output .= '</select>';

		return $output;
	}

	/**
	 * Checkbox
	 *
	 * Renders an HTML Checkbox.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $args Arguments for the Checkbox.
	 *
	 * @return string      The checkbox.
	 */
	public function checkbox( $args = array() ) {
		$defaults = array(
			'name'    => null,
			'current' => null,
			'class'   => 'give-checkbox',
			'options' => array(
				'disabled' => false,
				'readonly' => false
			)
		);

		$args = wp_parse_args( $args, $defaults );

		$options = '';
		if ( ! empty( $args['options']['disabled'] ) ) {
			$options .= ' disabled="disabled"';
		} elseif ( ! empty( $args['options']['readonly'] ) ) {
			$options .= ' readonly';
		}

		$output = '<input type="checkbox"' . $options . ' name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '" class="' . $args['class'] . ' ' . esc_attr( $args['name'] ) . '" ' . checked( 1, $args['current'], false ) . ' />';

		return $output;
	}

	/**
	 * Text Field
	 *
	 * Renders an HTML Text field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $args Arguments for the text field.
	 *
	 * @return string      The text field.
	 */
	public function text( $args = array() ) {
		// Backwards compatibility
		if ( func_num_args() > 1 ) {
			$args = func_get_args();

			$name  = $args[0];
			$value = isset( $args[1] ) ? $args[1] : '';
			$label = isset( $args[2] ) ? $args[2] : '';
			$desc  = isset( $args[3] ) ? $args[3] : '';
		}

		$defaults = array(
			'name'         => isset( $name ) ? $name : 'text',
			'value'        => isset( $value ) ? $value : null,
			'label'        => isset( $label ) ? $label : null,
			'desc'         => isset( $desc ) ? $desc : null,
			'placeholder'  => '',
			'class'        => 'regular-text',
			'disabled'     => false,
			'autocomplete' => '',
			'data'         => false
		);

		$args = wp_parse_args( $args, $defaults );

		$disabled = '';
		if ( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}

		$data = '';
		if ( ! empty( $args['data'] ) ) {
			foreach ( $args['data'] as $key => $value ) {
				$data .= 'data-' . $key . '="' . $value . '" ';
			}
		}

		$output = '<span id="give-' . sanitize_key( $args['name'] ) . '-wrap">';

		$output .= '<label class="give-label" for="give-' . sanitize_key( $args['name'] ) . '">' . esc_html( $args['label'] ) . '</label>';

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="give-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$output .= '<input type="text" name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '" autocomplete="' . esc_attr( $args['autocomplete'] ) . '" value="' . esc_attr( $args['value'] ) . '" placeholder="' . esc_attr( $args['placeholder'] ) . '" class="' . $args['class'] . '" ' . $data . '' . $disabled . '/>';

		$output .= '</span>';

		return $output;
	}

	/**
	 * Date Picker
	 *
	 * Renders a date picker field.
	 *
	 * @since  1.5
	 * @access public
	 *
	 * @param  array $args Arguments for the date picker.
	 *
	 * @return string      The date picker.
	 */
	public function date_field( $args = array() ) {

		if ( empty( $args['class'] ) ) {
			$args['class'] = 'give_datepicker';
		} elseif ( ! strpos( $args['class'], 'give_datepicker' ) ) {
			$args['class'] .= ' give_datepicker';
		}

		return $this->text( $args );
	}

	/**
	 * Textarea
	 *
	 * Renders an HTML textarea.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $args Arguments for the textarea.
	 *
	 * @return string      The textarea.
	 */
	public function textarea( $args = array() ) {
		$defaults = array(
			'name'     => 'textarea',
			'value'    => null,
			'label'    => null,
			'desc'     => null,
			'class'    => 'large-text',
			'disabled' => false
		);

		$args = wp_parse_args( $args, $defaults );

		$disabled = '';
		if ( $args['disabled'] ) {
			$disabled = ' disabled="disabled"';
		}

		$output = '<span id="give-' . sanitize_key( $args['name'] ) . '-wrap">';

		$output .= '<label class="give-label" for="give-' . sanitize_key( $args['name'] ) . '">' . esc_html( $args['label'] ) . '</label>';

		$output .= '<textarea name="' . esc_attr( $args['name'] ) . '" id="' . esc_attr( $args['name'] ) . '" class="' . $args['class'] . '"' . $disabled . '>' . esc_attr( $args['value'] ) . '</textarea>';

		if ( ! empty( $args['desc'] ) ) {
			$output .= '<span class="give-description">' . esc_html( $args['desc'] ) . '</span>';
		}

		$output .= '</span>';

		return $output;
	}

	/**
	 * User Search Field
	 *
	 * Renders an ajax user search field.
	 *
	 * @since  1.0
	 * @access public
	 *
	 * @param  array $args Arguments for the search field.
	 *
	 * @return string      The text field with ajax search.
	 */
	public function ajax_user_search( $args = array() ) {

		$defaults = array(
			'name'         => 'user_id',
			'value'        => null,
			'placeholder'  => esc_attr__( 'Enter username', 'give' ),
			'label'        => null,
			'desc'         => null,
			'class'        => '',
			'disabled'     => false,
			'autocomplete' => 'off',
			'data'         => false
		);

		$args = wp_parse_args( $args, $defaults );

		$args['class'] = 'give-ajax-user-search ' . $args['class'];

		$output = '<span class="give_user_search_wrap">';
		$output .= $this->text( $args );
		$output .= '<span class="give_user_search_results hidden"><a class="give-ajax-user-cancel" aria-label="' . esc_attr__( 'Cancel', 'give' ) . '" href="#">x</a><span></span></span>';
		$output .= '</span>';

		return $output;
	}

}
