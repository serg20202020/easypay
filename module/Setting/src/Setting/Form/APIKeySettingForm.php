<?php
namespace Setting\Form;

use Zend\Form\Form;

class APIKeySettingForm extends Form
{

    function __construct()
    {
        parent::__construct('api_key_setting');
        
        $this->add(array(
            'name' => 'key',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
        ));
    }
}

?>