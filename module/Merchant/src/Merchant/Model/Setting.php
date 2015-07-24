<?php
namespace Merchant\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceLocatorInterface;

class Setting
{
    public $id;
    public $name;
    public $alipay_account;
    public $bank_type;
    public $bank_account_name;
    public $bank_account_card;
    
    private $tableGateway;

    function __construct( $merchant_id, ServiceLocatorInterface $sl)
    {
        $dbAdapter = $sl->get('Zend\Db\Adapter\Adapter');
        
        $this->tableGateway = new TableGateway('merchant',$dbAdapter);
        $rs = $this->tableGateway->select(array('id'=>$merchant_id));
        
        if ($rs->count() > 0){
            $data = $rs->current();
            $this->id                   = $data['id'];
            $this->name                 = $data['name'];
            $this->alipay_account       = $data['alipay_account'];
            $this->bank_type            = $data['bank_type'];
            $this->bank_account_name    = $data['bank_account_name'];
            $this->bank_account_card    = $data['bank_account_card'];
        }else{
            $this->id                   = 
            $this->name                 = 
            $this->alipay_account       = 
            $this->bank_type            = 
            $this->bank_account_name    = 
            $this->bank_account_card    = '';
        }
    }
    
    public function save(){
    
        
        
        $rs = $this->tableGateway->select(array('id'=>$this->id));
        if ($rs->count() > 0){
            // There is a recornd, update it
            $this->tableGateway->update(array(
                'name'=>$this->name,
                'alipay_account'=>$this->alipay_account,
                'bank_type'=>$this->bank_type,
                'bank_account_name'=>$this->bank_account_name,
                'bank_account_card'=>$this->bank_account_card
            ),array('id'=>$this->id));
        }else{
            // No record, insert one
            $this->tableGateway->insert(array(
                'name'=>$this->name,
                'alipay_account'=>$this->alipay_account,
                'bank_type'=>$this->bank_type,
                'bank_account_name'=>$this->bank_account_name,
                'bank_account_card'=>$this->bank_account_card
            ));
        }
    }
    
    public function exchangeArray($data){
        
        $this->id                   = (!empty($data['id'])) ? $data['id'] : $this->id;
        $this->name                 = (!empty($data['name'])) ? $data['name'] : $this->name ;
        $this->alipay_account       = (!empty($data['alipay_account'])) ? $data['alipay_account'] : $this->alipay_account;
        $this->bank_type            = (!empty($data['bank_type'])) ? $data['bank_type'] : $this->bank_type;
        $this->bank_account_name    = (!empty($data['bank_account_name'])) ? $data['bank_account_name'] : $this->bank_account_name;
        $this->bank_account_card    = (!empty($data['bank_account_card'])) ? $data['bank_account_card'] : $this->bank_account_card;

    }
    
    public function getArrayCopy() {
        return array(
            'id'=>$this->id,
            'name'=>$this->name,
            'alipay_account'=>$this->alipay_account,
            'bank_type'=>$this->bank_type,
            'bank_account_name'=>$this->bank_account_name,
            'bank_account_card'=>$this->bank_account_card
        );
    }
}

?>