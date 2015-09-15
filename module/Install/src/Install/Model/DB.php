<?php
/**
 * The class for maintain database structure.
 */
namespace Install\Model;
use Zend\Db\Metadata\Metadata;
use Zend\Db\Adapter\Adapter;
use Zend\Config\Config;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Ddl;
use Zend\Db\Sql\Ddl\Column;
use Zend\Db\Sql\Ddl\Constraint;


class DB extends Metadata
{
    public $config = NULL;
    
    private $tables = NULL;

    function __construct($config)
    {
        $this->config = $config;
        parent::__construct(new Adapter(array(
            'driver' => $config['driver'],
            'hostname'=>$config['hostname'],
            'username' => $config['username'],
            'password' => $config['password'],
        )));
        
    }
    
    /**
     * Where start to install database structure.
     */
    public function install(){
        
        $this->create_database();
        
        $this->create_tables();
        
        $this->insert_rows();
        
    }
    
    private function create_database() {

        // Try to create a database named by $post_data->database
        try {
        
            $createDatabse_stament = $this->adapter->createStatement('CREATE DATABASE IF NOT EXISTS `'.$this->config['database'].'` CHARACTER SET = utf8 COLLATE = utf8_general_ci');
            $rs = $createDatabse_stament->execute();
            
            parent::__construct(new Adapter($this->config));
            
            if ( $this->adapter->getCurrentSchema() == $this->config['database'] ){
                
                $this->tables = $this->getTableNames();
                
                $local_db_config = new Config(array('db'=>$this->config), true);
                $this->generateConfigFile($local_db_config);
                
            }
        
        } catch (\Exception $e) {
            throw $e;
        }
        
        
    }
    /**
     * Generate the config file config/autoload/local.php for database session
     * @param Config $local_db_config
     */
    private function generateConfigFile(Config $local_db_config){
        
        $config_file = 'config/autoload/local.php';
        
        // If there is a config file at that path, so merge the configuration to it.
        $config = array();
        if (file_exists($config_file)) $config = include 'config/autoload/local.php';
            
        $reader = new \Zend\Config\Config($config);
        $reader->merge($local_db_config);
        
        $writer = new \Zend\Config\Writer\PhpArray();
        $writer->toFile($config_file, $reader);
        
    }

    /**
     * Create one table. if it is exsist, check the columns. if the column is exsist, change it's type, else create the column.
     * @param unknown $tableName
     * @param array $table
     */
    private function create_table( $tableName, $tableStructureData) {
    
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
    
        if (!in_array($tableName, $this->tables)){
    
            // Create the $table
            $CreateTable = new Ddl\CreateTable($tableName);
            
            foreach ($tableStructureData['column'] as $column){
                $CreateTable->addColumn($column);
            }
            
            foreach ($tableStructureData['constraint'] as $constraint){
                $CreateTable->addConstraint($constraint);
            }
            
            $adapter->query(
                $sql->getSqlStringForSqlObject($CreateTable),
                $adapter::QUERY_MODE_EXECUTE
            );
    
        }else{
    
            // Check the columns
            $columns = $this->getColumns($tableName);
            $constraints = $this->getConstraints($tableName); 
            $AlterTable = new Ddl\AlterTable($tableName);
            
            foreach ($tableStructureData['column'] as $createColumn){
                
                $column_exsist = false;
                
                foreach ($columns as $column){
                    if ($createColumn->getName() == $column->getName()) $column_exsist = true;
                }
                
                
                if ($column_exsist) {
                    
                    // Alter the table, change the column.
                    $AlterTable->changeColumn($createColumn->getName(), $createColumn);
                    
                }else{
                    
                    // Alter the table, add the column.
                    $AlterTable->addColumn($createColumn);
                    
                }
                
            }
            
            
            // Delete exsisted constraints(mysql index) but PRIMARY KEY
            $exsisted_constraints = $this->getConstraints($tableName);
            
            foreach ($exsisted_constraints as $exsisted_constraint){
                if ($exsisted_constraint->getType() != 'PRIMARY KEY'){
            
                    $adapter->query(
                        'ALTER TABLE `'.$tableName.'`
                                 DROP index `'.str_replace('_zf_'.$tableName.'_', '', $exsisted_constraint->getName()).'`',
                        $adapter::QUERY_MODE_EXECUTE
                    );
            
                }
            }
            
            // Add all constraints but PRIMARY KEY
            foreach ($tableStructureData['constraint'] as $constraint){
                
                if ($constraint instanceof Constraint\PrimaryKey){
                    // Do nothing
                }else{
                    // Add to DB
                    $AlterTable->addConstraint($constraint);
                }

            }
            
            $adapter->query(
                $sql->getSqlStringForSqlObject($AlterTable),
                $adapter::QUERY_MODE_EXECUTE
            );
            
            
        }
    }
    
    /**
     * Compose the tables structure data, and create them by $this->create_table();
     */
    private function create_tables() {
        
        // Common columns
        $COLUMNS['id']              = new Column\Integer('id',FALSE,NULL,array('autoincrement'=>true));
        $COLUMNS['name']            = new Column\Varchar('name', 50);
        $COLUMNS['merchant_id']     = new Column\Integer('merchant_id',FALSE,NULL);
        $COLUMNS['price']           = new Column\Floating('price',11,2);
        $COLUMNS['pay_status']      = new Column\Integer('pay_status',FALSE,0);
        $COLUMNS['create_time']     = new Column\Datetime('create_time');
        $COLUMNS['pay_time']        = new Column\Datetime('pay_time');
        $COLUMNS['pay_time']->setNullable(true);
        
        // Common constraints
        $CONSTRAINTS['id_primarykey'] = new Constraint\PrimaryKey('id');
        
        
        /**
         * Create table [payment_interface].
         */
        $table_PaymentInterface['column'] = array(
            $COLUMNS['id'],
            $COLUMNS['name'],
            new Column\Varchar('merchant_id',50),
            new Column\Varchar('api_key', 100),
            new Column\Varchar('account', 50),
            new Column\Varchar('option', 100),
        );
        $table_PaymentInterface['column'][5]->setNullable(true);
        $table_PaymentInterface['constraint'] = array(
            $CONSTRAINTS['id_primarykey']
        );
        $this->create_table( 'payment_interface', $table_PaymentInterface);
        
        
        /**
         * Create table [trade].
         */
        $table_Trade['column'] = array(
            $COLUMNS['id'],
            $COLUMNS['merchant_id'],
            new Column\Varchar('merchant_trade_id', 100),
            new Column\Varchar('payment_interface_type', 50),
            new Column\Varchar('payment_interface_trade_id', 100),
            new Column\Varchar('redirect_url', 255),
            new Column\Varchar('notify_url', 255),
            $COLUMNS['price'],
            $COLUMNS['pay_status'],
            $COLUMNS['create_time'],
            $COLUMNS['pay_time']
        );
        $table_Trade['column'][3]->setNullable(true);
        $table_Trade['column'][4]->setNullable(true);
        $table_Trade['constraint'] = array(
            $CONSTRAINTS['id_primarykey']
        );
        $this->create_table( 'trade', $table_Trade);
        
        
        /**
         * Create table [withdraw].
         */
        $table_Withdraw['column'] = array(
            $COLUMNS['id'],
            $COLUMNS['merchant_id'],
            new Column\Varchar('withdraw_type', 50),
            new Column\Varchar('withdraw_interface_trade_id', 100),
            $COLUMNS['price'],
            $COLUMNS['pay_status'],
            $COLUMNS['create_time'],
            $COLUMNS['pay_time']
        );
        $table_Withdraw['column'][2]->setNullable(true);
        $table_Withdraw['column'][3]->setNullable(true);
        $table_Withdraw['constraint'] = array(
            $CONSTRAINTS['id_primarykey']
        );
        $this->create_table( 'withdraw', $table_Withdraw);
        
        
        /**
         * Create table [merchant].
         */
        $table_Merchant['column'] = array(
            $COLUMNS['id'],
            $COLUMNS['name'],
            new Column\Varchar('alipay_account',50),
            new Column\Varchar('bank_account_name', 50),
            new Column\Varchar('bank_account_card', 50),
            new Column\Varchar('bank_type', 50),
        );
        $table_Merchant['column'][2]->setNullable(true);
        $table_Merchant['column'][3]->setNullable(true);
        $table_Merchant['column'][4]->setNullable(true);
        $table_Merchant['column'][5]->setNullable(true);
        $table_Merchant['constraint'] = array(
            $CONSTRAINTS['id_primarykey'],
            new Constraint\UniqueKey(array('name'),'name_UniqueKey')
        );
        $this->create_table( 'merchant', $table_Merchant);
        
        /**
         * Create table [account].
         */
        $table_Account['column'] = array(
            $COLUMNS['id'],
            new Column\Varchar('username',50),
            new Column\Varchar('password',50),
            new Column\Datetime('last_login_time')
        );
        $table_Account['column'][3]->setNullable(true);
        $table_Account['constraint'] = array(
            $CONSTRAINTS['id_primarykey']
        );
        $this->create_table( 'account', $table_Account);
        
        
    }
    
    /**
     * Insert the base data rows.
     */
    private function insert_rows() {
        
        $adapter = $this->adapter;
        $sql = new Sql($adapter);
        
        $validator = new \Zend\Validator\Db\RecordExists(
            array(
                'table'   => 'account',
                'field'   => 'username',
                'adapter' => $adapter
            )
        );
        
        $account_rows = array();
        
        if (!$validator->isValid('Administrator')) {
            array_push($account_rows,array(
                'username'=>'Administrator',
                'password'=>md5('Administrator')
            ));
        }
        
        if (!$validator->isValid('Staff')) {
            array_push($account_rows,array(
                'username'=>'Staff',
                'password'=>md5('StaffStaffStaff')
            ));
        }
        
        if (!empty($account_rows)){
            
            $insert = $sql->insert('account');
            $insert->columns(array('username','password'));
            
            foreach ($account_rows as $account_rows_row){
                
                $insert->values($account_rows_row);
                
                $statement = $sql->prepareStatementForSqlObject($insert);
                
                $results = $statement->execute();
                
                if ($results->getAffectedRows() != 1) throw new \Zend\Db\Exception\ErrorException();
                
            }
            
        }
        
        
    }
    

}

?>