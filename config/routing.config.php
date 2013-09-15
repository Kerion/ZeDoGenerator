<?php
return array(
    'console' => array(
        'router' => array(
            'routes' => array(
                'createmodel' => array(
                    'options' => array(
                        'route'    => 'create model [--all|-a|]:all [--entityName=] [--namespace=] [--dir=]',
                        'defaults' => array(
                            'controller' => 'ZeDoGenerator\Controller\ZeDoGenerator',
                            'action'     => 'consoleEntity',
                        )
                    )
                ),

                'createform' => array(
                    'options' => array(
                        'route'    => 'create form [--all|-a|]:all [--entityName=] [--namespace=] [--dir=]',
                        'defaults' => array(
                            'controller' => 'ZeDoGenerator\Controller\Form',
                            'action'     => 'consoleEntity',
                        )
                    )
                ),

                'createmodule' => array(
                    'options' => array(
                        'route'    => 'create module [--moduleName=] [--tableName=] [--entityManager=]',
                        'defaults' => array(
                            'controller' => 'ZeDoGenerator\Controller\ModuleCreator',
                            'action'     => 'consoleModule',
                        ),
                    ),
                ),
            ),
        ),
    ),
);