<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Merchant\Controller\Index' => 'Merchant\Controller\IndexController',
            'Merchant\Controller\Setting' => 'Merchant\Controller\SettingController',
            'Merchant\Controller\Trade' => 'Merchant\Controller\TradeController',
            'Merchant\Controller\Withdraw' => 'Merchant\Controller\WithdrawController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'merchant' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/merchant',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Merchant\Controller',
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
                    'setting' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            // Change this to something specific to your module
                            'route'    => '/setting',
                            'defaults' => array(
                                // Change this value to reflect the namespace in which
                                // the controllers for your module are found
                                '__NAMESPACE__' => 'Merchant\Controller',
                                'controller'    => 'Setting',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'withdraw' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            // Change this to something specific to your module
                            'route'    => '/withdraw[/:action]',
                            'defaults' => array(
                                // Change this value to reflect the namespace in which
                                // the controllers for your module are found
                                '__NAMESPACE__' => 'Merchant\Controller',
                                'controller'    => 'Withdraw',
                                'action'        => 'index',
                            ),
                        ),
                    ),
                    'withdraw_paginator' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            // Change this to something specific to your module
                            'route'    => '/withdraw[/page/:page]',
                            'defaults' => array(
                                // Change this value to reflect the namespace in which
                                // the controllers for your module are found
                                '__NAMESPACE__' => 'Merchant\Controller',
                                'controller'    => 'Withdraw',
                                'action'        => 'index',
                                'page'          => 1,
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'merchant/common/menu'           => __DIR__ . '/../view/common/menu.phtml',
            
        ),
        'template_path_stack' => array(
            'Merchant' => __DIR__ . '/../view',
        ),
    ),
);
