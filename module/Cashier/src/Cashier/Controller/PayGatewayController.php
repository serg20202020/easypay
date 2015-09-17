<?php
namespace Cashier\Controller;

use Zend\View\Model\ViewModel;
use Cashier\Model\Alipay\AlipaySubmit;
use Cashier\Model\Alipay\AlipayNotify;
use Setting\Model\PaymentInterface;
use Cashier\Model\Log;

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
        
        $this->appendTitle('收银台');
        $this->layout()->setVariable('is_cashier_page', true);
        
        /**
         * Get the trade info data.
         */
        @session_start();
        //print_r($_SESSION['paying_trade']);
        $paying_trade = $_SESSION['paying_trade'];
        
        /**
         * Get the configuration of alipay payment interface.
         */
        $alipay_config = $this->alipay_getconfig();
        
        
        $domain_url = 'http://'.$_SERVER['HTTP_HOST'];
        
        
        
        /* *
         * 功能：手机网站支付接口接入页
         * 版本：3.3
         * 修改日期：2012-07-23
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
         * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
        
         *************************注意*************************
         * 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决
         * 1、商户服务中心（https://b.alipay.com/support/helperApply.htm?action=consultationApply），提交申请集成协助，我们会有专业的技术工程师主动联系您协助解决
         * 2、商户帮助中心（http://help.alipay.com/support/232511-16307/0-16307.htm?sh=Y&info_type=9）
         * 3、支付宝论坛（http://club.alipay.com/read-htm-tid-8681712.html）
         * 如果不想使用扩展功能请把扩展功能参数赋空值。
         */
        
        
        /**************************请求参数**************************/
        
        //支付类型
        $payment_type = "1";
        //必填，不能修改
        //服务器异步通知页面路径
        $notify_url = $domain_url.$this->url()->fromRoute('cashier/gateway',array('action'=>'alipaynotify'));
        //需http://格式的完整路径，不能加?id=123这类自定义参数
        
        //页面跳转同步通知页面路径
        $return_url = $domain_url.$this->url()->fromRoute('cashier/gateway',array('action'=>'alipayredirect'));
        //需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/
        
        //商户订单号
        $out_trade_no = $paying_trade['merchant_trade_id'];
        //商户网站订单系统中唯一订单号，必填
        
        //订单名称
        $subject = '商户订单【'.$paying_trade['merchant_trade_id'].'】';
        //必填
        
        //付款金额
        $total_fee = $paying_trade['price'];
        //必填
        
        //商品展示地址
        $show_url = $domain_url;
        //必填，需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html
        
        //订单描述
        $body = $subject;
        //选填
        
        //超时时间
        $it_b_pay = '';
        //选填
        
        //钱包token
        $extern_token = '';
        //选填
        
        
        /************************************************************/
        
        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "alipay.wap.create.direct.pay.by.user",
            "partner" => trim($alipay_config['partner']),
            "seller_id" => trim($alipay_config['seller_id']),
            "payment_type"	=> $payment_type,
            "notify_url"	=> $notify_url,
            "return_url"	=> $return_url,
            "out_trade_no"	=> $out_trade_no,
            "subject"	=> $subject,
            "total_fee"	=> $total_fee,
            "show_url"	=> $show_url,
            "body"	=> $body,
            "it_b_pay"	=> $it_b_pay,
            "extern_token"	=> $extern_token,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );
        
        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        
        
        return new ViewModel(array('redirect_script'=>$html_text));
    }

    public function alipaynotifyAction() {
        
        $alipay_config = $this->alipay_getconfig();
        
        $log_file_path = 'data/log/alipay_notify.txt';
        $log_content = '';
        $Log = new Log();
        
        $log_content .= "【 接收到了支付宝异步通知 】\r\n";
        
        
        /* *
         * 功能：支付宝服务器异步通知页面
         * 版本：3.3
         * 日期：2012-07-23
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
         * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
        
        
         *************************页面功能说明*************************
         * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
         * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
         * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
         * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
         */
        
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
            
            
            $log_content .= "该通知[通过了]RAS验证\r\n";
            
            
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
            
            $log_content .= "订单状态为：".$_POST['trade_status']."\r\n";
            
        }
        else {
            //验证失败
            echo "fail";
        
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            
            $log_content .= "该通知[没有通过]RAS验证\r\n";
        }
        
        $log_content .= "通知的完整POST数据：".var_export($_POST,true);
        
        $Log->toFile($log_file_path, $log_content);
        
        exit();
        
    }

    public function alipayredirectAction() {
        
        $alipay_config = $this->alipay_getconfig();
        
        $log_file_path = 'data/log/alipay_redirect.txt';
        $log_content = '';
        $Log = new Log();
        
        $log_content .= "【 请求了支付宝跳转通知页面 】\r\n";
        
        
        /* *
         * 功能：支付宝页面跳转同步通知页面
         * 版本：3.3
         * 日期：2012-07-23
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
         * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
        
         *************************页面功能说明*************************
         * 该页面可在本机电脑测试
         * 可放入HTML等美化页面的代码、商户业务逻辑程序代码
         * 该页面可以使用PHP开发工具调试，也可以使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyReturn
         */
        
        
        //计算得出通知验证结果
        $alipayNotify = new AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyReturn();
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代码
        
            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——
            //获取支付宝的通知返回参数，可参考技术文档中页面跳转同步通知参数列表
        
            //商户订单号
        
            $out_trade_no = $_GET['out_trade_no'];
        
            //支付宝交易号
        
            $trade_no = $_GET['trade_no'];
        
            //交易状态
            $trade_status = $_GET['trade_status'];
            
            
            $log_content .= "该跳转[通过了]RAS验证\r\n";
        
        
            if($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
                //判断该笔订单是否在商户网站中已经做过处理
                //如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
                //如果有做过处理，不执行商户的业务程序
            }
            else {
                echo "trade_status=".$_GET['trade_status'];
            }
        
            echo "验证成功<br />";
        
            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——
        
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            
            $log_content .= "订单状态为：".$_GET['trade_status']."\r\n";
            
        }
        else {
            //验证失败
            //如要调试，请看alipay_notify.php页面的verifyReturn函数
            echo "验证失败";
            
            $log_content .= "该跳转[没有通过]RAS验证\r\n";
        }
        
        
        $log_content .= "请求的完整GET数据：".var_export($_GET,true);
        
        $Log->toFile($log_file_path, $log_content);
        
        exit();

    }
    
    /**
     * Read config from DB.
     * @return Ambigous <string, unknown>
     */
    private function alipay_getconfig() {
        
        $PaymentInterface = new PaymentInterface(PaymentInterface::PAYMENT_TYPE_ALIPAY, $this->getServiceLocator());

        /* *
         * 配置文件
         * 版本：3.3
         * 日期：2012-07-19
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
         * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。
        
         * 提示：如何获取安全校验码和合作身份者id
         * 1.用您的签约支付宝账号登录支付宝网站(www.alipay.com)
         * 2.点击“商家服务”(https://b.alipay.com/order/myorder.htm)
         * 3.点击“查询合作者身份(pid)”、“查询安全校验码(key)”
        
         * 安全校验码查看时，输入支付密码后，页面呈灰色的现象，怎么办？
         * 解决方法：
         * 1、检查浏览器配置，不让浏览器做弹框屏蔽设置
         * 2、更换浏览器或电脑，重新登录查询。
         */
        
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= $PaymentInterface->merchant_id;
        
        //收款支付宝账号
        $alipay_config['seller_id']	= $alipay_config['partner'];
        
        //商户的私钥（后缀是.pen）文件相对路径
        $alipay_config['private_key_path']	= 'data/alipay/key/rsa_private_key.pem';
        
        //支付宝公钥（后缀是.pen）文件相对路径
        $alipay_config['ali_public_key_path']= 'data/alipay/key/alipay_public_key.pem';
        
        
        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        
        
        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('RSA');
        
        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');
        
        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = 'data/alipay/cacert/cacert.pem';
        
        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';
        
        
        
        
        
        
        return $alipay_config;
    }
}