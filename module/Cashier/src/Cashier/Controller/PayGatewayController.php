<?php
namespace Cashier\Controller;

use Zend\View\Model\ViewModel;
use Cashier\Model\Alipay\AlipaySubmit;

/**
 * PayGatewayController
 *
 * @author
 *
 * @version
 *
 */
class PayGatewayController extends BaseController
{

    /**
     * The default action - show the home page
     */
    public function alipayAction()
    {
        
        /**
         * Get the trade info data.
         */
        session_start();
        print_r($_SESSION);
        
        /**
         * Get the configuration of alipay payment interface.
         */
        
        return new ViewModel();
        
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= '';
        
        //收款支付宝帐户
        $alipay_config['seller_email']	= '';
        
        //安全检验码，以数字和字母组成的32位字符
        //如果签名方式设置为“MD5”时，请设置该参数
        $alipay_config['key']			= '';
        
        
        //商户的私钥（后缀是.pem）文件相对路径
        //如果签名方式设置为“0001”时，请设置该参数
        $alipay_config['private_key_path']	= 'key/rsa_private_key.pem';
        
        //支付宝公钥（后缀是.pem）文件相对路径
        //如果签名方式设置为“0001”时，请设置该参数
        $alipay_config['ali_public_key_path']= 'key/alipay_public_key.pem';
        
        
        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        
        
        //签名方式 不需修改
        $alipay_config['sign_type']    = '0001';
        
        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= 'utf-8';
        
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd().'\\cacert.pem';
        
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';
        
        /**************************调用授权接口alipay.wap.trade.create.direct获取授权码token**************************/
        	
        //返回格式
        $format = "xml";
        //必填，不需要修改
        
        //返回格式
        $v = "2.0";
        //必填，不需要修改
        
        //请求号
        $req_id = date('Ymdhis');
        //必填，须保证每次请求都是唯一
        
        //**req_data详细信息**
        
        //服务器异步通知页面路径
        $notify_url = $this->url('cashier/gateway_alipay/notify');
        //需http://格式的完整路径，不允许加?id=123这类自定义参数
        
        //页面跳转同步通知页面路径
        $call_back_url = $this->url('cashier/gateway_alipay/redirect');
        //需http://格式的完整路径，不允许加?id=123这类自定义参数
        
        //操作中断返回地址
        $merchant_url = $this->url('cashier/fail');
        //用户付款中途退出返回商户的地址。需http://格式的完整路径，不允许加?id=123这类自定义参数
        
        //商户订单号
        $out_trade_no = $_POST['WIDout_trade_no'];
        //商户网站订单系统中唯一订单号，必填
        
        //订单名称
        $subject = $_POST['WIDsubject'];
        //必填
        
        //付款金额
        $total_fee = $_POST['WIDtotal_fee'];
        //必填
        
        //请求业务参数详细
        $req_data = '<direct_trade_create_req><notify_url>' . $notify_url . '</notify_url><call_back_url>' . $call_back_url . '</call_back_url><seller_account_name>' . trim($alipay_config['seller_email']) . '</seller_account_name><out_trade_no>' . $out_trade_no . '</out_trade_no><subject>' . $subject . '</subject><total_fee>' . $total_fee . '</total_fee><merchant_url>' . $merchant_url . '</merchant_url></direct_trade_create_req>';
        //必填
        
        /************************************************************/

        //构造要请求的参数数组，无需改动
        $para_token = array(
            "service" => "alipay.wap.trade.create.direct",
            "partner" => trim($alipay_config['partner']),
            "sec_id" => trim($alipay_config['sign_type']),
            "format"	=> $format,
            "v"	=> $v,
            "req_id"	=> $req_id,
            "req_data"	=> $req_data,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );
        
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestHttp($para_token);
        
        //URLDECODE返回的信息
        $html_text = urldecode($html_text);
        
        //解析远程模拟提交后返回的信息
        $para_html_text = $alipaySubmit->parseResponse($html_text);
        
        //获取request_token
        $request_token = $para_html_text['request_token'];
        
        
        /**************************根据授权码token调用交易接口alipay.wap.auth.authAndExecute**************************/
        
        //业务详细
        $req_data = '<auth_and_execute_req><request_token>' . $request_token . '</request_token></auth_and_execute_req>';
        //必填
        
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "alipay.wap.auth.authAndExecute",
            "partner" => trim($alipay_config['partner']),
            "sec_id" => trim($alipay_config['sign_type']),
            "format"	=> $format,
            "v"	=> $v,
            "req_id"	=> $req_id,
            "req_data"	=> $req_data,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );
        
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter, 'get', '确认');
        
        return new ViewModel('redirect_script',$html_text);
    }
}