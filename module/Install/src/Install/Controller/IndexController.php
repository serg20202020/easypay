<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Install for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Install\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Install\Form\InstallForm;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\Exception\RuntimeException as AdapterRuntimeException;
use Install\Model\DB;


class IndexController extends AbstractActionController
{
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
                    'driver' => 'Mysqli',
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

    public function fooAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /index/index/foo
        return array();
    }
}
