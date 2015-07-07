<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Setting\Controller\Index' => 'Setting\Controller\IndexController',
            'Setting\Controller\Payment' => 'Setting\Controller\PaymentController',
            'Setting\Controller\Apikey' => 'Setting\Controller\APIKeyController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'setting' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/setting',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Setting\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    // This route is a sane default when developing a module;
                    // as you solidify the routes for your module, however,
                    // you may want to remove it and replace it with more
                    // specific routes.
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'setting/common/menu'           => __DIR__ . '/../view/common/menu.phtml',
            'setting/common/payment-menu'           => __DIR__ . '/../view/common/payment-menu.phtml'
        ),
        'template_path_stack' => array(
            'Setting' => __DIR__ . '/../view',
        ),
    ),
);
