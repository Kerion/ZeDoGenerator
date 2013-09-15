<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kerion
 * Date: 27.08.13
 * Time: 20:51
 * To change this template use File | Settings | File Templates.
 */

namespace ZeDoGenerator\Service\Generators;


use ZeDoGenerator\Service\AbstractClassGenerator;
use Zend\View\Model\ViewModel;

class ControllerClassGenerator extends AbstractClassGenerator{

    private $entityManager;
    private $tableName;

    /**
     * @param mixed $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getEntityManager()
    {
        if(!$this->entityManager){
            throw new \Exception('No EntityManager given');
        }

        return $this->entityManager;
    }

    /**
     * @param mixed $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getTableName()
    {
        if(!$this->tableName){
            throw new \Exception('No TableName given');
        }

        return $this->tableName;
    }

    public function generateClass(){

        $view = new ViewModel(array('namespace' => $this->getNamespace(),
                                    'tableName' => $this->getTableName(),
                                    'entityManager' => $this->getEntityManager()));
        $view->setTemplate('zedo/classes/controllerclass');

        $this->generatedCode = $this->getRenderer()->render($view);

        $this->writeFile();

        return true;

    }
}