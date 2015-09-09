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
    public $AclResourceName = NULL;
    public $AclPrivilegeName = NULL;
    
    public function onDispatch(\Zend\Mvc\MvcEvent $e){
        $Acl = $this->getServiceLocator()->get('Acl');
        $Acl( $this->AclResourceName, $this->AclPrivilegeName );
        return parent::onDispatch($e);
    }
}