<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;

/**
 * AclController
 *
 * @author
 *
 * @version
 *
 */
class AclController extends AbstractActionController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e){
        $Acl = $this->getServiceLocator()->get('Acl');
        $Acl( $this->AclResourceName );
        return parent::onDispatch($e);
    }
}