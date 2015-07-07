<?php
namespace Setting\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

/**
 * APIKeyController
 *
 * @author
 *
 * @version
 *
 */
class APIKeyController extends AbstractActionController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        // TODO Auto-generated APIKeyController::indexAction() default action
        return new ViewModel();
    }
}