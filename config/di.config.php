<?php
return array(
    'di' => array(
        'definition' => array(
            'class' => array(
                'Zend\View\Resolver\AggregateResolver' => array(
                    'attach' => array(
                        array('type' => 'Zend\View\Resolver\TemplatePathStack')
                    )
                )
            )
        ),

        'instance' => array(
            'Zend\View\Resolver\AggregateResolver' => array(
                'injections' => array(
                    'Zend\View\Resolver\TemplatePathStack',
                ),
            ),

            'Zend\View\Resolver\TemplatePathStack' => array(
                'parameters' => array(
                    'paths'  => array(
                        'zedo' => __DIR__ . '/../view',
                    ),
                ),
            ),

            'Zend\View\Renderer\PhpRenderer' => array(
                'parameters' => array(
                    'resolver' => 'Zend\View\Resolver\AggregateResolver',
                ),
            ),

            'ZeDoGenerator\Service\Generators\ModuleClassGenerator' => array(
                'parameters' => array(
                    'renderer' => 'Zend\View\Renderer\PhpRenderer',
                ),
            ),

            'ZeDoGenerator\Service\Generators\ViewGenerator' => array(
                'parameters' => array(
                    'renderer' => 'Zend\View\Renderer\PhpRenderer',
                ),
            ),

            'ZeDoGenerator\Service\Generators\FormClassGenerator' => array(
                'parameters' => array(
                    'renderer' => 'Zend\View\Renderer\PhpRenderer',
                ),
            ),

            'ZeDoGenerator\Service\Generators\ControllerClassGenerator' => array(
                'parameters' => array(
                    'renderer' => 'Zend\View\Renderer\PhpRenderer',
                ),
            ),

            'ZeDoGenerator\Service\Generators\ZeDoMoGenerator' => array(
                'parameters' => array(
                    'renderer' => 'Zend\View\Renderer\PhpRenderer',
                ),
            ),

        ),
    ),
);