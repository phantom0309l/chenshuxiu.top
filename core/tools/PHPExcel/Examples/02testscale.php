<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();
$PHPReader = new PHPExcel_Reader_Excel2007();
$objPHPExcel = $PHPReader->load("/Users/qiaoxiaojin/Desktop/scale/scale_book.xlsx");


// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");

// Set default font
echo date('H:i:s') , " Set default font" , EOL;
$objPHPExcel->getDefaultStyle()->getFont()->setName('Arial')
                                          ->setSize(10);

// Add some data, resembling some different data types
echo date('H:i:s') , " Add some data" , EOL;

$arr = array(
    array(1,2,3,4,5),
    array("乔","2016-05-10","4","#",12)
);
$currentSheet = $objPHPExcel->getActiveSheet();
$max_row = $currentSheet->getHighestDataRow() + 1;
//$column = $currentSheet->getHighestDataColumn();
//echo $rows ."\n";
//echo $column ."\n";
foreach ($arr as $i => $item) {
    foreach ($item as $j => $a) {
        $currentSheet->setCellValueByColumnAndRow($j, $max_row+$i, $a);
    }
}


// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
//$currentSheet->setTitle('ADHD评定量表（IV）');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save("/Users/qiaoxiaojin/Desktop/scale/scale_book.xlsx");
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


echo date('H:i:s') , " Reload workbook from saved file" , EOL;
$callStartTime = microtime(true);

//$objPHPExcel = PHPExcel_IOFactory::load("/Users/qiaoxiaojin/Desktop/2test.xlsx");


$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo 'Call time to reload Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


var_dump($objPHPExcel->getActiveSheet()->toArray());


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done testing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;
