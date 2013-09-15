<?php
namespace ZeDoGenerator;

use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;

use Zend\View\Resolver;

class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{

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
	
	public function getConfig()
	{
        return array_merge(include __DIR__ . '/config/module.config.php',
                           include __DIR__ . '/config/routing.config.php',
                           include __DIR__ . '/config/di.config.php'
        );
	}
	
    public function getConsoleBanner(Console $console)
    {
        return 'ZeDoModel-Creator 0.0.2';
    }
	
    public function getConsoleUsage(Console $console){

        return array(
            // Describe available commands
            'create model ([--all|-a]|[--entityName=] [--entityName=] [--namespace=] [--dir=])'    => 'Create Entity Classes',
            'create form ([--all|-a]|[--entityName=] [--entityName=] [--namespace=] [--dir=])'    => 'Create Form Classes',
            'create module [--moduleName=] [--tableName=] [--entityManager=]'    => 'Create a whole module',

            // Describe expected parameters
            array('--all|-a',		'Create all Entitys of the Database'),
            array('--entityName=',	'The Name of the Entity/Table to be created'),
            array('--namespace=',	'The Namespace to be used'),
            array('--dir=',	        'The Directory where the files shold been droped'),
        );
    }
}
