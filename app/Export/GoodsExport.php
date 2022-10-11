<?php


namespace App\Export;


use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GoodsExport implements FromArray, WithStyles, ShouldAutoSize, WithColumnWidths
{
    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 导出数据
     * @return array
     */
    public function array():array {
        return $this->data;
    }

    /**
     * 设置单元格宽度
     * @return array
     */
    public function columnWidths(): array
    {
        return ['A'=>20, 'B'=>30, 'C'=>20, 'D'=>20, 'E'=>20, 'F'=>20, 'G'=>20, 'H'=>20, 'I'=>20];
    }

    /**
     * 设置样式
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        //设置标题左对齐
        $sheet->getStyle('A1:I1')->applyFromArray(['alignment' => ['horizontal' => 'left']]);
        //设置标题加粗
        $sheet->getStyle('A1:I1')->applyFromArray(['font' => ['bold' => true]]);
        //设置内容左对齐
        $sheet->getStyle('A2:I'. count($this->data))->applyFromArray(['alignment' => ['horizontal' => 'left']]);
    }
}