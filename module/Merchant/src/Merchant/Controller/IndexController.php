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



class IndexController extends BaseController
{
    public function indexAction()
    {
        $this->appendTitle($this->translate('Merchant workbench'));
        
        
        // Count total 
        $TotleIncome = null;
        $FreeIncome = null;
        $WithdrawedIncome = null;
        $EffectiveIncome = null;
        $TradeAll = null;
        $TradePayed = null;
        $WithdrawAll = null;
        $WithdrawPayed = null;
        
        $dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
        $GetClientMerchantID = $this->getServiceLocator()->get('GetClientMerchantID');
        $MerchantID = $GetClientMerchantID();
        
        $rs_TotleIncome = $dbAdapter->query(
            'SELECT sum(`price`) as sum FROM `trade` WHERE `merchant_id`='.$MerchantID.' AND `pay_status`=1',
            $dbAdapter::QUERY_MODE_EXECUTE
        );
        
        $rs_FreeIncome = $dbAdapter->query(
            'SELECT sum(`price`) as sum FROM `trade` WHERE `merchant_id`='.$MerchantID.' AND `pay_status`=1 AND `pay_time` < DATE_SUB(now(),INTERVAL 1 DAY)',
            $dbAdapter::QUERY_MODE_EXECUTE
        );
        
        $rs_WithdrawedIncome = $dbAdapter->query(
            'SELECT sum(`price`) as sum FROM `withdraw` WHERE `merchant_id`='.$MerchantID.' AND `pay_status`=1',
            $dbAdapter::QUERY_MODE_EXECUTE
        );
        
        $rs_TradeAll = $dbAdapter->query(
            'SELECT count(*) as count FROM `trade` WHERE `merchant_id`='.$MerchantID,
            $dbAdapter::QUERY_MODE_EXECUTE
        );
        
        $rs_TradePayed = $dbAdapter->query(
            'SELECT count(*) as count FROM `trade` WHERE `merchant_id`='.$MerchantID.' AND `pay_status`=1',
            $dbAdapter::QUERY_MODE_EXECUTE
        );
        
        $rs_WithdrawAll = $dbAdapter->query(
            'SELECT count(*) as count FROM `withdraw` WHERE `merchant_id`='.$MerchantID,
            $dbAdapter::QUERY_MODE_EXECUTE
        );
        
        $rs_WithdrawPayed = $dbAdapter->query(
            'SELECT count(*) as count FROM `withdraw` WHERE `merchant_id`='.$MerchantID.' AND `pay_status`=1',
            $dbAdapter::QUERY_MODE_EXECUTE
        );
        
        if ($rs_TotleIncome->count() > 0){
            $row = $rs_TotleIncome->current();
            $TotleIncome = $row['sum'];
        }
        
        if ($rs_FreeIncome->count() > 0){
            $row = $rs_FreeIncome->current();
            $FreeIncome = $row['sum'];
        }
        
        if ($rs_WithdrawedIncome->count() > 0){
            $row = $rs_WithdrawedIncome->current();
            $WithdrawedIncome = $row['sum'];
        }
        
        if ($rs_TradeAll->count() > 0){
            $row = $rs_TradeAll->current();
            $TradeAll = $row['count'];
        }
        
        if ($rs_TradePayed->count() > 0){
            $row = $rs_TradePayed->current();
            $TradePayed = $row['count'];
        }
        
        if ($rs_WithdrawAll->count() > 0){
            $row = $rs_WithdrawAll->current();
            $WithdrawAll = $row['count'];
        }
        
        if ($rs_WithdrawPayed->count() > 0){
            $row = $rs_WithdrawPayed->current();
            $WithdrawPayed = $row['count'];
        }
        
        $view_page = new ViewModel(array(
            'TotleIncome'=>$TotleIncome,
            'FreeIncome'=>$FreeIncome,
            'WithdrawedIncome'=>$WithdrawedIncome,
            'EffectiveIncome'=>$EffectiveIncome,
            'TradeAll'=>$TradeAll,
            'TradePayed'=>$TradePayed,
            'WithdrawAll'=>$WithdrawAll,
            'WithdrawPayed'=>$WithdrawPayed,
        ));
        
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
