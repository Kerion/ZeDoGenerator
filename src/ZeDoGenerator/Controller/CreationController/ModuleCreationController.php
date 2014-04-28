<?php
namespace ZeDoGenerator\Controller\CreationController;

use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Confirm;
use Zend\Config\Writer\PhpArray;
use Zend\Filter\Word\CamelCaseToSeparator;

use ZeDoGenerator\Controller\AbstractCreationController;


class ModuleCreationController  extends AbstractCreationController{

    public function consoleModuleAction(){

        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $module = $request->getParam('moduleName');
        $entity = $request->getParam('tableName');
        $entitymanager = $request->getParam('entityManager');

        if($module == false){
            $module = Line::prompt('Please Enter the Name of the Module: ',false,100);

        }

        if($entity == false){
            $entity = Line::prompt('Please Enter the Name of the The Table, the Module should be created from: ',false,100);
        }

        if($entitymanager == false){
            $entitymanager = Line::prompt('Please Enter a entity-manager (default:doctrine.entitymanager.orm_default): ',true,100);

            // If no entitymanager given, we set an default
            if($entitymanager == false){
                $entitymanager = 'doctrine.entitymanager.orm_default';
            }
        }

        // Check on a valid entitymanager
        $em = $this->getServiceLocator()->get($entitymanager);

        if(!is_a($em, 'Doctrine\ORM\EntityManager')){
            return 'Exit: Invalid Entitymanager';
        }

        $filepath = 'module/'.$module;


        if(is_dir($filepath)){

            if(!Confirm::prompt('Module filepath exists. Overwrite? [y/n]', 'y', 'n') ) {
                return 'EXIT: Module exists.';
            }
        }

        if(!is_dir($filepath) AND !mkdir($filepath, 0777, true)){
            return 'EXIT: Directory could not be created.';
        }

        // All Validated, we generate now.
        $this->setEntitymanager($entitymanager);
        $this->setFilepath($filepath);
        $this->setNamespace($module);
        $this->setTables(array($entity));


        $this->generate();

        return 'Module successfully created';
    }


    protected function generate()
    {

        $this->generateModuleClass();

        $this->generateModuleConfig();

        $this->generateViews();

        $this->generateForm();

        $this->generateController();

        $this->generateEntity();
    }

    protected function generateModuleConfig()
    {
        $oModuleConfig = new PhpArray();
        $aModuleConfig = array(
            'controllers' => array(
                'invokables' => array(
                    $this->getNamespace() . '\Controller\\' . $this->getNamespace() => $this->getNamespace() . '\Controller\\' . $this->getNamespace() . 'Controller',
                ),
            ),

            'router' => array(
                'routes' => array(
                    strtolower($this->getNamespace()) => array(
                        'type' => 'segment',
                        'options' => array(
                            'route' => '/' . strtolower($this->getNamespace()) . '[/:action][/:id]',
                            'constraints' => array(
                                'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'id' => '[0-9]+',
                            ),
                            'defaults' => array(
                                'controller' => $this->getNamespace() . '\Controller\\' . $this->getNamespace(),
                                'action' => 'index',
                            ),
                        ),
                    ),
                ),
            ),

            'view_manager' => array(
                'template_path_stack' => array(
                    strtolower($this->getNamespace()) => 'module/' . $this->getNamespace() . '/view',
                ),
            ),

            'doctrine' => array(
                'driver' => array(
                    $this->getNamespace().'_driver' => array(
                        'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                        'cache' => 'array',
                        'paths' => array('__DIR__ . /../src/' . $this->getNamespace() . '/Entity')
                    ),
                    'orm_default' => array(
                        'drivers' => array(
                            $this->getNamespace().'\Entity' => $this->getNamespace().'_driver'
                        )
                    )
                )
            ),
        );

        $fileName = 'module/' . $this->getNamespace() . '/config/module.config.php';

        if(!is_dir(dirname($fileName))){
            mkdir(dirname($fileName), 0777);
        }

        $oModuleConfig->toFile($fileName, $aModuleConfig);
    }

    protected function generateModuleClass()
    {

        $oModuleGenerator = $this->getDi()->get('ZeDoGenerator\Service\Generators\ModuleClassGenerator');

        $oModuleGenerator->setNamespace($this->getNamespace());
        $oModuleGenerator->setClassName('Module');
        $oModuleGenerator->setFilePath($this->getFilePath());
        $oModuleGenerator->generateClass();
    }


    protected function generateViews(){

        $oViewGenerator = $this->getDi()->get('ZeDoGenerator\Service\Generators\ViewGenerator');

        $oViewGenerator->setMetadata($this->getMetadata()[0]);
        $oViewGenerator->setRoute($this->getNamespace());

        //setting correct syntax
        $filter = new CamelCaseToSeparator('-');
        $sFilteredNamespace = strtolower($filter->filter($this->getNamespace()));

        $oViewGenerator->setFilePath($this->getFilePath().'/view/'.$sFilteredNamespace.'/'.$sFilteredNamespace);
        $oViewGenerator->generateView('index');
        $oViewGenerator->generateView('add');
        $oViewGenerator->generateView('edit');
        $oViewGenerator->generateView('delete');

    }


    protected function generateForm(){

        $oFormGenerator = $this->getDi()->get('ZeDoGenerator\Service\Generators\FormClassGenerator');

        $oFormGenerator->setMetadata($this->getMetadata()[0]);
        $oFormGenerator->setNamespace($this->getNamespace());
        $oFormGenerator->setFilePath($this->getFilePath().'/src/'.$this->getNamespace().'/Form');

        $oFormGenerator->generateClass();
    }


    protected function generateController(){

        $oFormGenerator = $this->getDi()->get('ZeDoGenerator\Service\Generators\ControllerClassGenerator');

        $oFormGenerator->setEntityManager($this->getEntitymanager());
        $oFormGenerator->setTableName($this->getTables()[0]);

        $oFormGenerator->setNamespace($this->getNamespace());
        $oFormGenerator->setClassName($this->getNamespace().'Controller');
        $oFormGenerator->setFilePath($this->getFilePath().'/src/'.$this->getNamespace().'/Controller');
        $oFormGenerator->generateClass();
    }


    protected function generateEntity(){

        $oEntityGenerator = $this->getDi()->get('ZeDoGenerator\Service\Generators\ZeDoMoGenerator');

        $oEntityGenerator->setNamespace($this->getNamespace().'\Entity');
        $oEntityGenerator->setFilePath($this->getFilePath().'/src/'.$this->getNamespace().'/Entity');
        $oEntityGenerator->setMetaData($this->getMetadata()[0]);
		
		if(isset($this->getDatabase())){
			$oEntityGenerator->setDatabase($this->getDatabase());
		}
		
        $oEntityGenerator->generateClass();

    }

}