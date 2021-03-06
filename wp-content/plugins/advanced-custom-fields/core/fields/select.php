<?php

class acf_Select extends acf_Field
{

	/*--------------------------------------------------------------------------------------
	*
	*	Constructor
	*
	*	@author Elliot Condon
	*	@since 1.0.0
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function __construct($parent)
	{
    	parent::__construct($parent);
    	
    	$this->name = 'select';
		$this->title = __("Select",'acf');
		
   	}
	

	
	/*--------------------------------------------------------------------------------------
	*
	*	create_field
	*
	*	@author Elliot Condon
	*	@since 2.0.5
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_field($field)
	{
		// vars
		$defaults = array(
			'value'			=>	array(),
			'multiple' 		=>	'0',
			'allow_null' 	=>	'0',
			'choices'		=>	array(),
			'optgroup'		=>	false,
		);
		
		$field = array_merge($defaults, $field);

		
		// no choices
		if(empty($field['choices']))
		{
			echo '<p>' . __("No choices to choose from",'acf') . '</p>';
			return false;
		}
		
		
		// multiple select
		$multiple = '';
		if($field['multiple'] == '1')
		{
			// create a hidden field to allow for no selections
			echo '<input type="hidden" name="' . $field['name'] . '" />';
			
			$multiple = ' multiple="multiple" size="5" ';
			$field['name'] .= '[]';
		} 
		
		
		// html
		echo '<select id="' . $field['name'] . '" class="' . $field['class'] . '" name="' . $field['name'] . '" ' . $multiple . ' >';	
		
		
		// null
		if($field['allow_null'] == '1')
		{
			echo '<option value="null"> - Select - </option>';
		}
		
		global $recorded_type2;
    	if ( $recorded_type2 != 'inspirasi' AND $_GET['post_type'] != 'inspirasi' AND $recorded_type2 != 'dokumen-gereja' AND $_GET['post_type'] != 'dokumen-gereja') 
    	{
			// loop through values and add them as options
			foreach($field['choices'] as $key => $value)
			{
				if($field['optgroup'])
				{
					// this select is grouped with optgroup
					if($key != '') echo '<optgroup label="'.$key.'">';
					
					if($value)
					{
						foreach($value as $id => $label)
						{
							$selected = '';
							if(is_array($field['value']) && in_array($id, $field['value']))
							{
								// 2. If the value is an array (multiple select), loop through values and check if it is selected
								$selected = 'selected="selected"';
							}
							else
							{
								// 3. this is not a multiple select, just check normaly
								if($id == $field['value'])
								{
									$selected = 'selected="selected"';
								}
							}	
							echo '<option value="'.$id.'" '.$selected.'>'.$label.'</option>';
						}
					}
					
					if($key != '') echo '</optgroup>';
				}
				$selected = '';
				if(is_array($field['value']) && in_array($key, $field['value']))
				{
					// 2. If the value is an array (multiple select), loop through values and check if it is selected
					$selected = 'selected="selected"';
				}
				else
				{
					// 3. this is not a multiple select, just check normaly
					if($key == $field['value'])
					{
						$selected = 'selected="selected"';
					}
				}	
				echo '<option value="'.$key.'" '.$selected.'>'.$value.'</option>';
			}	
		}
		else if ( $recorded_type2 == 'inspirasi' OR $_GET['post_type'] == 'inspirasi') 
		{
			$selected = '';
			$kunci = '';
			global $wpdb;
			$inspirators = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"inspirator\" AND post_status = \"publish\"");
			foreach ($inspirators as $inspirator)
			{
				if($inspirator->ID == $field['value'])
				{
					$selected = 'selected="selected"';
				}
				else
				{
					$selected = '';
				}
				echo '<option value="' . $inspirator->ID . '" ' . $selected . '>'. get_the_title($inspirator->ID) .'</option>';
			}
		}
		else
		{
			$selected = '';
			$kunci = '';
			global $wpdb;
			$tipes = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE post_type = \"tipe-dokumen-gereja\" AND post_status = \"publish\"");
			
			foreach ($tipes as $tipe)
			{
				if($tipe->ID == $field['value'])
				{
					$selected = 'selected="selected"';
				}
				else
				{
					$selected = '';
				}
				echo '<option value="' . $tipe->ID . '" ' . $selected . '>'. get_the_title($tipe->ID) .'</option>';
			}
		}
		echo '</select>';
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	create_options
	*
	*	@author Elliot Condon
	*	@since 2.0.6
	*	@updated 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function create_options($key, $field)
	{	
		// defaults
		$field['multiple'] = isset($field['multiple']) ? $field['multiple'] : '0';
		$field['allow_null'] = isset($field['allow_null']) ? $field['allow_null'] : '0';
		$field['default_value'] = isset($field['default_value']) ? $field['default_value'] : '';
		
		// implode selects so they work in a textarea
		if(isset($field['choices']) && is_array($field['choices']))
		{		
			foreach($field['choices'] as $choice_key => $choice_val)
			{
				$field['choices'][$choice_key] = $choice_key.' : '.$choice_val;
			}
			$field['choices'] = implode("\n", $field['choices']);
		}
		else
		{
			$field['choices'] = "";
		}

		?>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label for=""><?php _e("Choices",'acf'); ?></label>
				<p class="description"><?php _e("Enter your choices one per line",'acf'); ?><br />
				<br />
				<?php _e("Red",'acf'); ?><br />
				<?php _e("Blue",'acf'); ?><br />
				<br />
				<?php _e("red : Red",'acf'); ?><br />
				<?php _e("blue : Blue",'acf'); ?><br />
				</p>
			</td>
			<td>
				<textarea rows="5" name="fields[<?php echo $key; ?>][choices]" id=""><?php echo $field['choices']; ?></textarea>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Default Value",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'text',
					'name'	=>	'fields['.$key.'][default_value]',
					'value'	=>	$field['default_value'],
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Allow Null?",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][allow_null]',
					'value'	=>	$field['allow_null'],
					'choices'	=>	array(
						'1'	=>	__("Yes",'acf'),
						'0'	=>	__("No",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>
		<tr class="field_option field_option_<?php echo $this->name; ?>">
			<td class="label">
				<label><?php _e("Select multiple values?",'acf'); ?></label>
			</td>
			<td>
				<?php 
				$this->parent->create_field(array(
					'type'	=>	'radio',
					'name'	=>	'fields['.$key.'][multiple]',
					'value'	=>	$field['multiple'],
					'choices'	=>	array(
						'1'	=>	__("Yes",'acf'),
						'0'	=>	__("No",'acf'),
					),
					'layout'	=>	'horizontal',
				));
				?>
			</td>
		</tr>

		<?php
	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	pre_save_field
	*	- called just before saving the field to the database.
	*
	*	@author Elliot Condon
	*	@since 2.2.0
	* 
	*-------------------------------------------------------------------------------------*/
	
	function pre_save_field($field)
	{
		// defaults
		$field['choices'] = isset($field['choices']) ? $field['choices'] : '';
		
		// vars
		$new_choices = array();
		
		// explode choices from each line
		if(strpos($field['choices'], "\n") !== false)
		{
			// found multiple lines, explode it
			$field['choices'] = explode("\n", $field['choices']);
		}
		else
		{
			// no multiple lines! 
			$field['choices'] = array($field['choices']);
		}
		
		// key => value
		foreach($field['choices'] as $choice)
		{
			if(strpos($choice, ' : ') !== false)
			{
				$choice = explode(' : ', $choice);
				$new_choices[trim($choice[0])] = trim($choice[1]);
			}
			else
			{
				$new_choices[trim($choice)] = trim($choice);
			}
		}
		
		// update choices
		$field['choices'] = $new_choices;
		
		// return updated field
		return $field;

	}
	
	
	/*--------------------------------------------------------------------------------------
	*
	*	get_value_for_api
	*
	*	@author Elliot Condon
	*	@since 3.1.2
	* 
	*-------------------------------------------------------------------------------------*/
	
	function get_value_for_api($post_id, $field)
	{
		$value = parent::get_value($post_id, $field);
		
		if($value == 'null')
		{
			$value = false;
		}
		
		return $value;
	}
	
}

?>