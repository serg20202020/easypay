<?php
namespace Application\Model;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class DBModel
{
    private $data;
    
    private $tableGateway;
    private $id_column;
    private $id_value;

    function __construct( ServiceLocatorInterface $sl, $table, $id = null, $id_column = 'id')
    {
        $dbAdapter = $sl->get('Zend\Db\Adapter\Adapter');
        
        $this->id_column = $id_column;
        $this->id_value = $id;
        
        $this->tableGateway = new TableGateway($table,$dbAdapter);
        
        if (!empty($this->id_value)){
            
            $rs = $this->tableGateway->select(array($id_column=>$id));
            
            if ($rs->count() > 0){
                $data = $rs->current();
            
                foreach ($data as $k=>$v){
                    $this->addColumn($k, $v);
                }
            
            }
            
        }else{
            
            $metadata = new \Zend\Db\Metadata\Metadata($this->tableGateway->adapter);
            $columns = $metadata->getColumnNames($table);
            
            foreach ($columns as $column_name){
                $this->addColumn($column_name, null);
            }
            
        }
    }
    
    protected function addColumn($name,$value) {
        if (empty($this->data)) $this->data = array();
        $this->data[$name] = $value;
    }
    
    public function __get($name){
        if (empty($this->data[$name])) return null;
        else return $this->data[$name];
    }
    
    public function __set($name,$value){
        if (array_key_exists($name,$this->data)) $this->data[$name] = $value;
        else throw new \Exception('There is no column ['.$name.'] in model ['.get_class($this).']');
    }
    
    public function exchangeArray($data){
    
        foreach ($this->data as $name=>$value){
            
            $this->data[$name] = (!empty($data[$name])) ? $data[$name] : $this->data[$name];
            
        }
    
    }
    
    public function getArrayCopy() {
        return $this->data;
    }
    
    public function save(){

        $rs = $this->tableGateway->select(array($this->id_column=>$this->id_value));
        $data = $this->data;
        unset($data[$this->id_column]);
        
        if ($rs->count() > 0){
            
            // There is a recornd, update it
            $return = $this->tableGateway->update($data,array($this->id_column=>$this->id_value));
            
        }else{
            
            // No record, insert one
            $return = $this->tableGateway->insert($data);
            
        }
        
        return $return;
    }
}

?>