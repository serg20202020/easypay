<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Application\Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Authentication\AuthenticationService;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
        
        $auth = new AuthenticationService();
        
        
        if ($auth->hasIdentity()) {
        
            $identity = $auth->getIdentity();
            echo $identity;
            print_r($_SESSION);
        
        }else{
            echo '没有登录';
        }
    }

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Zend\Permissions\Acl\Acl' => function ($sm) {
                    
                    $acl = new Acl();
                    
                    $guest = new Role\Guest();
                    $client = new Role\Client();
                    $merchant = new Role\Merchant();
                    $staff = new Role\Staff();
                    $adminitrator = new Role\Adminitrator();
                    
                    $acl->addRole($guest)
                        ->addRole($client)
                        ->addRole($merchant)
                        ->addRole($staff)
                        ->addRole($adminitrator);
                    
                    
                    $SettingControllerIndex = new Resource('Setting\Controller\IndexController');
                    $acl->addResource($SettingControllerIndex);
                    
                    $acl->allow($guest,$SettingControllerIndex,'index');
                    
                    return $acl;
                },
                'Acl' => function ($sm) {
                    return function ($resouce,$privilege=null) use($sm){
                        $acl = $sm->get('Zend\Permissions\Acl\Acl');
                        $role = $sm->get('GetCurrentRole');
                        
                        $allow = $acl->isAllowed($role, $resouce, $privilege );
                        
                        if ($allow) return true;
                        else{
                            throw new Role\NoPermissionsException();
                            
                        }
                    };
                },
                'GetCurrentRole' => function(){
                    
                    $role = new Role\Guest();
                    
                    $auth = new AuthenticationService();
                    if ($auth->hasIdentity()) {
                        
                        if ($auth->getIdentity() === 'Administrator') $role = new Role\Adminitrator();
                        elseif ($auth->getIdentity() === 'Staff') $role = new Role\Staff();
                        
                    }
                    
                    return $role;
                }
            ),
        );
    }
}
