<?php

namespace ZeDoGenerator\Service;

abstract class AbstractClassGenerator extends AbstractFileGenerator{

	private $namespace;

    private $className;

    protected function getNamespace(){
        if(!$this->namespace){
            throw new \Exception('No namespace given');
        }
        return $this->namespace;
    }

    protected function getClassName(){
        return $this->className;
    }

    /**
     * @param mixed $className
     */
    public function setClassName($className)
    {
        $this->className = $className;
    }

	public function setNamespace($namespace){
		$this->namespace = $namespace;
	}

    protected function writeFile(){

        $this->fileName = $this->className;

        parent::writeFile();
    }



    abstract public function generateClass();

}
