<?php

require_once WEB_V3 . '/common/WebBasePage.class.php' ;
//require_once(FRAMEWORK_PATH. '/util/gearman/ClientGearman.class.php');

/**
 *
 * @author    miaoshiqian
 * @since     2013-3-7
 * @desc      
 */
class DefaultPage extends WebBasePage{
    /**
     *@desc home Page
     */
    function defaultAction() {

        $staHtml .= 'app/agency/js/animate_head.js,app/agency/css/white.css,app/agency/css/slider-pro.min.css,app/agency/js/jquery.sliderPro.min.js';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));

        $this->render(array(
            'staHtml' => $staHtml,
        ), 'default_v2.php');

    }

    /**
     *@desc resume Page
     */
    function resumeAction() {

        $staHtml .= 'app/person/web/css/index_v2.css, app/person/web/css/resume.css';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));

        $this->render(array(
            'staHtml' => $staHtml,
        ), 'resume.php');

    }
}

