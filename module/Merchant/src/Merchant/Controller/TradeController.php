<?php
namespace Merchant\Controller;

use Zend\View\Model\ViewModel;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\DbSelect;

/**
 * TradeController
 *
 * @author
 *
 * @version
 *
 */
class TradeController extends BaseController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $this->appendTitle($this->translate('Trade query'));
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $select = new \Zend\Db\Sql\Select();
        $select->from('trade')->where(array(
            'merchant_id'=>$this->getMerchantId()
        ));
        
        
        $dbselect = new DbSelect($select,$dbAdapter);
        $paginator = new Paginator($dbselect);
        
        $paginator->setCurrentPageNumber( $this->params()->fromRoute('page') );
        
        $vars = array('paginator'=>$paginator);
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
}