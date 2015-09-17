<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Cashier for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Cashier\Controller;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature;
use Zend\Db\ResultSet\ResultSet;
use Zend\View\Model\JsonModel;

class IndexController extends BaseController
{
    
    /**
     * Create easypay trade.
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        
        if ($this->verifyRequest()){
            
            $this->appendTitle('收银台');
            $this->layout()->setVariable('is_cashier_page', true);
            
            
            $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
            
            
            /**
             * Check the parameters if they are valid.
             */
            $parameters = $this->getRequest()->getQuery();
            
            // if merchant exsist
            // if trade|redirect_url|notify_url|sign empty
            // if price is a floating numberic
            $exception = new \Exception('Parameters is invalid!');
            
            if ( empty(trim($parameters['trade'])) || 
                 empty(trim($parameters['redirect_url'])) || 
                 empty(trim($parameters['notify_url'])) || 
                 empty(trim($parameters['sign'])) ) throw $exception;
            
            
            if ( !is_numeric(trim($parameters['price'])) ) throw $exception;
            
            $merchant_validator = new \Zend\Validator\Db\NoRecordExists(
                array(
                    'table'   => 'merchant',
                    'field'   => 'name',
                    'adapter' => $dbAdapter
                )
            );
            if ($merchant_validator->isValid(intval($parameters['merchant']))) throw $exception;
            
                
                
                
            /**
             * Check if there is an exsist trade.
             */
            $GetMerchantIdByName = $this->getServiceLocator()->get('GetMerchantIdByName');
            $merchant_id = $GetMerchantIdByName($parameters['merchant']);
            $trade = $this->getTrade($merchant_id,trim($parameters['trade']));
            
            $trade_id = null;
            $payment_interface_type = null;
            

            if (!empty($trade)){   // Trade exsist.
                
                if ($trade->pay_status){
                    throw new \Exception('Trade had payed before!');
                }else{
                    
                    // Update the exsist trade
                    $trade->price = trim($parameters['price']);
                    $trade->redirect_url = trim($parameters['redirect_url']);
                    $trade->notify_url = trim($parameters['notify_url']);
                    $trade->save();
                    
                    $trade_id = $trade->id;
                    $payment_interface_type = $trade->payment_interface_type;
                    
                }
                
            }else{   // Trade not exsist.
                
                // Create a new trade
                $tableGateway = new TableGateway('trade', $dbAdapter, new Feature\RowGatewayFeature('id'));
                
                $tableGateway->insert(array(
                    'merchant_id'=>$merchant_id,
                    'merchant_trade_id'=>trim($parameters['trade']),
                    'redirect_url'=>trim($parameters['redirect_url']),
                    'notify_url'=>trim($parameters['notify_url']),
                    'price'=>trim($parameters['price']),
                    'pay_status'=>0,
                    'create_time'=>date('Y-n-j H:i:s',time()),
                ));
                
                $trade_id = $tableGateway->getLastInsertValue();
                
            }
            
            return array(
                'price'=>trim($parameters['price']),
                'merchant'=>$parameters['merchant'],
                'trade'=>trim($parameters['trade']),
                'selected_payment' =>$payment_interface_type, 
            );

        }else{
            
            throw new \Exception('Bad Reaquest !');
            
        }
        
    }

    /**
     * Save one trade's payment type into database in AJAX method.
     * @return \Zend\View\Model\JsonModel
     */
    public function setpaymentAction()
    {
        $data = array();
        $post = $this->request->getPost();
        
        $GetMerchantIdByName = $this->getServiceLocator()->get('GetMerchantIdByName');
        $merchant_id = $GetMerchantIdByName($post['merchant']);
            
        $row = $this->getTrade($merchant_id, $post['trade']);
        
        $payment_interface_type = array(
            'alipay'=>'alipay',
            'wechatpay'=>'wxpay',
            'bankcard'=>'bankcard'
        );
        
        if ( !$this->request->isPost() || !isset($payment_interface_type[$post['payment_method']]) || empty($row) ){
            
            $data['status'] = 'false';
            $data['error'] = 'Invalid request.'.var_export($row,true);
            
        }else{
            
            if ($row){
                $row->payment_interface_type = $payment_interface_type[$post['payment_method']];
                $data['return'] = $row->save();
                $data['interface'] = $row->payment_interface_type;
                @session_start();
                $_SESSION['paying_trade'] = $row->toArray();
            }
            
            $data['status'] = 'true';
            $data['error'] = 'OK';
            
        }
            
        return new JsonModel($data);
        
    }
    
    /**
     * Get the trade Object by the merchant id and the trade id, if it exsist.
     * @param unknown $merchant
     * @param unknown $trade
     * @throws \Exception
     * @return Ambigous <\Zend\Db\ResultSet\array, ArrayObject, \Zend\Db\ResultSet\null>|NULL
     */
    private function getTrade($merchantID,$trade) {
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        
        $tableGateway = new TableGateway('trade', $dbAdapter, new Feature\RowGatewayFeature('id'));
        
        $rs = $tableGateway->select(array(
            'merchant_id'=>$merchantID,
            'merchant_trade_id'=>$trade,
        ));
        
        if ($rs instanceof ResultSet){
            if ($rs->count()){
        
                return $rs->current();
                
            }else{
                return NULL;
            }
        }else{
                throw new \Exception('DB Error!');
        }
    }
}
