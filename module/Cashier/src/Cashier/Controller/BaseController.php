<?php
namespace Cashier\Controller;

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
    /**
     * Verify the request by the apikey.
     * @return boolean
     */
    protected function verifyRequest(&$maded_sign = NULL)
    {
       $config = $this->getServiceLocator()->get('Config');
       
       $sign = '';
       $remote_sign = '';
       $apikey = $config['apikey'];
       
       foreach ($this->getRequest()->getQuery() as $k=>$v){
           if ($k === 'sign'){
               $remote_sign = $v;
           }else{
               if (!empty($sign)) $sign = $sign.'&';
               $sign = $sign.$k.'='.$v;
           }
       }
       
       if (!empty($sign)) $sign = $sign.'&';
       $sign = $sign.'key='.$apikey;
       $maded_sign = md5($sign);
       if ($remote_sign === $maded_sign) return true;
       else return false;
       
    }
}