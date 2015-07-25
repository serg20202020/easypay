<?php
namespace Application\Controller;


/**
 * AclController
 *
 * @author
 *
 * @version
 *
 */
class AclController extends BaseController
{
    public function onDispatch(\Zend\Mvc\MvcEvent $e){
        $Acl = $this->getServiceLocator()->get('Acl');
        $Acl( $this->AclResourceName );
        return parent::onDispatch($e);
    }
}