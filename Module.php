<?php
namespace ZeDoGenerator;

use Zend\ModuleManager\Feature\ConsoleUsageProviderInterface;
use Zend\Console\Adapter\AdapterInterface as Console;
use Zend\ModuleManager\Feature\ConsoleBannerProviderInterface;

use Zend\View\Resolver;

/**
 * Class Module
 * @package ZeDoGenerator
 */
class Module implements ConsoleBannerProviderInterface, ConsoleUsageProviderInterface
{

    /**
     * @return array
     */
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

    /**
     * @return array
     */
    public function getConfig()
	{
        return array_merge(include __DIR__ . '/config/module.config.php',
                           include __DIR__ . '/config/routing.config.php',
                           include __DIR__ . '/config/di.config.php'
        );
	}

    /**
     * @param Console $console
     * @return string
     */
    public function getConsoleBanner(Console $console)
    {
        return 'ZeDoModel-Creator 0.0.2';
    }

    /**
     * @param Console $console
     * @return array
     */
    public function getConsoleUsage(Console $console){

        return array(
            // Describe available commands
            'create model ([--all|-a]|[--entityName=] [--entityName=] [--namespace=] [--dir=])'    => 'Create Entity Classes',
            'create form ([--all|-a]|[--entityName=] [--entityName=] [--namespace=] [--dir=])'    => 'Create Form Classes',
            'create module [--moduleName=] [--tableName=] [--entityManager=]'    => 'Create a whole module',

            // Describe expected parameters
            array('--all|-a',		'Create all Entitys of the Database'),
            array('--entityName=',	'The name of the Entity/Table to be created'),
            array('--namespace=',	'The namespace to be used'),
            array('--dir=',	        'The directory where the files should been droped'),
        );
    }
}
