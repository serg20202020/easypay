<?php
namespace Merchant\Controller;

use Zend\Mvc\Controller\AbstractActionController;


/**
 * BaseController
 *
 * @author
 *
 * @version
 *
 */
class BaseController extends AbstractActionController
{
    function __construct() {
        //parent::__construct();
        
        @session_start();
        echo 'This will check if logined.';
        print_r($_SERVER);
        
    }
}