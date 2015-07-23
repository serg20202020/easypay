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
use Zend\Config\Config;

class Module
{
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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
                    
                    /**
                     * Define Roles
                     */
                    $acl->addRole($guest)
                        ->addRole($client)
                        ->addRole($merchant)
                        ->addRole($staff)
                        ->addRole($adminitrator);
                    
                    /**
                     * Define Resources
                     */
                    $ControllerResource = array();
                    array_push($ControllerResource, new Resource('Setting\Controller\BaseSettingController'));
                    array_push($ControllerResource, new Resource('Install\Controller\IndexController'));
                    array_push($ControllerResource, new Resource('Workbench\Controller\BaseController'));
                    
                    foreach ($ControllerResource as $resource){
                        $acl->addResource($resource);
                    }
                    
                    /**
                     * Assigning permissions
                     */
                    $acl->allow($adminitrator,'Setting\Controller\BaseSettingController');
                    $acl->allow($adminitrator,'Install\Controller\IndexController');
                    $acl->allow($adminitrator,'Workbench\Controller\BaseController');
                    
                    $acl->allow($staff,'Workbench\Controller\BaseController');
                    
                    if (!$sm->get('SiteIsInstalled')) $acl->allow($guest,'Install\Controller\IndexController');
                    
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
                },
                'SiteIsInstalled'=> function (){
                    $config_file = 'config/autoload/local.php';
                    if (file_exists($config_file)) $config = include $config_file;
                    
                    $reader = new Config($config);
                    if (!empty($reader->db)) return true;
                    else return false;
                }
                
            ),
        );
    }
}
