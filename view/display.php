<?php
//Metabox to create display areas for ads in ads admin post
class adsforwp_view_display {
    
    private $screen = array(
		'adsforwp',
                'adsforwp-groups'
	);
	private $meta_fields = array(
		array(
			'label' => 'Display Type',
			'id' => 'wheretodisplay',
			'type' => 'select',
			'options' => array(
                                'ad_shortcode'        => 'Shortcode (Manual)',
				'between_the_content' => 'Between the Content (Automatic)',
				'after_the_content'   => 'After the Content (Automatic)',
				'before_the_content'  => 'Before the Content (Automatic)',
                                'custom_target'       => 'Custom Target',
                                'sticky'              => 'Sticky',
			),
		),                
		array(
			'label' => 'Position',
			'id' => 'adposition',
			'type' => 'select',
			'options' => array(
				'50_of_the_content'=>'50% of the content',
				'number_of_paragraph'=>'Number of paragraph',
			),
		),                
                array(
			'label' => 'Count As Per The',
			'id' => 'display_tag_name',
			'type' => 'select',
			'options' => array(                               
				'p_tag'=>'p (default)',
				'div_tag'=>'div', 
                                'img_tag'=>'img',
                                'custom_tag'=>'custom',
			),
		),
                array(
			'label' => 'Enter Your Tag',
			'id' => 'entered_tag_name',
			'type' => 'text',
                        'attributes' => array(				
                               'placeholder' 	=> 'div',	                               
			),
		),
		array(
			'label' => 'Paragraph',
			'id' => 'paragraph_number',
			'type' => 'number',
		),
               array(
			'label' => 'Manual Ad',
			'id' => 'manual_ads_type',
			'type' => 'text',                                
                        'attributes' => array(				
                               'readonly' 	=> 'readonly',	
                               'disabled' 	=> 'disabled',
                               'class' => 'afw_manual_ads_type',
			),
		),               
                array(
			'label' => 'Alignment',
			'id' => 'adsforwp_ad_align',
			'type' => 'radio',
			'options' => array(
				'left'=>'Left',
				'center'=>'Center',
                                'right' => 'Right'
			),
		),
                array(
			'label' => 'Position',
			'id' => 'adsforwp_custom_target_position',
			'type' => 'radio',
			'options' => array(
				'existing_element'=>'Existing html element',
                                'new_element'=>'New html element',
                                
			),
		),
                array(
			'label' => 'jQuery Selector',
			'id' => 'adsforwp_jquery_selector',
			'type' => 'text',
			'attributes' => array(				                               	                               
                               'placeholder' => '#container_id or .container_id',
			),
		),
                array(
			'label' => 'New Element',
			'id' => 'adsforwp_new_element',
			'type' => 'text',                        
		),
                array(
			'label' => 'Action',
			'id' => 'adsforwp_existing_element_action',
			'type' => 'select',
                        'options' => array(
                                'prepend_content'=>'Prepend Content',
				'append_content'=>'Append Content', 
                               // 'replace_content'=>'Replace Content',
                               // 'replace_element'=>'Replace Element',
                        )
		),
                array(		
                        'label' => 'Margin',
			'id' => 'adsforwp_ad_margin',                        
			'type' => 'multiple-text',
                        'fields'=> array(
                            array(	
                            'label' => 'Top',    
                            'id' => 'ad_margin_top',                        
                            'type' => 'number',
                          ),
                            array(	
                            'label' => 'Bottom',    
                            'id' => 'ad_margin_bottom',                        
                            'type' => 'number',
                          ),
                            array(	
                            'label' => 'Left',    
                            'id' => 'ad_margin_left',                        
                            'type' => 'number',
                          ),
                            array(	
                            'label' => 'Right',    
                            'id' => 'ad_margin_right',                        
                            'type' => 'number',
                          ),
                        )
		)
                                
	);
	public function __construct() {                                                                                                     
		add_action( 'add_meta_boxes', array( $this, 'adsforwp_add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'adsforwp_save_fields' ) );                               
                
	}
	public function adsforwp_add_meta_boxes() {                                              
		foreach ( $this->screen as $single_screen ) {                                                                    
			add_meta_box(
				'display-metabox',
				esc_html__( 'Display', 'ads-for-wp' ),
				array( $this, 'adsforwp_meta_box_callback' ),
				$single_screen,
				'normal',
				'high'
			);
		}
	}
	public function adsforwp_meta_box_callback( $post ) {
		wp_nonce_field( 'adsforwp_display_data', 'adsforwp_display_nonce' );
		$this->adsforwp_field_generator( $post );
	}
	public function adsforwp_field_generator( $post ) {
           
		$output = '';
		foreach ( $this->meta_fields as $meta_field ) {
                        $attributes ='';
			$label = '<label for="' . $meta_field['id'] . '">' . esc_html__( $meta_field['label'], 'ads-for-wp' ) . '</label>';
			$meta_value = get_post_meta( $post->ID, $meta_field['id'], true );
			if ( empty( $meta_value ) ) {
                                if($meta_field['id'] == 'adsforwp_new_element'){
                                 $meta_value = esc_html('<div id="'.md5(uniqid(rand(), true)).'"></div>');    
                                }else{
                                 if(isset($meta_field['default'])){
                                  $meta_value = $meta_field['default'];    
                                 }                                   
                                }				                                                                
                        }
			switch ( $meta_field['type'] ) {
				case 'select':
                                    
                                    switch ($meta_field['id']) {
                                        case 'adposition':
                                            $input = sprintf(
						'<select class="afw_select" id="%s" name="%s">',
						$meta_field['id'],
						$meta_field['id']
					);
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
						$input .= sprintf(
							'<option %s value="%s">%s</option>',
							$meta_value === $meta_field_value ? 'selected' : '',
							$meta_field_value,
							esc_html__($value, 'ads-for-wp')                                                        
						);
					}
					$input .= '</select><a href="#" class="adsforwp-advance-option-click">Advance Option</a>';

                                            break;

                                        default:
                                            $input = sprintf(
						'<select class="afw_select" id="%s" name="%s">',
						$meta_field['id'],
						$meta_field['id']
					   );
                                            foreach ( $meta_field['options'] as $key => $value ) {
                                                    $meta_field_value = !is_numeric( $key ) ? $key : $value;
                                                    $input .= sprintf(
                                                            '<option %s value="%s">%s</option>',
                                                            $meta_value === $meta_field_value ? 'selected' : '',
                                                            $meta_field_value,
                                                            esc_html__($value, 'ads-for-wp')                                                        
                                                    );
                                            }
                                            $input .= '</select>';
                                            break;
                                    }                                        					
					break;
                                        
                                        case 'multiple-text':                                        
                                        $input ='<div class="afw_ad_img_margin">';    
                                            
                                        foreach($meta_field['fields'] as $field){
                                         $margin_value = '';   
                                         if(!empty( $meta_value )){
                                            $margin_value = $meta_value[$field['id']];                  
                                         }                                               
                                        $input.= sprintf(
						'<input class="afw_input" %s id="%s" name="adsforwp_ad_margin[%s]" type="%s" placeholder="%s" value="%s">',
						$meta_field['type'] !== 'color' ? '' : '',
						$field['id'],
						$field['id'],
						$field['type'],
						$field['label'],   
                                                $margin_value
                                             );                                        
                                        }
					$input .='</div>';
                                    break;
                                    case 'radio':
                                        
                                        
                                        switch ($meta_field['id']) {
                                            case 'adsforwp_ad_align':
                                                $input = '<fieldset class="afw_ads_margin_field">';
					$input .= '<legend class="screen-reader-text">' . isset($meta_field['label']) . '</legend>';
					$i = 0;
					foreach ( $meta_field['options'] as $key => $value ) {
						$meta_field_value = !is_numeric( $key ) ? $key : $value;
                                                $checked ='';
                                             
                                                if($meta_value==''){
                                                    
                                                    if($key == 'left'){
                                                     $checked = 'checked';   
                                                    }
                                                }else{
                                                     $checked = $meta_value === $meta_field_value ? 'checked' : '';
                                                }
						$input .= sprintf(
							'<label style="padding-right:10px;"><input %s id="%s" name="% s" type="radio" value="% s"> %s</label>%s',
							$checked,
							$meta_field['id'],
							$meta_field['id'],
							$meta_field_value,
							esc_html__($value, 'ads-for-wp'),
							$i < count( $meta_field['options'] ) - 1 ? '' : ''
						);
						$i++;
					}
					$input .= '</fieldset>';

                                                break;
                                            
                                            case 'adsforwp_custom_target_position':
                                                
                                                $input = '<fieldset class="adsforwp-custom-target-fields">';
                                                $input .= '<legend class="screen-reader-text">' . $meta_field['label'] . '</legend>';
                                                $i = 0;
                                                foreach ( $meta_field['options'] as $key => $value ) {
                                                        $meta_field_value = !is_numeric( $key ) ? $key : $value;
                                                        $input .= sprintf(
                                                                '<label style="padding-right:10px;"><input %s id="%s" name="%s" type="radio" value="%s">%s</label>%s',
                                                                $meta_value === $meta_field_value ? 'checked' : '',
                                                                $meta_field['id'],
                                                                $meta_field['id'],
                                                                $meta_field_value,
                                                                $value,
                                                                $i < count( $meta_field['options'] ) - 1 ? '' : ''
                                                        );
                                                        $i++;
                                                }
                                                $input .= '</fieldset>';

                                                break;

                                            default:
                                                break;
                                        }                                                                                					
					break;
				default:
                                    
                                         if(isset($meta_field['attributes'])){
                                      foreach ( $meta_field['attributes'] as $key => $value ) {
                                    
					$attributes .=  $key."=".'"'.$value.'"'.' ';                                        
					}
                                       }
    
					$input = sprintf(
						'<input class="afw_input" %s id="%s" name="%s" type="%s" value="%s" %s>',
						$meta_field['type'] !== 'color' ? '' : '',
						$meta_field['id'],
						$meta_field['id'],
						$meta_field['type'],
						$meta_value,
                                                $attributes    
					);
			}
			$output .= $this->adsforwp_format_rows( $label, $input );
		}
                        $common_function_obj = new adsforwp_admin_common_functions();
                        $allowed_html = $common_function_obj->adsforwp_expanded_allowed_tags();                
                        $in_group = $common_function_obj->adsforwp_check_ads_in_group($post->ID);                               
                if(!empty($in_group)){
                        $group_links = '';
                        foreach($in_group as $group){                       
                        $group_post = get_post($group);                        
                        $group_links .= '<span style="padding-right:5px;"><a href="?post='.esc_attr($group).'&action=edit"> '.esc_html__($group_post->post_title, 'ads-for-wp').'</a>,</span>';    
                        }
                        echo '<p>'.esc_html__('This ad is associated with ', 'ads-for-wp').''.html_entity_decode(esc_html($group_links)).'group</p>';   
                        echo '<table class="form-table" style="display:none;"><tbody>' . wp_kses($output, $allowed_html) . '</tbody></table><div id="afw-embed-code-div"></div>';      
                }else{
                        echo '<table class="form-table adsforwp-display-table"><tbody>' . wp_kses($output, $allowed_html) . '</tbody></table><div style="display:none;" id="afw-embed-code-div"></div>';   
                }
		                
	}
	public function adsforwp_format_rows( $label, $input ) {                                    
		return '<tr><th>'.$label.'</th><td>'.$input.'</td></tr>';                
	}
	public function adsforwp_save_fields( $post_id ) {
                            
		if ( ! isset( $_POST['adsforwp_display_nonce'] ) )
			return $post_id;		
		if ( !wp_verify_nonce( $_POST['adsforwp_display_nonce'], 'adsforwp_display_data' ) )
			return $post_id;
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return $post_id;
                
                $ad_margin = array();                     
                $ad_margin = array_map('sanitize_text_field', $_POST['adsforwp_ad_margin']);                      
                update_post_meta($post_id, 'adsforwp_ad_margin', $ad_margin);
                
		foreach ( $this->meta_fields as $meta_field ) {
                    if($meta_field['id'] != 'adsforwp_ad_margin'){ 
			if ( isset( $_POST[ $meta_field['id'] ] ) ) {
				switch ( $meta_field['type'] ) {
					case 'email':
						$_POST[ $meta_field['id'] ] = sanitize_email( $_POST[ $meta_field['id'] ] );
						break;
					case 'text':
						$_POST[ $meta_field['id'] ] = sanitize_text_field( esc_html($_POST[ $meta_field['id'] ]));
						break;
				}
				update_post_meta( $post_id, $meta_field['id'], $_POST[ $meta_field['id'] ] );
			} else if ( $meta_field['type'] === 'checkbox' ) {
				update_post_meta( $post_id, $meta_field['id'], '0' );
			}
                    }
		}  
                
	}
}
if (class_exists('adsforwp_view_display')) {
	new adsforwp_view_display;
};