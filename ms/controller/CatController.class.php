<?PHP
class CatController extends MsController {
    /**
     * @brief 分类列表
     */
    public function defaultAction() {
        $staHtml = 'app/agency/js/animate_head.js'
            .',app/agency/css/common.css'
            .',app/agency/css/index.css'
            .',app/agency/css/list.css'
            .',app/agency/css/slider-pro.min.css'
            .',app/agency/js/jquery.sliderPro.min.js';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));
        $data = array(
            'staHtml' => $staHtml,
        );
        $tpl = 'cat/index.php';
        $content = $this->view->fetch($tpl, $data);
        echo $content;
    }
}