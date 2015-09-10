<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Install for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Install\Controller;

use Application\Controller\AclController;
use Install\Form\InstallForm;
use Zend\Db\Adapter\Exception\RuntimeException as AdapterRuntimeException;
use Install\Model\DB;

use Zend\Db\Sql\Ddl;
use Zend\Db\Sql\Ddl\Column;
use Zend\Db\Sql\Ddl\Constraint;
use Zend\Db\Sql\Sql;


class IndexController extends AclController
{
    public $AclResourceName = __CLASS__;
    
    public function indexAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        
        $headTitle->append($translator->translate('Installing The UserHub System'));
        
        $form = new InstallForm();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            
            $post_data = $request->getPost();
            
            $form->setData($post_data);
            
            // Test the account by form submited.
            try {
                
                if (empty($post_data->database)) throw new \Exception($translator->translate('Database name has not spacify!'));
                
                $db = new DB(array(
                    'driver' => 'Pdo_Mysql',
                    'hostname'=>$post_data->server,
                    'username' => $post_data->username,
                    'password' => $post_data->password,
                    'database' => $post_data->database
                ));
                $db->install();
                
            } catch (AdapterRuntimeException $e) {
                print($e->getMessage());
            }
            
        }
        
        return array('form'=>$form);
    }

    public function testAction()
    {
        $adapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $metadata = new \Zend\Db\Metadata\Metadata($adapter);
        $sql = new Sql($adapter);
        
        
        $test_table_name = 'test';
        
        /*
        $tablenames = $metadata->getTableNames();
        
        if (in_array($test_table_name, $tablenames)){
            // Delete [test]
            $DropTable = new Ddl\DropTable($test_table_name);
            $adapter->query(
                $sql->buildSqlString($DropTable,$adapter),
                $adapter::QUERY_MODE_EXECUTE
            );
        }
        
        
        // Create [test]
        $CreateTable = new Ddl\CreateTable($test_table_name);
        $CreateTable->addColumn(new Column\Integer('id',false,null,array('autoincrement'=>true)));
        $CreateTable->addColumn(new Column\Varchar('text', 100));
        $CreateTable->addColumn(new Column\Varchar('text2', 100));
        $CreateTable->addColumn(new Column\Varchar('text3', 100));
        $CreateTable->addConstraint(new Constraint\PrimaryKey('id'));
        $CreateTable->addConstraint(new Constraint\UniqueKey(array('text','text3'),'text_UniqueKey'));
        
        $adapter->query(
            $sql->buildSqlString($CreateTable,$adapter),
            $adapter::QUERY_MODE_EXECUTE
        );
        
        
        //print_r($metadata->getConstraints($test_table_name));
        
        //print_r($metadata->getConstraintKeys('PRIMARY', $test_table_name));
        //print_r($metadata->getConstraintKeys('text_UniqueKey', $test_table_name));
        //print_r($metadata->getConstraint('_zf_test_PRIMARY', $test_table_name));
        //print_r($metadata->getConstraint('_zf_test_text_UniqueKey', $test_table_name));
        
        
        $Constraints = $metadata->getConstraints($test_table_name);
        print_r($Constraints);
        
        
        $AlterTable = new Ddl\AlterTable($test_table_name);
        
        $AlterTable->addConstraint(new Constraint\UniqueKey(array('text','text3'),'text_UniqueKey2'));
        echo $sql->buildSqlString($AlterTable,$adapter);
        $adapter->query(
            $sql->buildSqlString($AlterTable,$adapter),
            $adapter::QUERY_MODE_EXECUTE
        );*/
        
        
        return array();
    }
}
