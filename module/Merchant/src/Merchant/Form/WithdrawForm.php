<?php
namespace Merchant\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Merchant\Model\Withdraw;

class WithdrawForm extends Form
{

    function __construct()
    {
        parent::__construct('withdraw');
        
        $this->add(array(
            'name' => 'withdraw_type',
            'type' => 'Select',
            'options' => array(
                'label' => 'Which is your WithdrawType ?',
                'value_options' => array(
                    Withdraw::WITHDRAW_TYPE_ALIPAY => 'Alipay',
                    Withdraw::WITHDRAW_TYPE_BANK => 'BankCard'
                ),
            )
        ));
        
        $this->add(array(
            'name' => 'price',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
        ));
        
        
        /**
         * Setting up inputFilter
        */
        $input_WithdrawType = new Input('withdraw_type');
        $input_WithdrawType->getFilterChain()->attach(new \Zend\Filter\Whitelist(array(
                                                                                        'list' => array(
                                                                                            Withdraw::WITHDRAW_TYPE_ALIPAY,
                                                                                            Withdraw::WITHDRAW_TYPE_BANK
                                                                                        )
                                                                                    )));
        
        $input_Price = new Input('price');
        $input_Price->getValidatorChain()->attach( new \Zend\I18n\Validator\IsFloat() );
        
        $input_filter = new InputFilter();
        $input_filter->add($input_WithdrawType);
        $input_filter->add($input_Price);
        
        $this->setInputFilter($input_filter);
    }
}

?>