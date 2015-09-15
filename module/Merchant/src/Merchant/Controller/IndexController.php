<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/Merchant for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Merchant\Controller;
use Zend\View\Model\ViewModel;
use Merchant\Model\Report;



class IndexController extends BaseController
{
    public function indexAction()
    {
        $this->appendTitle($this->translate('Merchant workbench'));
        
        // Load Report.
        $report = new Report($this->getServiceLocator());
        $report_array = $report->load();
        
        $view_page = new ViewModel($report_array);
        
        $view_page = $this->setChildViews($view_page);
        
        return $view_page;
    }

    /**
     * Merchant automately login
     * @return multitype:
     */
    public function loginAction()
    {
        // Merchant automately login
        
        $encrypted_token = $this->params()->fromQuery('a');
        $encrypted_ip = $this->params()->fromQuery('b');
        
        $key_file_path = getcwd().'/data/alipay/key/rsa_private_key.pem'; //echo $key_file_path.'<br>';
        $key = openssl_pkey_get_private('file://'.$key_file_path);
        
        if (file_exists($key_file_path) && $key){
             
            openssl_private_decrypt(base64_decode($encrypted_token),$decrypted_token,$key);
            openssl_private_decrypt(base64_decode($encrypted_ip),$decrypted_ip,$key);

        }else{
            exit('数据加密时出错：公钥不存在或无效！');
        }
        
        @session_start();
        
        $_SESSION['Merchant'] = array(
            'token'=>$decrypted_token,
            'ip'=>$decrypted_ip
        );
        
        //echo $decrypted_token."<br>";
        //echo $decrypted_ip."<br>";
        
        return array();
    }
}
