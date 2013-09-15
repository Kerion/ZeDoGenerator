<?php
namespace ZeDoGenerator\Controller\CreationController;

use ZeDoGenerator\Controller\AbstractCreationController;

class ZedomodelCreationController extends AbstractCreationController
{

	protected function generate()
	{
        $metadata = $this->getMetadata();
		
		if (count($metadata)) {
	
	
			$generator = $this->getDi()->get('ZeDoGenerator\Service\Generators\ZeDoMoGenerator');
            $generator->setNamespace($this->getNamespace());
		
			foreach ($metadata as $onemetadata) {

				if(count($this->getTables()) > 0 AND !in_array($onemetadata->name, $this->getTables())){
					continue;
				}

                $generator->setFilePath($this->getFilePath());
                $generator->setMetaData($onemetadata);
				$generator->generateClass();
	
			}
	
		} else {
			die('No Metadata Classes to process.' . PHP_EOL);
		}
	}

}