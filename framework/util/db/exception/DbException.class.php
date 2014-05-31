<?php
/**
 * DbException 数组库异常类
 *
 * @copyright Copyright (c) 2006 - 2008 MYRIS.CN
 * @author 戴志君 <dzjzmj@gmail.com>
 * @package db
 * @version v0.1
 */

/**
 * DbException 数组库异常类
 * @package db
 */
class DbException extends Exception {
	/**#@+
	 * WEIP 数据库连接及查询错误类型
	 */
	const DB_ERROR								= 0 ;
	const DB_OPEN_FAILED						= 1 ;
	const DB_UNCONNECTED						= 1 ;
	const DB_QUERY_ERROR						= 10 ;
	const DB_RECORD_IS_EXISTED					= 11 ;
	/**#@-*/

	/**
	 * 异常类型
	 * @var int $type
	 */
	public $type = self::DB_ERROR;

	/**
	 * 构造函数
	 * @param string $msg 异常信息
	 * @param string $type 异常类型
	 */
    function __construct($msg, $type = self::DB_ERROR) {
        parent::__construct($msg,$type);
		$this->type = $type;
    }
}