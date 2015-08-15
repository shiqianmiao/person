<?PHP
class DetailController extends MsController {
    
    public function defaultAction() {
        $staHtml = 'app/agency/js/animate_head.js'
            .',app/agency/css/common.css'
            .',app/agency/css/detail.css'
            .',app/agency/css/slider-pro.min.css'
            .',app/agency/js/jquery.sliderPro.min.js';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));
        $data = array(
            'staHtml' => $staHtml,
        );
        $tpl = 'detail/detail.php';
        $content = $this->view->fetch($tpl, $data);
        echo $content;
    }
}