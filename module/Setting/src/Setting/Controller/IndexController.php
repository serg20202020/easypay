<?php
namespace Setting\Controller;

use Zend\View\Model\ViewModel;

/**
 * IndexController
 *
 * @author
 *
 * @version
 *
 */
class IndexController extends BaseSettingController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $this->appendTitle($this->translate('Setting up the system.'));
        
        $view_page = new ViewModel();
        $this->setChildViews($view_page);
        
        return $view_page;
    }
}