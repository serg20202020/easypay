<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'Workbench\Controller\Index' => 'Workbench\Controller\IndexController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'workbench' => array(
                'type'    => 'Literal',
                'options' => array(
                    // Change this to something specific to your module
                    'route'    => '/workbench',
                    'defaults' => array(
                        // Change this value to reflect the namespace in which
                        // the controllers for your module are found
                        '__NAMESPACE__' => 'Workbench\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'withdraw_paginator'=>array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/withdraw[/page/:page]',
                            'constraints' => array(
                                'page' => '[1-9][0-9]*'
                            ),
                            'defaults' => array(
                                'action'        => 'withdraw',
                                'page'=>1
                            ),
                        ),
                    ),
                    'withdraw_edit' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/withdraw/edit/:withdraw_id',
                            'constraints' => array(
                                'withdraw_id' => '[1-9][0-9]*'
                            ),
                            'defaults' => array(
                                'action'        => 'withdrawedit',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_map' => array(
            'workbench/common/menu'           => __DIR__ . '/../view/common/menu.phtml',
        
        ),
        'template_path_stack' => array(
            'Workbench' => __DIR__ . '/../view',
        ),
    ),
    'translator' => array(
        'locale' => 'zh_CN',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    
);
