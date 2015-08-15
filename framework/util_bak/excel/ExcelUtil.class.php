<?php

/**
 * @brief         exportExecl 导出其列表execl
 * @author        zhuangjx<zhuangjx@273.cn>
 * @date          2013-12-30
 */
class ExcelUtil
{

    public static $objPHPExcel = null;

    /**
     *
     * @param array $info
     *            array('contract_no'=>590532415421)
     * @param array $columnName
     *            array(''合同编号','车牌号',...)
     * @param string $fileName            
     * @param inde $index            
     */
    public static function export ($info, $columnName, $fileName, $index = 0)
    {
        // 创建一个工作表
        include_once FRAMEWORK_PATH . '/extend/phpexcel/PHPExcel.php';
        
        self::$objPHPExcel = new PHPExcel();
        self::$objPHPExcel->createSheet($index);
        if ($info) {
            // 导入Excel
            $sheet = self::$objPHPExcel->setActiveSheetIndex($index);
            $i = 1;
            $t = 0;
            foreach ($columnName as $key => $value) {
                $sheet->setCellValue(chr(65 + $t) . $i, $value);
                $t ++;
            }
            $i ++;
            self::$objPHPExcel->getActiveSheet($index)
                ->getDefaultColumnDimension()
                ->setWidth(16);
            self::$objPHPExcel->getActiveSheet($index)
                ->getDefaultRowDimension()
                ->setRowHeight(16);
            // 导出到execl中
            
            foreach ($info as $in) {
                $t = 0;
                foreach ($in as $key => $val) {
                    $sheet->setCellValue(chr(65 + $t) . $i, strip_tags($val));
                    $t ++;
                }
                $i ++;
            }
            self::$objPHPExcel->setActiveSheetIndex($index);
            self::$objPHPExcel->getActiveSheet()->setTitle($fileName);
            $fileName = '' . $fileName . '.xls';
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="' . $fileName .
                     '"');
            header('Cache-Control: max-age=0');
            $objWriter = PHPExcel_IOFactory::createWriter(self::$objPHPExcel, 
                    'Excel5');
            $objWriter->save('php://output');
            exit();
        }
    }
}