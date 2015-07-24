<?php
namespace Merchant\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;

class SettingForm extends Form
{

    function __construct()
    {
        parent::__construct('merchant_setting');

        $this->add(array(
            'name' => 'alipay_account',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'name' => 'bank_type',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'name' => 'bank_account_name',
            'type' => 'Text',
        ));
         
        $this->add(array(
            'name' => 'bank_account_card',
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
        $input_AlipayAccount->setRequired(false);
        $input_AlipayAccount->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        $input_AlipayAccount->getValidatorChain()->attach( new \Zend\Validator\EmailAddress() );
        
        $input_BankType = new Input('bank_type');
        $input_BankType->setRequired(false);
        $input_BankType->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        
        $input_BankAccountName = new Input('bank_account_name');
        $input_BankAccountName->setRequired(false);
        $input_BankAccountName->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        
        $input_BankAccountCard = new Input('bank_account_card');
        $input_BankAccountCard->setRequired(false);
        $input_BankAccountCard->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        $input_BankAccountCard->getValidatorChain()->attach( new \Zend\Validator\Digits() );
        
        $input_filter = new InputFilter();
        $input_filter->add($input_AlipayAccount);
        $input_filter->add($input_BankType);
        $input_filter->add($input_BankAccountName);
        $input_filter->add($input_BankAccountCard);
        
        $this->setInputFilter($input_filter);
        
    }
}

?>