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

        $staHtml .= 'g.js,config.js,app/person/web/css/index_v2.css';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));

        $this->render(array(
            'staHtml' => $staHtml,
        ), 'default.php');

    }

    /**
     *@desc resume Page
     */
    function resumeAction() {

        $staHtml .= 'g.js,config.js,app/person/web/css/index_v2.css';
        $staHtml = $this->view->helper('sta', array('files' => $staHtml));

        $this->render(array(
            'staHtml' => $staHtml,
        ), 'resume.php');

    }
}

