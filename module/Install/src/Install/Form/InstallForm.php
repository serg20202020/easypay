<?php
namespace Install\Form;

use Zend\Form\Form;

class InstallForm extends Form
{
 public function __construct($name = null)
 {
     // we want to ignore the name passed
     parent::__construct('install');


     $this->add(array(
         'name' => 'server',
         'type' => 'Text',
     ));
     $this->add(array(
         'name' => 'username',
         'type' => 'Text',
     ));

     $this->add(array(
         'name' => 'password',
         'type' => 'Text',
     ));
     
     $this->add(array(
         'name' => 'database',
         'type' => 'Text',
     ));
     
     $this->add(array(
         'name' => 'submit',
         'type' => 'Submit',
     ));
 }
}
