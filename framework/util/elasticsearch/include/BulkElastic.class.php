<?php
/**
 * @package              v3
 * @subpackage           
 * @author               $Author:   weihongfeng$
 * @file                 $HeadURL$
 * @version              $Rev$
 * @lastChangeBy         $LastChangedBy$
 * @lastmodified         $LastChangedDate$
 * @copyright            Copyright (c) 2013, www.273.cn
 */
class BulkElastic {
    /*
     *Client实体
     */
    private $client;

    private $operations = array();

    public function __construct($client) {
        $this->client = $client;
    }

    /*
     *提交批量事件
     */
    public function commit() {
		return $this->client->request('/_bulk', 'POST', $this->createPayload());
    }
    /*
     *一次索引添加或更新，实际并没有执行，需要等待提交
     */
	public function index($document, $id=false, $index, $type, array $options = array()) {
		$params = array( '_id' => $id, 
   					     '_index' => $index, 
					     '_type' => $type);

		foreach ($options as $key => $value) {
			$params['_' . $key] = $value;
		}

		$operation = array(
			array('index' => $params),
			$document 
		);
		$this->operations[] = $operation;
		return $this;
	}
    /*
     *一次索引的删除，实际并没有执行，需要等待提交
     */
	public function delete($id=false, $index, $type, array $options = array()) {
		$params = array( '_id' => $id, 
   					     '_index' => $index, 
					     '_type' => $type);

		foreach ($options as $key => $value) {
			$params['_' . $key] = $value;
		}

		$operation = array(
			array('delete' => $params)
		);
		$this->operations[] = $operation;
		return $this;
	}

    /*
     *获取当前累积的需要提交的操作
     */
    public function getOperations() {
        return $this->operations;
    }


    /*
     *获取当前累积的需要提交的操作的总数
     */
    public function countOperations() {
        return count($this->operations);
    }
    /*
     *构造post信息
     */
	public function createPayload() {
		$payloads = array();
		foreach ($this->operations as $operation) {
			foreach ($operation as $partial) {
				$payloads[] = json_encode($partial);
			}
		}
		return join("\n", $payloads) . "\n";
	}
}
