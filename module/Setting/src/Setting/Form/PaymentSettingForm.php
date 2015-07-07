<?php
namespace Setting\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Setting\Model\PaymentInterface;

class PaymentSettingForm extends Form
{
    
    function __construct($payment_type)
    {
        parent::__construct($payment_type);
        
        $this->add(array(
            'name' => 'name',
            'type' => 'Hidden',
            'attributes'=> array(
                'value'=>$payment_type,
            ),
        ));
        $this->add(array(
            'name' => 'merchant_id',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'name' => 'api_key',
            'type' => 'Text',
        ));
         
        $this->add(array(
            'name' => 'account',
            'type' => 'Text',
        ));

        $this->add(array(
            'name' => 'option',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
        ));
        
        
        /**
         * Setting up inputFilter
         */
        $input_Name = new Input('name');
        $input_Name->getFilterChain()->attach(new \Zend\Filter\Whitelist(array(
                                                                        'list' => array(PaymentInterface::PAYMENT_TYPE_ALIPAY,PaymentInterface::PAYMENT_TYPE_WXPAY)
                                                                            )));
        $input_filter = new InputFilter();
        $input_filter->add($input_Name);
        $this->setInputFilter($input_filter);
        
    }
}

?>