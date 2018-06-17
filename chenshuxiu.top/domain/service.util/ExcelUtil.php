<?php

class ExcelUtil
{

    //web导出的方法
    public static function createForWeb ($data, $headarr, $filename = "") {
        if ($filename == "") {
            $filename = time();
        }
        require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
        $objPHPExcel = new PHPExcel();

        $currentSheet = $objPHPExcel->getActiveSheet();
        foreach ($headarr as $m => $a) {
            $currentSheet->setCellValueByColumnAndRow($m, 1, $a);
        }

        $max_row = $currentSheet->getHighestDataRow() + 1;
        foreach ($data as $i => $item) {
            foreach ($item as $j => $cell) {
                $currentSheet->setCellValueByColumnAndRow($j, $max_row + $i, $cell);
            }
        }
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Content-Type:application/force-download');
        header('Content-Type:application/vnd.ms-execl');
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header("Content-Disposition:attachment;filename='{$filename}.xlsx'");
        header('Content-Transfer-Encoding:binary');
        $objWriter->save('php://output');
        exit();
    }

    //web导出的方法 有单元格合并
    public static function createHasMergeCellsForWeb ($data, $headarr, $needMergeRowIndexArr, $needMergeColIndexArr, $filename = "") {
        if ($filename == "") {
            $filename = time();
        }

        //同一个序号有多个的需要合并
        //保存下需要合并的行
        $rowArr = array();
        require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
        $objPHPExcel = new PHPExcel();

        $currentSheet = $objPHPExcel->getActiveSheet();
        foreach ($headarr as $m => $a) {
            $currentSheet->setCellValueByColumnAndRow($m, 1, $a);
        }

        $max_row = $currentSheet->getHighestDataRow() + 1;
        foreach ($data as $i => $item) {
            foreach ($item as $j => $cell) {
                $currentSheet->setCellValueByColumnAndRow($j, $max_row + $i, $cell);
            }
        }

        //合并单元格逻辑
        foreach($needMergeRowIndexArr as $arr_row){
            foreach ($needMergeColIndexArr as $col_index) {
                $col = PHPExcel_Cell::stringFromColumnIndex($col_index);
                $row_index_1 = $arr_row[0];
                $row_index_2 = $arr_row[1];
                $currentSheet->mergeCells("{$col}{$row_index_1}:{$col}{$row_index_2}");

                $currentSheet->getStyle("{$col}{$row_index_1}")->applyFromArray(
                    array(
                        'alignment' => array(
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );

            }
        }

        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Content-Type:application/force-download');
        header('Content-Type:application/vnd.ms-execl');
        header('Content-Type:application/octet-stream');
        header('Content-Type:application/download');
        header("Content-Disposition:attachment;filename='{$filename}.xlsx'");
        header('Content-Transfer-Encoding:binary');
        $objWriter->save('php://output');
        exit();
    }

    //脚本导出的方法
    //fileurl 的例子：$fileurl = "/home/taoxiaojin/scale/huanjie.xlsx";
    //路径和文件名改成自己的！！！
    public static function createForCron ($arr, $headarr, $fileurl) {
        require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
        // $fileurl = "/home/taoxiaojin/scale/output_331302.xlsx";
        if(!file_exists($fileurl)){
            $fp=fopen($fileurl, "w+"); //打开文件指针，创建文件
            if ( !is_writable($fileurl) ){
                Debug::trace("文件:" .$fileurl. "不可写，请检查！");
                return;
            }
            fclose($fp); //关闭指针
        }
        $objPHPExcel = new PHPExcel();

        $currentSheet = $objPHPExcel->getActiveSheet();
        foreach ($headarr as $m => $a) {
            $currentSheet->setCellValueByColumnAndRow($m, 1, $a);
        }

        $max_row = $currentSheet->getHighestDataRow() + 1;
        foreach ($arr as $i => $item) {
            foreach ($item as $j => $cell) {
                $currentSheet->setCellValueByColumnAndRow($j, $max_row+$i, $cell);
            }
        }
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($fileurl);
    }

    public static function createHasMergeCellsForCron ($arr, $headarr, $fileurl, $needMergeRowIndexArr, $needMergeColIndexArr) {
        require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");
        // $fileurl = "/home/taoxiaojin/scale/output_331302.xlsx";
        if(!file_exists($fileurl)){
            $fp=fopen($fileurl, "w+"); //打开文件指针，创建文件
            if ( !is_writable($fileurl) ){
                Debug::trace("文件:" .$fileurl. "不可写，请检查！");
                return;
            }
            fclose($fp); //关闭指针
        }
        $objPHPExcel = new PHPExcel();

        $currentSheet = $objPHPExcel->getActiveSheet();
        foreach ($headarr as $m => $a) {
            $currentSheet->setCellValueByColumnAndRow($m, 1, $a);
        }

        $max_row = $currentSheet->getHighestDataRow() + 1;
        foreach ($arr as $i => $item) {
            foreach ($item as $j => $cell) {
                $currentSheet->setCellValueByColumnAndRow($j, $max_row+$i, $cell);
            }
        }

        //合并单元格逻辑
        foreach($needMergeRowIndexArr as $arr_row){
            foreach ($needMergeColIndexArr as $col_index) {
                $col = PHPExcel_Cell::stringFromColumnIndex($col_index);
                $row_index_1 = $arr_row[0];
                $row_index_2 = $arr_row[1];
                $currentSheet->mergeCells("{$col}{$row_index_1}:{$col}{$row_index_2}");

                $currentSheet->getStyle("{$col}{$row_index_1}")->applyFromArray(
                    array(
                        'alignment' => array(
                            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
                        )
                    )
                );

            }
        }
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($fileurl);
    }

    /*
     * 创建一个或多个sheet的Excel
     * 格式如下：
        $data = [
            'sheet1name' => [
                'heads' => [
                    '姓名',
                    '年龄'
                ],
                'data' => [
                    [0] => [
                        'fanghanwen',
                        23
                    ],
                    [1] => [
                        'liufei',
                        23
                    ],
                    ...
                ]
            ],
            'sheet2name' => [
                'heads' => [
                    '姓名',
                    '学校',
                    '专业'
                ],
                'data' => [
                    [0] => [
                        'fanghanwen',
                        '清华大学',
                        '计算机科学与技术'
                    ],
                    [1] => [
                        'liufei',
                        '北京大学',
                        '历史系'
                    ],
                    ...
                ]
            ],
            ...
        ];
        $fileurl = "/tmp/certican/test.xls";
     */
    public static function createExcelImp ($data, $fileurl) {
        require_once (ROOT_TOP_PATH . "/../core/tools/PHPExcel/Classes/PHPExcel.php");

        if(!file_exists($fileurl)){
            $fp=fopen($fileurl, "w+"); //打开文件指针，创建文件
            if ( !is_writable($fileurl) ){
                Debug::trace("文件:" .$fileurl. "不可写，请检查！");
                return;
            }
            fclose($fp); //关闭指针
        }

        $objPHPExcel = new PHPExcel();

        // 创建多个sheet
        $site = 0;
        foreach ($data as $title => $sheetlist) {
            if ($site == 0) {
                $sheet = $objPHPExcel->getActiveSheet();
            } else {
                $sheet = $objPHPExcel->createSheet();
            }

            $sheet->setTitle($title);

            // 头部
            $headarr = $sheetlist['heads'];
            // 数据
            $datalist = $sheetlist['data'];

            foreach ($headarr as $m => $a) {
                $sheet->setCellValueByColumnAndRow($m, 1, $a);
            }

            $max_row = $sheet->getHighestDataRow() + 1;
            foreach ($datalist as $i => $item) {
                $j = 0;
                foreach ($item as $cell) {
                    $sheet->setCellValueByColumnAndRow($j++, $max_row+$i, $cell);
                }
            }

            $site ++;
        }
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->save($fileurl);
    }
}
