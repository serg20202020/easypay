<?php
namespace Setting\Model;

use Zend\Db\TableGateway\TableGateway;

class PaymentInterface
{
    const PAYMENT_TYPE_ALIPAY = 'alipay';
    const PAYMENT_TYPE_WXPAY  = 'wxpay';
    
    public $name;
    public $merchant_id;
    public $api_key;
    public $account;
    public $option;
    
    private $tableGateway;
    
    function __construct( $payment_type, $sl )
    {
        if ($payment_type !== self::PAYMENT_TYPE_ALIPAY && $payment_type !== self::PAYMENT_TYPE_WXPAY) throw new \Exception('Error PaymentType!');
        
        $this->name = $payment_type;
        
        // Read setting data from DB
        $dbAdapter = $sl->get('Zend\Db\Adapter\Adapter');
        
        $this->tableGateway = new TableGateway('payment_interface',$dbAdapter);
        $rs = $this->tableGateway->select(array('name'=>$this->name));
        
        if ($rs->count() > 0){
            $data = $rs->current();
            $this->merchant_id = $data['merchant_id'];
            $this->api_key    = $data['api_key'];
            $this->account    = $data['account'];
            $this->option     = $data['option'];
        }else{
            $this->merchant_id = '';
            $this->api_key    = '';
            $this->account    = '';
            $this->option     = '';
        }
    }

    public function save(){
        
        $rs = $this->tableGateway->select(array('name'=>$this->name));
        if ($rs->count() > 0){
            // There is a recornd, update it
            $this->tableGateway->update(array(
                'merchant_id' => $this->merchant_id,
                'api_key'     => $this->api_key,
                'account'     => $this->account,
                'option'      => $this->option,
            ),array('name'=>$this->name));
        }else{
            // No record, insert one
            $this->tableGateway->insert(array(
                'name'       => $this->name,
                'merchant_id'=> $this->merchant_id,
                'api_key'    => $this->api_key,
                'account'    => $this->account,
                'option'     => $this->option,
            ));
        }
    }
    
    public function exchangeArray($data){
        $this->merchant_id      = (!empty($data['merchant_id'])) ? $data['merchant_id'] : '';
        $this->api_key          = (!empty($data['api_key'])) ? $data['api_key'] : '';
        $this->account          = (!empty($data['account'])) ? $data['account'] : '';
        $this->option           = (!empty($data['option'])) ? $data['option'] : '';
    }
    
    public function getArrayCopy() {
        return array(
                'name'       => $this->name,
                'merchant_id'=> $this->merchant_id,
                'api_key'    => $this->api_key,
                'account'    => $this->account,
                'option'     => $this->option,
                );
    }
}

?>