<?php

namespace ZeDoGenerator\Service\Generators;

use ZeDoGenerator\Service\AbstractClassGenerator;
use Zend\View\Model\ViewModel;


/**
 * Class FormClassGenerator
 * @package ZeDoGenerator\Service\Generators
 */
class FormClassGenerator extends AbstractClassGenerator{

    /**
     * @return bool
     */
    public function generateClass(){

        if(!$this->getClassName()){
            $this->setClassName($this->getMetaData()->name.'Form');
        }

        $view = new ViewModel(array('namespace' => $this->getNamespace(),
                                    'classname' => $this->getClassName(),
                                    'fieldMappings' => $this->getMetaData()->fieldMappings));
        $view->setTemplate('zedo/classes/formclass');

        $this->generatedCode = $this->getRenderer()->render($view);

        $this->writeFile();

        return true;
    }

}
