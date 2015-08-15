<?php
/**
 * 密码强度检测
 * @see https://github.com/davestj/check_thatpass
 *
 * @author 王煜 <wangyu@273.cn>
 * @since 2014-09-16
 *
 **/
class PasswordStrength {

    public      $password = '';
    protected   $desc     = array(array());
    public      $score    = int;
    private     $strpass  = string;

    /**
     * 检测密码强度
     * @param   stirng  $password   明文密码
     * @param   boolean $returnRank 是否返回等级制，默认true
     **/
    public function check($password, $returnRank = true){
        $this->password = $password;

        $this->desc[-1] = 'Terrible';
        $this->desc[0]  = 'Super Weak';
        $this->desc[1]  = 'Weak Sauce';
        $this->desc[2]  = 'Meh, could be better';
        $this->desc[3]  = 'Sufficient';
        $this->desc[4]  = 'Acceptable';
        $this->desc[5]  = 'Solid';
        $this->desc[6]  = 'Best';

        $this->score   = 0;
        $this->strpass = strlen($this->password);

        //if password bigger than 6 give 1 point
        if($this->strpass > 6) {
            $this->score++;
        }

        //if password bigger than 8 give 1 point
        if($this->strpass > 8) {
            $this->score++;
        }

        //if password bigger than 12 give 1 point
        if($this->strpass > 12) {
            $this->score++;
        }

        //if password less than 6 characters take away 1 point
        if($this->strpass < 6) {
            $this->score--;
        }

        //if password has both lower and uppercase characters give 1 point
        if(preg_match("/[a-z]/", $this->password) && preg_match("/[A-Z]/", $this->password)) {
             $this->score++;
        }

        //deduct points for stupid paterns
        if(preg_match("/^123/", $this->passwordd)
            || preg_match("/^1234567/",$this->password)
            || preg_match("/^lloveyou/",$this->password)
            || preg_match("/^6543210/",$this->password)) {
            $this->score--;
        }

        //if password has at least one number give 1 point
         if(preg_match("/\d+/", $this->password)) {
             $this->score++;
        }

        //if password has at least one special caracther give 1 point
         if(preg_match("/.[!,@,#,$,%,^,&,*,?,_,~,-,(,)]/", $this->password)) {
             $this->score++;
         }

        return $returnRank
               ? $this->_getRank()
               : array($this->score => $this->desc[$this->score]);
    }

    /**
     * 获取相应的等级 1:低 2:中 3:高
     **/
    private function _getRank() {
        $weak   = array(-1, 0, 1);
        $normal = array(2, 3, 4);
        $strong = array(5, 6);
        if (in_array($this->score, $weak)) {
            return 1;
        } else if (in_array($this->score, $normal)) {
            return 2;
        } else if (in_array($this->score, $strong)){
            return 3;
        } else {
            return 1;
        }
    }

}

//EXAMPLE CASE USAGE
//$_pass   = 'gTi678Kdl0D';
//$passobj = new PasswordStrength();
//$rank  = $passobj->check($_pass);
//echo $rank;
