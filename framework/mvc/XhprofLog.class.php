<?php
class XhprofLog {
    public static $IS_XHPROF = false;

    public static function beginXhprof($rand=300) {
        if (mt_rand(1, $rand) == 1 || DEBUG_STATUS) {
            if (function_exists('xhprof_enable')) {
                xhprof_enable(XHPROF_FLAGS_CPU + XHPROF_FLAGS_MEMORY);
                self::$IS_XHPROF = true;
            }
        }
    }

    public static function logXhprof($flag) {
        if (self::$IS_XHPROF) {
            self::$IS_XHPROF = false;
            $data = xhprof_disable();
            if ($data['main()']['wt'] > 500000) {
                include_once FRAMEWORK_PATH . '/util/xhprof/xhprof_lib/utils/xhprof_lib.php';
                include_once FRAMEWORK_PATH . '/util/xhprof/xhprof_lib/utils/xhprof_runs.php';
                $objXhprofRun = new XHProfRuns_Default();
                $objXhprofRun->save_run($data, $flag);
            }
        }
    }

}
