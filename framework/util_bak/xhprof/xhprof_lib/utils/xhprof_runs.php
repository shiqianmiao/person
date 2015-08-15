<?php
//
//  Copyright (c) 2009 Facebook
//
//  Licensed under the Apache License, Version 2.0 (the "License");
//  you may not use this file except in compliance with the License.
//  You may obtain a copy of the License at
//
//      http://www.apache.org/licenses/LICENSE-2.0
//
//  Unless required by applicable law or agreed to in writing, software
//  distributed under the License is distributed on an "AS IS" BASIS,
//  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
//  See the License for the specific language governing permissions and
//  limitations under the License.
//

//
// This file defines the interface iXHProfRuns and also provides a default
// implementation of the interface (class XHProfRuns).
//

/**
 * iXHProfRuns interface for getting/saving a XHProf run.
 *
 * Clients can either use the default implementation,
 * namely XHProfRuns_Default, of this interface or define
 * their own implementation.
 *
 * @author Kannan
 */
require_once FRAMEWORK_PATH . '/util/gearman/ClientGearman.class.php';
require_once API_PATH . '/model/PhpLogModel.class.php';
interface iXHProfRuns {

  /**
   * Returns XHProf data given a run id ($run) of a given
   * type ($type).
   *
   * Also, a brief description of the run is returned via the
   * $run_desc out parameter.
   */
  public function get_run($run_id, $type, &$run_desc);

  /**
   * Save XHProf data for a profiler run of specified type
   * ($type).
   *
   * The caller may optionally pass in run_id (which they
   * promise to be unique). If a run_id is not passed in,
   * the implementation of this method must generated a
   * unique run id for this saved XHProf run.
   *
   * Returns the run id for the saved XHProf run.
   *
   */
  public function save_run($xhprof_data, $type, $run_id = null);
}


/**
 * XHProfRuns_Default is the default implementation of the
 * iXHProfRuns interface for saving/fetching XHProf runs.
 *
 * It stores/retrieves runs to/from a filesystem directory
 * specified by the "xhprof.output_dir" ini parameter.
 *
 * @author Kannan
 */
class XHProfRuns_Default implements iXHProfRuns {

  private $dir = '';
  private $suffix = 'xhprof';

  private function gen_run_id($type) {
    return uniqid();
  }

  private function file_name($run_id, $type) {

    $file = "$run_id.$type." . $this->suffix;

    if (!empty($this->dir)) {
      $file = $this->dir . "/" . $file;
    }
    return $file;
  }

  public function __construct($dir = null) {

    // if user hasn't passed a directory location,
    // we use the xhprof.output_dir ini setting
    // if specified, else we default to the directory
    // in which the error_log file resides.

    if (empty($dir)) {
      $dir = ini_get("xhprof.output_dir");
      if (empty($dir)) {

        // some default that at least works on unix...
        $dir = "/tmp";

        xhprof_error("Warning: Must specify directory location for XHProf runs. ".
                     "Trying {$dir} as default. You can either pass the " .
                     "directory location as an argument to the constructor ".
                     "for XHProfRuns_Default() or set xhprof.output_dir ".
                     "ini param.");
      }
    }
    $this->dir = $dir;
  }

  public function get_run($run_id, $type, &$run_desc) {
    $model = new PhpLogModel();
    $contents = $model->getOne('content', array(array('id', '=', $run_id)));
    if (empty($contents)) {
      xhprof_error("Could not find $run_id file");
      $run_desc = "Invalid Run Id = $run_id";
      return null;
    }
    $run_desc = "XHProf Run (Namespace=$type)";
    return unserialize($contents);
  }

  public function save_run($xhprof_data, $type, $run_id = null) {

    // Use PHP serialize function to store the XHProf's
    // raw profiler data.
    $row = array();
    $row['all_time'] = $xhprof_data['main()']['wt'];
    $xhprof_data = serialize($xhprof_data);
    $row['insert_time'] = time();
    $row['type'] = $type;
    $row['content'] = $xhprof_data;
    $row['url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; 
    try{
        $gearman = ClientGearman::instance();
        $gearman->doBackground('phpLog', $row);
    } catch (Exception $e) {
        xhprof_error("Could not log to gearman\n");
    }
    return true;
  }

  function list_runs() {
    $model = new PhpLogModel();
    $files = $model->getAll('*', array(), array('id' => 'desc'), 100, 0);  
    if (!empty($files)) {
        echo "<hr/>Existing runs:\n<ul>\n";
        foreach ($files as $file) {
            $run = $file['id'];
            $source = $file['type'];
            echo '<li><a href="' . htmlentities($_SERVER['SCRIPT_NAME'])
                . '?run=' . htmlentities($run) . '&source='
                . htmlentities($source) . '">'
                . $run . "." . $source . ".xhprof</a><small> "
                . date("Y-m-d H:i:s", $file['insert_time']) . "</small></li>\n";
        }
        echo "</ul>\n";
    }
  }
}
