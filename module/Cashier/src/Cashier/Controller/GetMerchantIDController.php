<?php
namespace Cashier\Controller;

use Zend\View\Model\JsonModel;
use Cashier\Model;

/**
 * GetMerchantIDController
 *
 * @author kent@solody.com
 *
 * @version 1.0
 *
 */
class GetMerchantIDController extends BaseController
{
    public function indexAction()
    {
        $maded_sign = null;
        
        if (!$this->verifyRequest($maded_sign)){
            
            $result = new JsonModel(array(
                'sign' =>$maded_sign
            ));
            
        }else{
        
            $merchant = new Model\Merchant($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $merchant_id = $merchant->getID($this->request->getQuery('name'));
            $result = new JsonModel(array(
                'merchant_id' => $merchant_id,
                'sign' =>$maded_sign
            ));
            
        }
        
        return $result;
        
    }
}