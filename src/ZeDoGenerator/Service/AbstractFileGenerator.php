<?php
namespace ZeDoGenerator\Service;

use Doctrine\ORM\Mapping\ClassMetadata;
use Zend\View\Renderer\PhpRenderer;


/**
 * Class AbstractFileGenerator
 * @package ZeDoGenerator\Service
 */
abstract class AbstractFileGenerator {

    /**
     * @var
     */
    private $filePath;

    /**
     * @var
     */
    private $metadata;

    /**
     * @var
     */
    protected $generatedCode;

    /**
     * @var
     */
    protected $fileName;

    /**
     * @var
     */
    protected $fileExtension;

    /**
     * @var
     */
    private $renderer;


    /**
     * @param PhpRenderer $renderer
     */
    public function setRenderer(PhpRenderer $renderer){
        $this->renderer = $renderer;
    }

    /**
     * @return \Zend\View\Renderer\PhpRenderer
     * @throws \Exception
     */
    protected function getRenderer(){
        if(!is_a($this->renderer, 'Zend\View\Renderer\PhpRenderer')){
            throw new \Exception('No PhpRenderer set');
        }
        return $this->renderer;
    }

    /**
     * @param ClassMetadata $metadata
     */
    public function setMetaData(ClassMetadata $metadata){
        $this->metadata = $metadata;
    }

    /**
     * @return ClassMetadata
     * @throws \Exception
     */
    protected function getMetaData(){
        if(!isset($this->metadata)){
            throw new \Exception('No Metadata given');
        }
        return $this->metadata;
    }

    /**
     * @param $sPath
     */
    public function setFilePath($sPath){
        $this->filePath = $sPath;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    protected function writeFile(){

        if(!isset($this->filePath)){
            throw new \Exception('no filePath given');
        }

        if(empty($this->fileName)){
            throw new \Exception('No Filename given');
        }

        if (!is_dir($this->filePath)) {
            mkdir($this->filePath, 0777, true);
        }

        if (!is_dir($this->filePath)) {
            throw new \Exception('filePath invalid');
        }

        if(!isset($this->fileExtension)){
            $sFileExtension = 'php';
        } else {
            $sFileExtension = $this->fileExtension;
        }

        file_put_contents($this->filePath.'/'.$this->fileName.'.'.$sFileExtension, $this->generatedCode);

        return true;
    }
}