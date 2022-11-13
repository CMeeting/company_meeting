<?php
/**
 * Created by PhpStorm.
 * User: lzz
 * Date: 2019/12/9
 * Time: 15:31
 */

namespace core\helper;


use app\admin\service\CommonService;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class CsvHelper
{
    //导出csv文件
    public static function put_csv($list, $title = '', $file_name = '')
    {
        if (empty($title)) {
            $title = array_keys($list[0]);
        }
        $file_name .= "CSV" . date("mdHis", time()) . ".csv";
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename=' . $file_name);
        header('Cache-Control: max-age=0');
        $file = fopen('php://output', "a");
        $limit = 1000;
        $calc = 0;
        foreach ($title as $v) {
            $tit[] = iconv('UTF-8', 'GB2312//IGNORE', $v);
        }
        fputcsv($file, $tit);
        foreach ($list as $v) {
            if (isset($v['created_at'])) {
                $v['created_at'] = DateHelper::getInstance()->show($v['created_at']);
            }
            if (isset($v['updated_at'])) {
                $v['updated_at'] = DateHelper::getInstance()->show($v['updated_at']);
            }
            $calc++;
            if ($limit == $calc) {
                ob_flush();
                flush();
                $calc = 0;
            }
            foreach ($v as $t) {
                $tarr[] = iconv('UTF-8', 'GB2312//IGNORE', $t);
            }
            fputcsv($file, $tarr);
            unset($tarr);
        }
        unset($list);
        fclose($file);
        exit();
    }


    public static function write_csv($data, $path, $filename = 'license_code.csv')
    {
        if (empty($data) || empty($path) || empty($filename)) {
            return false;
        }
        if (!file_exists($path)) {
            mkdir($path);
        }
        $filename = $path . '/' . $filename;
        file_put_contents($filename, '');
        $fp = fopen($filename, 'w');
        foreach ($data as $fields) {
            fputcsv($fp, $fields);
        }
        fclose($fp);
        return $filename;
    }

    /**
     * 导出excel文件
     *
     * @param string $excelFileName 导出的文件名
     * @param array $title excel的标题列
     * @param array $data 导出的数据
     */
    public static function exportExcel($list, $title, $file_name = '')
    {
        if (empty($title)) {
            $title = array_keys($list[0]);
        }
        $list1 = self::resultData($list);
        $file_name .= "XLSX" . date("mdHis", time()) . ".xlsx";
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $a = 'A';$b = 'A';$c = 'B';

        $A = 3;$B = 2;$C = 1;$d = 2;
        foreach ($title as $k => $v) {
            $sheet->setCellValue($a . $A++, $v);
        }
        foreach ($list['name'] as $key => $listVal) {
            $sheet->setCellValue($c . $C, $listVal);
            foreach ($list1['result']['result' . $key]['data'] as $v) {
                if ($v != '') {
                    $v = explode(',', $v);
                    $b++;
                    foreach ($v as $k => $t) {
                        $sheet->setCellValue($b . $B++, $t);
                    }
                    $c++;
                    $B = 2;
                }
            }
        }
        $mergeCellOne = 'B';$mergeCellTwo = 'B';
        foreach ($list1['result'] as $key => $value) {
            $count = count($value['data']);
            if ($count > 1) {
                for ($c = 1; $c < $count; $c++) {
                    $mergeCellTwo++;
                }
                $sheet->mergeCells($mergeCellOne . '1:' . $mergeCellTwo . '1');
                $mergeCellOne = $mergeCellTwo;
            } else {
                $mergeCellOne++;
                $mergeCellTwo++;
            }
        }
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        // 实现文件下载
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="' . $file_name);
        header('Cache-Control: max-age=0');
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet);
        exit;
    }

    public static function resultData($list, $rate = 'false')
    {
        foreach ($list['result'] as $key => &$item) {
            foreach ($item as $k => &$v) {
                if (!empty($v)) {
                    if (!is_array($v)) {
                        $v = explode(',',str_replace("'",'',str_replace('"','',$v)));
                    }
                    foreach ($v as $vk => &$val) {
                        if ($val == '') {
                            unset($list[$key][$k][$vk]);
                        } else {
                            $value_count = array_sum(explode(',', $val));
                            $val = $vk . ',' . $value_count . ',' . $val;
                        }
                    }
                }
            }
            unset($list['result'][$key]['yLabels']);
        }
        if ($rate == 'true') {
            $arr = [];
            foreach ($list['result'] as $key => &$item) {
                foreach ($item as $k => &$v) {
                    $v = explode(',', $v);
                    if ($v != '') {
                        foreach ($v as $vKey => $vVal) {
                            if ($vKey == 0) {
                                $arr[$vKey] = $vVal;
                            } else {
                                if (!isset($arr[$vKey])) {
                                    $arr[$vKey] = $vVal;
                                } else {
                                    $arr[$vKey] += $vVal;
                                }
                            }
                        }
                    }
                }
                foreach ($item as $it => &$iv) {
                    foreach ($iv as $vk => &$vv) {
                        if ($vk > 0) {
                            if ($arr[$vk] > 0) {
                                $vv = round($vv / $arr[$vk] * 100, 2) . '%';
                            } else {
                                $vv = 0 . '%';
                            }
                        } else if ($vk = 0) {
                            $vv = $arr[$vk];
                        }
                    }
                    $iv = implode(',', $iv);
                }
            }
        }
        return $list;
    }
}