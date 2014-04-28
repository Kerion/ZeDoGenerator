<?php
namespace ZeDoGenerator\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\Exception\ServiceNotFoundException;
use Zend\Console\Request as ConsoleRequest;
use Zend\Console\Prompt\Char;
use Zend\Console\Prompt\Line;
use Zend\Console\Prompt\Confirm;
use Zend\Di\Config;

use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory,
    Doctrine\ORM\Mapping\Driver\DatabaseDriver;

abstract class AbstractCreationController extends AbstractActionController
{

    protected $entitymanager;

    protected $namespace;

    protected $filepath;

    protected $tables;
	
    protected $database;

    private $metadata;

    private $di;

    /**
     * @param string $entitymanager
     */
    public function setEntitymanager($entitymanager)
    {
        $this->entitymanager = $entitymanager;
    }

    /**
     * @return string
     */
    public function getEntitymanager()
    {
        return $this->entitymanager;
    }

    /**
     * @param string $filepath
     */
    public function setFilepath($filepath)
    {
        $this->filepath = $filepath;
    }

    /**
     * @return string
     */
    public function getFilepath()
    {
        return $this->filepath;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * @param array $tables
     */
    public function setTables($tables)
    {
        $this->tables = $tables;
    }

    /**
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
    }
	
    /**
     * @param array $tables
     */
    public function setDatabase($database)
    {
        $this->database = $database;
    }

    /**
     * @return array
     */
    public function getDatabase()
    {
        return $this->database;
    }

    protected function getDi(){

        if(!isset($this->di)){
            $this->di = $this->getServiceLocator()->get('di');
            $this->di->configure(new Config($this->getServiceLocator()->get('config')['di']));
        }

        return $this->di;
    }

    public function consoleEntityAction(){

        $request = $this->getRequest();

        // Make sure that we are running in a console and the user has not tricked our
        // application into running this action from a public web server.
        if (!$request instanceof ConsoleRequest){
            throw new \RuntimeException('You can only use this action from a console!');
        }

        $all = $request->getParam('all');
        $entity = $request->getParam('entityName');
        $namespace = $request->getParam('namespace');
        $filepath = $request->getParam('dir');
        $entitymanager = $request->getParam('entityManager');

        // If we have no Parameters given, well ask the user
        if(!isset($entity) AND $all === false){

            $char = Char::prompt('Do you want do create one Model (o) or All Models (a)?','ao',true,false,true);

            if($char == 'o'){

                $entity = Line::prompt('Please Enter the Name of the Entity to be created: ',false,100);

            } else {
                $all = true;
            }
        }

        if($namespace == false){
            $namespace = Line::prompt('Please Enter the Name of Namespace to be used: ',false,100);

        }

        if($filepath == false){
            $filepath = Line::prompt('Please Enter a File-Path for the Entity-Files: (std: "Entities") ',true,100);


            // If no filepath given, we set an default filepath
            if(empty($filepath)){
                $filepath = 'Entities';
            }
        }

        if(!is_dir($filepath)){

            if (Confirm::prompt('Directory '.$filepath.' does not exist. Create? [y/n]', 'y', 'n') ) {
                if(!mkdir($filepath, 0777, true)){
                    return 'EXIT: Directory could not be created.';
                }
            } else {
                return 'EXIT: Invalid Directory.';
            }
        }


        if($entitymanager == false){
            $entitymanager = Line::prompt('Please Enter a entity-manager (default:doctrine.entitymanager.orm_default): ',true,100);

            // If no entitymanager given, we set an default
            if($entitymanager == false){
                $entitymanager = 'doctrine.entitymanager.orm_default';
            }
        }

        // Check on a valid entitymanager
        try{
            $em = $this->getServiceLocator()->get($entitymanager);

            if(!is_a($em, 'Doctrine\ORM\EntityManager')){
                throw new ServiceNotFoundException();
            }

        } catch(ServiceNotFoundException $e){
            return 'Exit: Invalid Entitymanager';
        }

        // All Validated, we generate now.
        $this->setEntitymanager($entitymanager);
        $this->setFilepath($filepath);
        $this->setNamespace($namespace);

        if($all === true){
            $tables = array();
        } else {
            $tables = array($entity);
        }

        $this->setTables($tables);

        $this->generate();

        return 'Files successfully created'."\n";
    }



    /**
     * @return array
     */
    protected function getMetadata()
    {

        if(!isset($this->metadata)){
            $em = $this->getServiceLocator()->get($this->entitymanager);

            $databaseDriver = new DatabaseDriver(
                $em->getConnection()->getSchemaManager()
            );

            $em->getConfiguration()->setMetadataDriverImpl(
                $databaseDriver
            );

			// Setting the Database for annotation-prefix
			$this->setDatabase($em->getConnection()->getDatabase());
		
            // Doctrine dosn't support mysql-enums, so we declare them here as a string
            $conn = $em->getConnection();
            $conn->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');
			//@todo more typemappings??
            //$conn->getDatabasePlatform()->registerDoctrineTypeMapping('datetime', 'string');

            //\Doctrine\DBAL\Types\Type::addType('blob', 'Doctrine\DBAL\Types\Blob');
            //$em->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('blob', 'string');

            $cmf = new DisconnectedClassMetadataFactory();
            $cmf->setEntityManager($em);

            $this->metadata = $cmf->getAllMetadata();
        }

        return $this->metadata;
    }


    abstract protected function generate();


}