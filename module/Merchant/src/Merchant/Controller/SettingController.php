<?php
namespace Merchant\Controller;

use Zend\View\Model\ViewModel;
use Merchant\Form;
use Merchant\Model\Setting;

/**
 * SettingController
 *
 * @author
 *
 * @version
 *
 */
class SettingController extends BaseController
{

    /**
     * The default action - show the home page
     */
    public function indexAction()
    {
        $headTitle = $this->getServiceLocator()->get('viewHelperManager')->get('headTitle');
        $translator = $this->getServiceLocator()->get('translator');
        $headTitle->append($translator->translate('Withdraw account setting'));
        
        
        $form = new Form\SettingForm();
        $vars = array('form'=>$form);
        
        $GetClientMerchantID = $this->getServiceLocator()->get('GetClientMerchantID');
        $MerchantID = $GetClientMerchantID();
        
        if (empty($MerchantID)) throw new \Exception('MerchantID is empty !');
        
        $setting = new Setting($MerchantID,$this->getServiceLocator());
        $form->bind($setting);
        
        $request = $this->getRequest();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
        
            // Validate the form
            if ($form->isValid()) {
                $setting->save();
            }
        }
        
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
}