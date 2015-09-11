<?php
namespace Cashier\Controller;

use Application\Controller\AclController;

/**
 * BaseController
 *
 * @author
 *
 * @version
 *
 */
class BaseController extends AclController
{
    public $AclResourceName = __CLASS__;
    
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
       
       $arrayData = $this->getRequest()->getQuery()->toArray();
       
       // sort the params
       ksort($arrayData);
       reset($arrayData);
       
       foreach ($arrayData as $k=>$v){
           if ($k === 'sign'){
               $remote_sign = $v;
           }else{
               if (!empty($sign)) $sign = $sign.'&';
               $sign = $sign.$k.'='.$v;
           }
       }
       
       if (!empty($sign)) $sign = $sign.'&';
       $sign = $sign.'apikey='.$apikey;
       $maded_sign = md5($sign);
       if ($remote_sign === $maded_sign) return true;
       else return false;
       
    }
}