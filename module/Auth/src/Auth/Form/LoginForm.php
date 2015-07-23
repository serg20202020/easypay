<?php
namespace Auth\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class LoginForm extends Form
{

    function __construct()
    {
        parent::__construct('login');
    
        $this->add(array(
            'name' => 'username',
            'type' => 'Text',
        ));
        $this->add(array(
            'name' => 'password',
            'type' => 'Password',
        ));

        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
        ));
    
    
        /**
         * Setting up inputFilter
        */
        $input_Username = new Input('username');
        $input_Username->getFilterChain()->attach(new \Zend\Filter\Whitelist(array(
            'list' => array('Administrator','Staff')
        )));
        
        $input_Password = new Input('password');
        $input_Password->getFilterChain()->attach(new \Zend\I18n\Filter\Alnum());
        $input_Password->getValidatorChain()->attach(new \Zend\Validator\StringLength(array('min'=>12,'max' => 32)));
        
        $input_filter = new InputFilter();
        $input_filter->add($input_Username);
        $input_filter->add($input_Password);
        
        $this->setInputFilter($input_filter);
    
    }
}

?>