<?php

namespace ZeDoGenerator\Service\Generators;

use ZeDoGenerator\Service\AbstractClassGenerator;
use Zend\View\Model\ViewModel;


/**
 * Class ModuleClassGenerator
 * @package ZeDoGenerator\Service\Generators
 */
class ModuleClassGenerator extends AbstractClassGenerator{

    /**
     * @return bool
     */
    public function generateClass(){

        $view = new ViewModel(array('namespace' => $this->getNamespace()));
        $view->setTemplate('zedo/classes/moduleclass');

        $this->generatedCode = $this->getRenderer()->render($view);

        $this->writeFile();

        return true;

    }
}