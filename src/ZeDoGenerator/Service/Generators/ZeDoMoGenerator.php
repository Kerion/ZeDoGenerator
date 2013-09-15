<?php

namespace ZeDoGenerator\Service\Generators;

use Doctrine\ORM\Tools\EntityGenerator;
use ZeDoGenerator\Service\AbstractClassGenerator;
use Zend\View\Model\ViewModel;


/**
 * Class ZeDoMoGenerator
 * @package ZeDoGenerator\Service\Generators
 */
class ZeDoMoGenerator extends AbstractClassGenerator{

    /**
     * @var \Doctrine\ORM\Tools\EntityGenerator
     */
    private $entityGenerator;

    /**
     * @var string
     */
    private static $useStatements = "use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;\n";


    /**
     *
     */
    public function __construct(){
		$this->entityGenerator = new EntityGenerator();
		$this->entityGenerator->setGenerateAnnotations(true);
		$this->entityGenerator->setGenerateStubMethods(true);
	}


    /**
     * @return bool
     * @throws \Exception
     */
    public function generateClass(){

        $metadata = $this->getMetaData();

		$this->setClassName($metadata->name);

		// setting the namespace
		$metadata->name = $this->getNamespace().'\\'.$metadata->name;

		//generate the basic class-code
		if(!$this->generatedCode = $this->entityGenerator->generateEntityClass($metadata)){
			throw new \Exception('Entity class could not be created');
		}

		$this->generateUseStatements();

		$this->generateMethods();

		$this->writeFile();

		return true;

	}

    /**
     *
     */
    private function generateUseStatements(){

        // Insert Use Statements in front of the given use-Statements
		$useExploded = explode('use', $this->generatedCode);
		$useExploded[0] = $useExploded[0].self::$useStatements;
		$this->generatedCode = implode('use', $useExploded);

        return true;

	}


    /**
     *
     */
    private function generateMethods(){

		// Implode Class parts. First we strip the last Bracket, then we replace it.
		$this->generatedCode = implode(array(

				substr(trim($this->generatedCode), 0, -1), // Strip last Bracket
                'private $inputFilter;',
				$this->generateArrayCopyMethod(),
				$this->generatePopulateMethod(),
				$this->generateInputFilters(),
				'}'
		),
			"\n\n");

        return true;
	}


    /**
     * @return string
     */
    private function generatePopulateMethod(){

        $view = new ViewModel(array('fieldMappings' => $this->getMetaData()->fieldMappings));
        $view->setTemplate('zedo/methods/modelpopulatemethod');

        return $this->getRenderer()->render($view);
    }


    /**
     * @return string
     */
    private function generateArrayCopyMethod(){

        $view = new ViewModel();
        $view->setTemplate('zedo/methods/modelarraycopymethod');

        return $this->getRenderer()->render($view);
    }


    /**
     * @return string
     */
    private function generateInputFilters(){


        $view = new ViewModel(array('fieldMappings' => $this->getMetaData()->fieldMappings));
        $view->setTemplate('zedo/methods/modelgetinputfiltermethod');

        return $this->getRenderer()->render($view);

	}

}
