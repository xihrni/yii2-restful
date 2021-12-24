<?php

namespace app\components;

use PHPExcel;
use PHPExcel_Cell;
use PHPExcel_IOFactory;
use PHPExcel_Shared_Date;
use PHPExcel_Reader_Excel5;
use PHPExcel_Reader_Excel2007;
use PHPExcel_Exception;
use PHPExcel_Reader_Exception;
use PHPExcel_Writer_Exception;

/**
 * EXCEL表格组件
 *
 * Class ExcelTableComponent
 * @package app\components
 */
class ExcelTableComponent extends \app\base\BaseComponent
{
    /**
     * 读取数据
     *
     * @param  string $file                 文件地址
     * @param  bool   [$isFilterRow = true] 是否过滤空行（整行都为空的数据）
     * @return array
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     */
    public function getData($file, $isFilterRow = true)
    {
        // 验证后缀
        $url    = explode('.', $file);
        $suffix = $url[count($url) - 1];
        if ($suffix == 'xls') {
            $reader = new PHPExcel_Reader_Excel5;
        } else if ($suffix == 'xlsx') {
            $reader = new PHPExcel_Reader_Excel2007;
        } else {
            throw new PHPExcel_Exception('file format is incorrect.');
        }

        $excel = $reader->load($file);
        $sheet = $excel->getSheet(0);
        $rows  = $sheet->getHighestRow(); // 行数
        $cols  = PHPExcel_Cell::columnIndexFromString($sheet->getHighestColumn()); // 列数

        // 字段
        $fields = [];
        for ($col = 0; $col < $cols; $col++) {
            $value = $sheet->getCellByColumnAndRow($col, 1)->getValue();
            if (is_object($value)) {
                $value = $value->__toString();
            }

            $fields[] = trim($value);
        }

        // 数据
        $data = [];
        for ($row = 2; $row <= $rows; $row++) {
            for ($col = 0; $col < $cols; $col++) {
                $value = $sheet->getCellByColumnAndRow($col, $row)->getValue();
                if (is_object($value)) {
                    $value = $value->__toString();
                }

                if (PHPExcel_Shared_Date::isDateTime($sheet->getCellByColumnAndRow($col, $row))) {
                    // 时间转换
                    date_default_timezone_set('Etc/GMT');
                    $value = date('Y-m-d H:i:s', PHPExcel_Shared_Date::ExcelToPHP($value));
                    date_default_timezone_set('Asia/Shanghai');
                }

                $data[$row - 1][$fields[$col]] = trim($value); // trim() 会将类型转换为字符串
            }

            // 过滤整行都是空的数据
            if ($isFilterRow && !array_filter($data[$row - 1])) {
                unset($data[$row - 1]);
            }
        }

        return $data;
    }

    /**
     * 导出CSV
     *
     * @param  array  $data     数据
     * @param  string $filename 文件名称
     * @return void
     */
    public function exportCsv($data, $filename)
    {
        $html = '';
        foreach ($data as $v) {
            $html .= implode(',', $v) . PHP_EOL;
        }

        // 输出 CSV文件
        header('Content-type: text/csv');
        header('Content-Disposition: attachment; filename=' . $filename . '.csv');
        echo $html;
        exit;
    }

    /**
     * 导出XLS
     *
     * @param  array  $data     数据
     * @param  string $filename 文件名称
     * @return bool|void
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public function exportXls($data, $filename)
    {
        if (!$data || !$data[0]) {
            return false;
        }

        // 列KEY数据准备
        $cellKey = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M',
            'N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM',
            'AN','AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        ];

        $excel = new PHPExcel;

        // 输出数据
        foreach ($data as $k => $v) {
            foreach ($v as $key => $value) {
                $excel->getActiveSheet()->setCellValue($cellKey[$key] . ($k + 1),$value);
            }
        }

        // 生成xls文件
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Cache-Control: max-age=0');
        $objWriter = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
        $objWriter->save('php://output');

        exit;
    }

    /**
     *  导出XLS使用原生方式
     *
     * @param  array  $data     数据
     * @param  string $filename 文件名称
     * @return void
     */
    public function exportXlsByNative($data, $filename)
    {
        $html = '';
        foreach ($data as $v) {
            $html .= implode("\t", $v) . "\n";
        }

        // 输出 xls文件
        ob_get_clean();
        ob_start();

        echo $html;

        header('Content-Disposition: attachment; filename=' . $filename . '.xls');
        header('Accept-Ranges: bytes');
        header('Content-Length: ' . ob_get_length());
        header('Content-Type: application/vnd.ms-excel');
        ob_end_flush();

        exit;
    }
}
