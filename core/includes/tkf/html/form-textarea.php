<?php
class TK_Form_Textarea extends TK_Form_Element{		var $extra;	var $before_element;	var $after_textarea;		/**	 * PHP 4 constructor	 *	 * @package Themekraft Framework	 * @since 0.1.0	 * 	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra textarea code, $rows Number of rows in textarea, $cols Number of columns in textarea, $before_textarea Code before textarea, $after_textarea Code after textarea ]	 */	function tk_form_textarea( $args ){		$this->__construct( $args );	}		/**	 * PHP 5 constructor	 *	 * @package Themekraft Framework	 * @since 0.1.0	 * 	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra textarea code, $rows Number of rows in textarea, $cols Number of columns in textarea, $before_textarea Code before textarea, $after_textarea Code after textarea ]	 */	function __construct( $args ){		$defaults = array(			'id' => '',			'name' => '',			'value' => '',			'extra' => '',			'rows' => '',			'cols' => '',			'before_element' => '',			'after_element' => ''		);
		$args = wp_parse_args($args, $defaults);		extract( $args , EXTR_SKIP );
		parent::__construct( $args );
		$this->extra = $extra;
		$this->rows = $rows;		$this->cols = $cols;		$this->before_element = $before_element;		$this->after_element = $after_element;			}
	/**	 * Getting HTML of textfield	 *	 * @package Themekraft Framework	 * @since 0.1.0	 * 	 * @return string $html The html of the textfield	 */	function get_html(){				$id = '';		$name = '';		$rows = '';		$cols = '';		$extra = '';
				if( $this->id != '' ) $id = ' id="' . $this->id . '"';		if( $this->name != '' ) $name = ' name="' . $this->name . '"';		if( $this->rows != '' ) $rows = ' rows="' . $this->rows . '"';		if( $this->cols != '' ) $cols = ' cols="' . $this->cols . '"';				if( $this->extra != '' ) $extra = $this->extra;				$html = $this->before_element;		$html.= '<div class="linedtextarea"><textarea' . $id . $name . $rows . $cols . $extra . ' class="lined"	/>' . $this->value  . '</textarea></div>';		$html.= $this->after_element;				return $html;	}}function tk_textarea( $args, $return_object = FALSE ){	$textarea = new TK_Form_Textarea( $args );		if( TRUE == $return_object ){		return $textarea;	}else{		return $textarea->get_html();	}	}