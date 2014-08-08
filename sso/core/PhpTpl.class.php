<?php

/**
 * @brief 模板引擎
 * @author aozhongxu
 */

class PhpTpl {

    /**
     * @brief 解析模板
     */
    public function fetch($tplFile, $paramArray = array()) {
        if (!empty($paramArray)) {
            foreach ($paramArray as $key => $value) {
                $this->$key = $value;
            }
        }
        ob_start();
        include SSO_SITE . '/template/' . $tplFile;
        $ret = ob_get_contents();
        ob_end_clean();
        return $ret;
    }

}
