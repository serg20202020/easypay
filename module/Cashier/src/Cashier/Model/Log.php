<?php
namespace Cashier\Model;

class Log
{
    // 打印log
    public function toFile($file,$word)
    {
        $fp = fopen($file,"a");
        flock($fp, LOCK_EX) ;
        fwrite($fp,"[ ".date("Y-m-d H:i:s",time())." ]\r\n==================== Log Start ====================\r\n".$word."\r\n==================== Log End ====================\r\n\r\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }
}

?>