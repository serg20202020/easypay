<?php
namespace Merchant\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class WithdrawForm extends Form
{

    function __construct()
    {
        $this->add(array(
            'name' => 'withdraw_type',
            'type' => 'Select',
            'options' => array(
                'label' => 'Which is your WithdrawType ?',
                'value_options' => array(
                    'alipay' => 'Alipay',
                    'bank' => 'BankCard'
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
        $input_AlipayAccount = new Input('alipay_account');
        $input_AlipayAccount->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        $input_AlipayAccount->getValidatorChain()->attach( new \Zend\Validator\EmailAddress() );
        
        $input_BankType = new Input('bank_type');
        $input_BankType->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        
        $input_BankAccountName = new Input('bank_account_name');
        $input_BankAccountName->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        
        $input_BankAccountCard = new Input('bank_account_card');
        $input_BankAccountCard->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        
        $input_filter = new InputFilter();
        $input_filter->add($input_AlipayAccount);
        $input_filter->add($input_BankType);
        $input_filter->add($input_BankAccountName);
        $input_filter->add($input_BankAccountCard);
        
        $this->setInputFilter($input_filter);
    }
}

?>