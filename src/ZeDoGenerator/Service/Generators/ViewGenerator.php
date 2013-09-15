<?php

namespace ZeDoGenerator\Service\Generators;

use ZeDoGenerator\Service\AbstractFileGenerator;
use Zend\View\Model\ViewModel;


/**
 * Class ViewGenerator
 * @package ZeDoGenerator\Service\Generators
 */
class ViewGenerator extends AbstractFileGenerator{

    /**
     * @var
     */
    private $form;
    /**
     * @var
     */
    private $tableHeads;
    /**
     * @var
     */
    private $tableFields;

    /**
     * @var
     */
    private $route;

    /**
     * @param $sType string Valid Types: add, edit, delete, index
     * @return bool
     * @throws \Exception
     */
    public function generateView($sType){

        if(!in_array($sType, array('add', 'edit', 'delete', 'index'))){
            throw new \Exception('Invalid view selected');
        }

        $view = new ViewModel(array('name' => $this->getName(),
                                    'route' => $this->getRoute(),
                                    'form' => $this->getForm(),
                                    'tableheads' => $this->getTableHeads(),
                                    'tablefields' => $this->getTableFields(),
                                    ));
        $view->setTemplate('zedo/views/'.$sType.'view');

        $this->fileName = $sType;
        $this->fileExtension = 'phtml';
        $this->generatedCode = $this->getRenderer()->render($view);
        $this->writeFile();

        return true;
    }

    /**
     * @param $route
     */
    public function setRoute($route){
        $this->route = $route;
    }

    /**
     * @return mixed
     */
    private function getName(){
        return $this->getMetadata()->name;
    }

    /**
     * @return string
     * @throws \Exception
     */
    private function getRoute(){

        if(!isset($this->route)){
            throw new \Exception('No route set.');
        }

        return strtolower($this->route);
    }

    /**
     * @return string
     */
    private function getForm(){

        if(!isset($this->form)){

            $formFields = array();

            foreach($this->getMetaData()->fieldMappings AS $field){

                $formFields[] = 'echo $this->formRow($form->get(\''.$field['fieldName'].'\'));';

            }

            $this->form = implode($formFields, "\n");
        }

        return $this->form;
    }

    /**
     * @return string
     */
    private function getTableHeads(){

        if(!isset($this->tableHeads)){

            $aFields = array();

            foreach($this->getMetaData()->fieldMappings AS $field){
                $aFields[] = '  <th>'.$field['fieldName'].'</th>';
            }

            $this->tableHeads = implode($aFields, "\n")."\n".'<th>Action</th>'."\n";
        }

        return $this->tableHeads;
    }

    /**
     * @return string
     */
    private function getTableFields(){

        if(!isset($this->tableFields)){

            $aFields = array();

            foreach($this->getMetaData()->fieldMappings AS $field){
                $aFields[] = '  <td><?php echo $this->escapeHtml($item->get'.ucfirst($field['fieldName']).'());?></td>';
            }

            $this->tableFields = implode($aFields, "\n");
        }

        return $this->tableFields;
    }

}




