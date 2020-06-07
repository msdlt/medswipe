<?php  
//http://bakery.cakephp.org/articles/christian.kilb/2010/09/09/form-helper-enum-fields-to-select-boxes
App::uses('FormHelper', 'View/Helper');
class FormEnumHelper extends FormHelper 
{ 
    function input($fieldName, $options = array()) { 
        $this->setEntity($fieldName); 
         
        $modelKey = $this->model(); 
        $fieldKey = $this->field(); 

        if (!isset($this->fieldset[$modelKey]['fields'][$fieldKey])) { 
        	$this->_introspectModel($modelKey, 'fields', $fieldKey);
        } 

        if ((!isset($options['type']) || $options['type'] == 'select') && !isset($options['options'])) {
            if(isset($this->fieldset[$modelKey]['fields'][$fieldKey])) 
            { 
            	$type = $this->fieldset[$modelKey]['fields'][$fieldKey]['type']; 
                if(substr($type, 0, 4) == 'enum') 
                { 
                    $arr = explode('\'', $type); 
                    $enumValues = array(); 
                    foreach($arr as $value) 
                    { 
                        if($value != 'enum(' && $value != ',' && $value != ')') 
                            $options['options'][$value] = __($value, true); 
                    } 
                } 
            } 
        } 
         
        return parent::input($fieldName, $options); 
    } 
} 
?>
