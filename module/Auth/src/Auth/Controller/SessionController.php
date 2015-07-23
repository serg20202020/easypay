<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Auth for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Auth\Form\LoginForm;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Authentication\AuthenticationService;

class SessionController extends AbstractActionController
{
    public function indexAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('System Login'));
        
        
        $form = new LoginForm();
        $vars = array();
        
        $auth = new AuthenticationService();
        if (!$auth->hasIdentity()){
            
            $vars['form']= $form;
            
            $request = $this->getRequest();
            if ($request->isPost()) {
            
                $post_data = $request->getPost();
            
                $form->setData($post_data);
            
                // Validate the form
                if ($form->isValid()) {
                    // Authentication ...
            
                    $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            
                    // Configure the instance with constructor parameters...
                    $authAdapter = new AuthAdapter($dbAdapter,
                        'account',
                        'username',
                        'password',
                        'MD5(?)'
                    );
            
                    // Set the input credential values (e.g., from a login form)
                    $data = $form->getData();
                    $authAdapter
                        ->setIdentity($data['username'])
                        ->setCredential($data['password'])
                    ;
            
                    $auth = new AuthenticationService();
                    $result = $auth->authenticate($authAdapter);
                    $vars['result'] = $result;
            
                    if (!$result->isValid()) {
                        // Authentication failed;
            
                    } else {
                        // Authentication succeeded; the identity ($username) is stored
                        // in the session
                        // $result->getIdentity() === $auth->getIdentity()
                        // $result->getIdentity() === $username
                        return $this->redirect()->toRoute('auth');
                    }
                }
            }
            
        }
        
        

        
        
        $view_page = new ViewModel($vars);
        
        return $view_page;
    }

    public function logoutAction()
    {
        $auth = new AuthenticationService();
        
        $auth->clearIdentity();
        
        return array('status'=>$auth->hasIdentity());
    }
}
