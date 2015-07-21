<?php
namespace Cashier\Controller;

use Zend\View\Model\ViewModel;
use Cashier\Model\Alipay\AlipaySubmit;
use Cashier\Model\Alipay\AlipayNotify;
use Setting\Model\PaymentInterface;

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
        @session_start();
        print_r($_SESSION['paying_trade']);
        $paying_trade = $_SESSION['paying_trade'];
        
        /**
         * Get the configuration of alipay payment interface.
         */
        $alipay_config = $this->alipay_getconfig();
        	
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
        
        $domain_url = 'http://'.$_SERVER['HTTP_HOST'];
        //服务器异步通知页面路径
        $notify_url = $domain_url.$this->url()->fromRoute('cashier/gateway',array('action'=>'alipay_notify'));
        //需http://格式的完整路径，不允许加?id=123这类自定义参数
        
        //页面跳转同步通知页面路径
        $call_back_url = $domain_url.$this->url()->fromRoute('cashier/gateway',array('action'=>'alipay_redirect'));
        //需http://格式的完整路径，不允许加?id=123这类自定义参数
        
        //操作中断返回地址
        $merchant_url = $domain_url.$this->url()->fromRoute('cashier/cancel');
        //用户付款中途退出返回商户的地址。需http://格式的完整路径，不允许加?id=123这类自定义参数
        
        //商户订单号
        $out_trade_no = $paying_trade['merchant_trade_id'];
        //商户网站订单系统中唯一订单号，必填
        
        //订单名称
        $subject = '商户订单【'.$paying_trade['merchant_trade_id'].'】';
        //必填
        
        //付款金额
        $total_fee = $paying_trade['price'];
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
        
        return new ViewModel(array('redirect_script'=>$html_text));
    }

    public function alipay_notifyAction() {
        
        $alipay_config = $this->alipay_getconfig();
        
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
        
        
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
        
            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表
        
            //商户订单号
        
            $out_trade_no = $_POST['out_trade_no'];
        
            //支付宝交易号
        
            $trade_no = $_POST['trade_no'];
        
            //交易状态
            $trade_status = $_POST['trade_status'];
        
        
            if($_POST['trade_status'] == 'TRADE_FINISHED') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
        
                //注意：
                //退款日期超过可退款期限后（如三个月可退款），支付宝系统发送该交易状态通知
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
            else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
        
                //注意：
                //付款完成后，支付宝系统发送该交易状态通知
        
                //调试用，写文本函数记录程序运行情况是否正常
                //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            }
        
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
            echo "success";		//请不要修改或删除
        
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "fail";
        
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
        }
        
    }

    public function alipay_redirectAction() {
        ;
    }
    
    /**
     * Read config from DB.
     * @return Ambigous <string, unknown>
     */
    private function alipay_getconfig() {
        
        $PaymentInterface = new PaymentInterface(PaymentInterface::PAYMENT_TYPE_ALIPAY, $this->getServiceLocator());
        
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= $PaymentInterface->merchant_id;
        
        //收款支付宝帐户
        $alipay_config['seller_email']	= $PaymentInterface->account;
        
        //安全检验码，以数字和字母组成的32位字符
        //如果签名方式设置为“MD5”时，请设置该参数
        $alipay_config['key']			= $PaymentInterface->api_key;
        
        
        //商户的私钥（后缀是.pem）文件相对路径
        //如果签名方式设置为“0001”时，请设置该参数
        $alipay_config['private_key_path']	= 'data/alipay/key/rsa_private_key.pem';
        
        //支付宝公钥（后缀是.pem）文件相对路径
        //如果签名方式设置为“0001”时，请设置该参数
        $alipay_config['ali_public_key_path']= 'data/alipay/key/alipay_public_key.pem';
        
        
        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        
        
        //签名方式 不需修改
        $alipay_config['sign_type']    = 'MD5';
        
        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= 'utf-8';
        
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = 'data/alipay/cacert/cacert.pem';
        
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';
        
        /**************************调用授权接口alipay.wap.trade.create.direct获取授权码token**************************/
        
        return $alipay_config;
    }
}