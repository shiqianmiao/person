<?php
class String
{
	/**
	 * 截字(utf8)
	 * @param $sourceString 被截取的字符串
	 * @param $length		截取的长度
	 * @param $offset		开始截取的位置，从0开始
	 * @return string		截取得到的字符串
	 */
	public static function trword($sourceString, $maxLength, $postFix='...')
	{
		if( self::strlen_utf8($sourceString)>$maxLength )
		{
			return self::substr_utf8($sourceString, $maxLength, 0) . $postFix;
		}
		return $sourceString;
	}
	
	/**
	 * 截取utf-8编码的字符串
	 * @param $sourceString 被截取的字符串
	 * @param $length		截取的长度
	 * @param $offset		开始截取的位置，从0开始
	 * @return string		截取得到的字符串
	 */
	public static function substr_utf8($sourceString, $length, $offset = 0)
	{
    	return mb_substr($sourceString, $offset, $length, 'utf-8');
	}
	
	/**
	 * 查找字符串位置
	 *
	 * @param sring $sourceString 被查找的字符串
	 * @param string $needle 	  查找字符串
	 * @param int $offset   	  开始查找的位置，从0开始
	 * @return 若存在返回字符串位置，否则返回false
	 */
	public static function strpos_utf8($sourceString, $needle, $offset = 0)
	{
    	return mb_strpos($sourceString, $needle, $offset, 'utf-8');
	}
	
	/**
	 * 获得utf-8编码的字符串的长度
	 * @param $sourceString	utf-8编码的字符串
	 * @return int			长度
	 */
	public static function strlen_utf8($sourceString)
	{
		//环境检查不要在runtime中处理  2009-08-03 3:13 PM zjy
		/*
		if(!extension_loaded('mbstring') )
			die('php-mbstring should be install ');
		*/
		
		//换行符修改为一字节 和JS判断相一致 修改人：刘必坚 修改时间：2009.2.18
		$str = str_replace("\r\n"," ",$sourceString);
		$str = stripslashes($str);
		
	    return mb_strlen($str, 'utf-8');
	}
	
	/**
	 * 检查一个字符串是否以特定字符串开始
	 * @param $sourceString	待检查的字符串
	 * @param $prefix		特定字符串
	 * @return boolean		如果是，返回true，否则返回false
	 */
	public static function beginWith($sourceString, $prefix)
	{
		if($prefix == '' || $sourceString == '')
			return false;

		return (@substr_compare($sourceString, $prefix, 0, strlen($prefix)) === 0);
	}
	
	/**
	 * 检查一个字符串是否以特定字符串结尾
	 * @param $sourceString	待检查的字符串
	 * @param $postfix		特定字符串
	 * @return boolean		如果是，返回true，否则返回false
	 */
	public static function endWith($sourceString, $postfix)
	{
		if($postfix == '' || $sourceString == '')
			return false;

		$size = strlen($postfix);
		return (@substr_compare($sourceString, $postfix, strlen($sourceString) - $size, $size) === 0);
	}
	
	/**
	 * 检查一段utf-8编码的字符串是否为中文
	 * @param $sourceString	被检查的字符串
	 * @return boolean		如果是，返回true，否则返回false
	 */
	public static function isChinese_utf8($sourceString)
	{
		return preg_match('/^[\x7f-\xff]+$/', $sourceString);
	}
	
	/**
	 * 将相差timestamp转为如“1分钟前”，“3天前”等形式
	 *
	 * @param timestamp $ts_diff 当前时间 - 要格式化的timestamp
	 */
	public static function formatTime($ts_diff)
	{
		if ($ts_diff <=0)
		{
			return date('Y-m-d');
		}
		else if ( $ts_diff <= 3600 )
		{
			return max(1, (int)($ts_diff/60)) . '分钟前';
		}
		else if ( $ts_diff <= 86400 )
		{
			return ((int)($ts_diff/3600)) . '小时前';
		}
		else
		{
			return ((int)($ts_diff/86400)) . '天前';
		}
	}
	/**
	 * 
	 *将timestamp时间转化为x时x分x秒
	 * 
	 */
    public static function getTimeLong($seconds) {
        if (!$seconds) {
            return '0秒';
        }
        $ret = '';
        if ($seconds >= 3600) {
            $hours = (int)($seconds / 3600);
            $seconds = $seconds % 3600;
            if ($hours) {
                $ret .= ($hours . '时');
            }
        }
        if ($seconds >= 60) {
            $mi = (int)($seconds / 60);
            $seconds = $seconds % 60;
            if ($mi) {
                $ret .= ($mi . '分');
            }
        }
        if ($seconds) {
            $ret .= ($seconds . '秒');
        }
        return $ret;
    }

	
	/**
	 * 获取unix时间戳秒数
	 *
	 * @return float
	 */
	public static function microtime_float()
	{
	    list($usec, $sec) = explode(" ", microtime());
	    return ((float)$usec + (float)$sec);
	}
	
	/**
     * 格式化为安全字符串(用于FCK编辑器提交后的内容清理中)
     *
     * @var string $str 字符串
     * @return string 返回完全的字符串
     */
	public function formatSafeStr($str)
    {
    	include_once dirname(__FILE__) . "/RichText.class.php";

		$richText = new RichText();
		return $richText->filter($str);
    }
    /** 将数字星期转换成字符串星期 weekNum2String($num)
     * @param int
     * @return string
     */
    public static function weekNum2String($num){
        switch($num){
            case 1:
                return '星期一';
            case 2:
                return '星期二';
            case 3:
                return '星期三';
            case 4:
                return '星期四';
            case 5:
                return '星期五';
            case 6:
                return '星期六';
            case 7:
                return '星期日';
            default:
                return '未知';
        }
    }
    /** 进行防注入检查 _gpc($g)
     * @param  string $g   要检查的变量
     * @return string
     */
    public static function gpc($g){
        $gpc = get_magic_quotes_gpc();
        if (!$gpc) {
            $g = addslashes($g);
        }
        return trim($g);
    }
    /** 生成数字,大小写字母组成的任意位数的字符串 random($min_len,$max_len,$t)
     * @param  $min_len  字符串最小长度
     * @param  $max_len    字符串最大长度
     * @param  $type       生成的字符串类别
     *                     0为全部小写和数字的组合,1为全部大写和数字的组合,3为全部数字的组合,4为大小写字母的组合
     * @return string
     */
    public static function random($min_len=9,$max_len=9,$type=3){
        $str_len = mt_rand($min_len,$max_len);
        $ps = "";
        while(strlen($ps) < $str_len){
            $r = array(
                    mt_rand(49,57),//1-9的ASCII码
                    mt_rand(65,90),//A-Z的ASCII码
                    mt_rand(97,122)//a-z的ASCII码
                    );
            if($type == 3) $tmp = chr($r[mt_rand(0,0)]);
            else $tmp = chr($r[mt_rand(0,2)]);
            if($type == 0)
                $tmp = strtolower($tmp);
            else
                $tmp = $type==1?strtoupper($tmp):$tmp;
            $ps .= $tmp;
        }
        return trim($ps);
    }

    /**
    * @author Chunsheng Wang
    * @param string $String the string to cut.
    * @param int $Length the length of returned string.
    * @param booble $Append whether append "...": false|true
    * @return string the cutted string.
    */
    public static function sysSubStr($String,$Length,$Append = false)
    {
        $StringLast = array();
        if (strlen($String) <= $Length )
        {
            return $String;
        }
        else
        {
            $I = 0;
            while ($I < $Length){
                $StringTMP = substr($String,$I,1);
                if ( ord($StringTMP) >=224 ){
                    $StringTMP = substr($String,$I,3);
                    $I = $I + 3;
                }elseif( ord($StringTMP) >=192 ){
                    $StringTMP = substr($String,$I,2);
                    $I = $I + 2;
                }
                else{
                    $I = $I + 1;
                }
                $StringLast[] = $StringTMP;
            }
            $StringLast = implode("",$StringLast);
            if($Append){
                $StringLast .= "...";
            }
            return $StringLast;
        }
    }
}
