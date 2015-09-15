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
        $this->appendTitle($this->translate('Withdraw account setting'));
        
        
        $form = new Form\SettingForm();
        $vars = array('form'=>$form);
        
        $GetClientMerchantID = $this->getServiceLocator()->get('GetClientMerchantID');
        $MerchantID = $GetClientMerchantID();
        
        if (empty($MerchantID)) throw new \Exception('MerchantID is empty !');
        
        $setting = new Setting($this->getServiceLocator(),$MerchantID);
        $form->bind($setting);
        
        $request = $this->getRequest();
        $vars['is_post'] = $request->isPost();
        if ($request->isPost()) {
        
            $post_data = $request->getPost();
        
            $form->setData($post_data);
        
            // Validate the form
            if ($form->isValid()) {
                try {
                
                    $r_int = $setting->save();
                    $vars['status'] = true;
                
                } catch (Exception $e) {
                
                    $vars['status'] = false;
                
                }
            }
        }
        
        
        $view_page = new ViewModel($vars);
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }
}