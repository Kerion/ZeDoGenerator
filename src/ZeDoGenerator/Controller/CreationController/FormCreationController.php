<?php
namespace ZeDoGenerator\Controller\CreationController;

use ZeDoGenerator\Controller\AbstractCreationController;
use ZeDoGenerator\Service\Generators\FormClassGenerator;

class FormCreationController extends AbstractCreationController
{

    protected function generate()
    {

        $metadata = $this->getMetadata();

        if (count($metadata)) {

            $generator = new FormClassGenerator();
            $generator->setNamespace($this->getNamespace());

            foreach ($metadata as $onemetadata) {

                if(count($this->getTables()) > 0 AND !in_array($onemetadata->name, $this->getTables())){
                    continue;
                }

                $generator->setFilePath($this->getFilePath());
                $generator->generateForm($onemetadata);

            }

        } else {
            die('No Metadata Classes to process.' . PHP_EOL);
        }
    }

}