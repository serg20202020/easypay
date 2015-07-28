<?php
namespace Workbench\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Input;
use Merchant\Model\Withdraw;

class WithdrawEditForm extends Form
{
    function __construct()
    {
        parent::__construct('withdraw_edit');
        
        $this->add(array(
            'name' => 'withdraw_interface_trade_id',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'pay_status',
            'options' => array(
                'value_options' => array(
                    Withdraw::WITHDRAW_PAY_STATUS_NO => 'No',
                    Withdraw::WITHDRAW_PAY_STATUS_YES => 'Yes',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'Submit',
        ));
        
        
        
        /**
         * Setting up inputFilter
         */
        $input_PayStatus = new Input('pay_status');
        $input_PayStatus->getFilterChain()->attach(new \Zend\Filter\Whitelist(array(
            'list' => array(
                Withdraw::WITHDRAW_PAY_STATUS_NO,
                Withdraw::WITHDRAW_PAY_STATUS_YES
            )
        )));
        
        $input_WithdrawInterfaceTradeId = new Input('withdraw_interface_trade_id');
        $input_WithdrawInterfaceTradeId->getValidatorChain()->attach( new \Zend\Validator\StringLength(array('max' => 50)) );
        
        $input_filter = new InputFilter();
        $input_filter->add($input_PayStatus);
        
        $this->setInputFilter($input_filter);
    }
}

?>