<?php
return array(
	'controllers' => array(
		'invokables' => array(
            'ZeDoGenerator\Controller\ZeDoGenerator' => 'ZeDoGenerator\Controller\CreationController\ZedomodelCreationController',
            'ZeDoGenerator\Controller\Form'          => 'ZeDoGenerator\Controller\CreationController\FormCreationController',
			'ZeDoGenerator\Controller\ModuleCreator' => 'ZeDoGenerator\Controller\CreationController\ModuleCreationController',
		)
	),

);