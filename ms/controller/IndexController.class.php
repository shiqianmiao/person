<?PHP
class IndexController extends MsController {
    
    public function defaultAction() {
        $staHtml = 'app/agency/js/animate_head.js'
                    .',app/agency/css/common.css'
                    .',app/agency/css/index.css'
                    .',app/agency/css/slider-pro.min.css'
                    .',app/agency/js/jquery.sliderPro.min.js';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));
        $data = array(
            'staHtml' => $staHtml,
        );
        $tpl = 'index/index.php';
        $content = $this->view->fetch($tpl, $data);
        echo $content;
    }

    /**
     * @brief 网站时间轴
     */
    public function logAction() {
        $staHtml = 'app/agency/js/animate_head.js'
            .',app/agency/css/common.css'
            .',app/agency/css/index.css';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));
        $data = array(
            'staHtml' => $staHtml,
        );
        $tpl = 'index/log.php';
        $content = $this->view->fetch($tpl, $data);
        echo $content;
    }
}