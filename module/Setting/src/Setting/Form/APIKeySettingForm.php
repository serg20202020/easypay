<?php
namespace Setting\Form;

class APIKeySettingForm extends Form
{

    function __construct()
    {
        $this->add(array(
            'name' => 'key',
            'type' => 'Text',
        ));
    }
}

?>