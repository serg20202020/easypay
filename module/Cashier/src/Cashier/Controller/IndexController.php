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

class IndexController extends BaseController
{
    /**
     * Create easypay trade.
     * @see \Zend\Mvc\Controller\AbstractActionController::indexAction()
     */
    public function indexAction()
    {
        if (!$this->verifyRequest()){
            
            
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
                    'field'   => 'id',
                    'adapter' => $dbAdapter
                )
            );
            if ($merchant_validator->isValid(intval($parameters['merchant']))) throw $exception;
            
                
                
                
            /**
             * Check if there is an exsist trade.
             */
            $tableGateway = new TableGateway('trade', $dbAdapter, new Feature\RowGatewayFeature('id'));
            
            $rs = $tableGateway->select(array(
                'merchant_id'=>intval($parameters['merchant']),
                'merchant_trade_id'=>trim($parameters['trade']),
            ));
            
            if ($rs instanceof ResultSet){
                if ($rs->count()){
                    
                    $trade = $rs->current();
                    
                    if ($trade->pay_status){
                        throw new \Exception('Trade had payed before!');
                    }else{
                        
                        // Update the exsist trade
                        $trade = $rs->current();
                        $trade->price = trim($parameters['price']);
                        $trade->redirect_url = trim($parameters['redirect_url']);
                        $trade->notify_url = trim($parameters['notify_url']);
                        $trade->save();
                        
                    }
                    
                }else{
                    
                    // Create a new trade
                    $tableGateway->insert(array(
                        'merchant_id'=>intval($parameters['merchant']),
                        'merchant_trade_id'=>trim($parameters['trade']),
                        'redirect_url'=>trim($parameters['redirect_url']),
                        'notify_url'=>trim($parameters['notify_url']),
                        'price'=>trim($parameters['price']),
                        'pay_status'=>0,
                        'create_time'=>date('Y-n-j H:i:s',time()),
                    ));
                    
                }
            }else{
                throw new \Exception('DB Error!');
            }
            
        }
        
        return array(
            'price'=>trim($parameters['price'])
        );
    }

    public function setpaymentAction()
    {
        // This shows the :controller and :action parameters in default route
        // are working when you browse to /index/index/foo
        return array();
    }
}
