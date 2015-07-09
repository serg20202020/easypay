<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Cashier\Controller\Index' => 'Cashier\Controller\IndexController',
            'Cashier\Controller\Getmerchantid' => 'Cashier\Controller\GetMerchantIDController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'cashier' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/cashier',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Cashier\Controller',
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
                    'setpayment' => array(
                        'type'    => 'Literal',
                        'options' => array(
                            // Change this to something specific to your module
                            'route'    => '/setpayment',
                            'defaults' => array(
                                // Change this value to reflect the namespace in which
                                // the controllers for your module are found
                                '__NAMESPACE__' => 'Cashier\Controller',
                                'controller'    => 'Index',
                                'action'        => 'setpayment',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'Cashier' => __DIR__ . '/../view',
        ),
    ),
);
