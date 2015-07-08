<?php
namespace Cashier\Model;

use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;

class Merchant
{
    private $adapter;
    private $tableGateway;

    function __construct( Adapter $adapter )
    {
        
        $this->adapter = $adapter;
        $this->tableGateway = new TableGateway('merchant', $this->adapter);
    }
    
    public function getID( $name ) {
        
        $rs = $this->tableGateway->select(array('name'=>$name));
        
        if ($rs->count() > 0){
            $data = $rs->current();
            return $data['id'];
        }else{
            // there is no record, insert one
            $id = $this->tableGateway->insert(array(
                'name'=>$name
            ));
            
            if ($id != 1) throw new \Exception('Can\'t make new data.');
            else{
                return $this->tableGateway->getLastInsertValue();
            }
        }
        
    }
}

?>