<?php
namespace Merchant\Model;

class Report
{
    public $MerchantID;
    private $sl;
    
    public $TotleIncome = null;
    public $FreeIncome = null;
    public $WithdrawedIncome = null;
    public $EffectiveIncome = null;
    public $TradeAll = null;
    public $TradePayed = null;
    public $WithdrawAll = null;
    public $WithdrawPayed = null;
    public $EnableMakeWithdraw = null;

    function __construct( $sl, $merchant_id = NULL )
    {
        $this->sl = $sl;
        $this->MerchantID = $merchant_id;
        $this->load();
    }
    
    public function load( $merchant_id = NULL ) {
        
        if (!empty($merchant_id)) $this->MerchantID = $merchant_id;
        
        // Read Data From DB.
        // Count total
        $TotleIncome = null;
        $FreeIncome = null;
        $WithdrawedIncome = null;
        $EffectiveIncome = null;
        $TradeAll = null;
        $TradePayed = null;
        $WithdrawAll = null;
        $WithdrawPayed = null;
        $EnableMakeWithdraw = null;
        
        $dbAdapter = $this->sl->get('Zend\Db\Adapter\Adapter');
        $GetClientMerchantID = $this->sl->get('GetClientMerchantID');
        if (!empty($this->MerchantID)) $MerchantID = $this->MerchantID;
        else $MerchantID = $GetClientMerchantID();
        
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
        
        $rs_EnableMakeWithdraw = $dbAdapter->query(
            'SELECT sum(`price`) as sum FROM `withdraw` WHERE `merchant_id`='.$MerchantID,
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
        
        if ($rs_EnableMakeWithdraw->count() > 0){
            $row = $rs_EnableMakeWithdraw->current();
            $EnableMakeWithdraw = $FreeIncome - $row['sum'];
        }else{
            $EnableMakeWithdraw = $FreeIncome;
        }
        
        
        $this->TotleIncome=$TotleIncome;
        $this->FreeIncome=$FreeIncome;
        $this->WithdrawedIncome=$WithdrawedIncome;
        $this->EffectiveIncome=$FreeIncome-$WithdrawedIncome;
        $this->TradeAll=$TradeAll;
        $this->TradePayed=$TradePayed;
        $this->WithdrawAll=$WithdrawAll;
        $this->WithdrawPayed=$WithdrawPayed;
        $this->EnableMakeWithdraw = $EnableMakeWithdraw;
        
        return array(
            'TotleIncome'=>$this->TotleIncome,
            'FreeIncome'=>$this->FreeIncome,
            'WithdrawedIncome'=>$this->WithdrawedIncome,
            'EffectiveIncome'=>$this->EffectiveIncome,
            'TradeAll'=>$this->TradeAll,
            'TradePayed'=>$this->TradePayed,
            'WithdrawAll'=>$this->WithdrawAll,
            'WithdrawPayed'=>$this->WithdrawPayed,
            'EnableMakeWithdraw'=>$this->EnableMakeWithdraw,
        );
        
    }
}

?>