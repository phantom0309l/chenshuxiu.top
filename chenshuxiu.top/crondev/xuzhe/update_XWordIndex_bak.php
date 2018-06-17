<?php
//ini_set("arg_seperator.output", "&amp;");
//ini_set("magic_quotes_gpc", 0);
//ini_set("magic_quotes_sybase", 0);
//ini_set("magic_quotes_runtime", 0);
//ini_set('display_errors', 1);
//ini_set("memory_limit", "2048M");
//include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
//include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
//mb_internal_encoding("UTF-8");
//
//TheSystem::init(__FILE__);
//
//class Update_XWordIndex
//{
//
//    private $tablesArr = array();
//
//    private function setTablesArr(){
//        $this->tablesArr['Patient'][] = 'name';
////        $this->tablesArr['Medicine'][] = 'name';
//    }
//
//    public function dowork () {
//
//        $this->setTablesArr();
//        $tablesArr = $this->tablesArr;
//        echo "\n [Update_XWordIndex] begin ";
//
//        foreach( $tablesArr as $tablename=>$types ){
//            foreach( $types as $type ){
//                echo "\n [Update_XWordIndex] {$tablename}_{$type} ";
//                $fucname = "updateXWordTable_{$tablename}_{$type}";
//                $this->$fucname();
//            }
//        }
//
//        $unitofwork = BeanFinder::get("UnitOfWork");
//
//        $unitofwork->commitAndInit();
//
//        echo "\n [Update_XWordIndex] finished \n";
//
//    }
//
//    private function updateXWordTable_Patient_name(){
//        $sql = "select id from patients limit 10";
//        $patientids = Dao::queryValues($sql);
//
//        foreach( $patientids as $key=>$patientid ){
//            $patient = Patient::getById($patientid);
//
//            echo "\n {$key}";
//            if( trim($patient->name) == '' ){
//                continue;
//            }
//            $unitofwork = BeanFinder::get("UnitOfWork");
//
//            $xwordtable = XWordTableDao::getByObjType($patient,'patient_name');
//            if( $xwordtable instanceof XWordTable ){
//                if( $patient->name == $xwordtable->keyword ){
//                    $unitofwork->commitAndInit();
//                    continue;
//                }else{
//                    $xwordtable->keyword = $patient->name;
//
//                    $xwordindexs = XWordIndexDao::getListByXwordtableid( $xwordtable->id );
//                    foreach( $xwordindexs as $xwordindex){
//                        $xwordindex->remove();
//                    }
//                    $unitofwork->commitAndInit();
//
//                    $this->updateXWordIndex_Patient_name( $xwordtable );
//                }
//            }else{
//                $row = array();
//                $row['keyword'] = $patient->name;
//                $row['type'] = 'patient_name';
//                $row['objtype'] = get_class($patient);
//                $row['objid'] = $patient->id;
//                $row['weight'] = 1;
//
//                $xwordtable = XWordTable::createByBiz($row);
//                $unitofwork->commitAndInit();
//                $this->updateXWordIndex_Patient_name( $xwordtable );
//            }
//
//            $unitofwork->commitAndInit();
//        }
//    }
//
//    private function updateXWordIndex_Patient_name( $xwordtable ){
//        $unitofwork = BeanFinder::get("UnitOfWork");
//
//        $keyword = $xwordtable->keyword;
//
//        $wordpieces = array();
//        $wordpieces = $this->wordpiece_C2Cs($keyword);
//
//        foreach( $wordpieces as $wordpiece ){
//            $this->saveXWordIndex( $wordpiece, $xwordtable, 1);
//        }
//        $unitofwork->commitAndInit();
//
//        $unitofwork = BeanFinder::get("UnitOfWork");
//
//        $wordpieces = array();
//        $wordpieces = $this->wordpiece_C2Pinyins($keyword);
//
//        foreach( $wordpieces as $wordpiece ){
//            $this->saveXWordIndex( $wordpiece, $xwordtable, 1);
//        }
//
//        $unitofwork->commitAndInit();
//
//        $unitofwork = BeanFinder::get("UnitOfWork");
//
//        $wordpieces = array();
//        $wordpieces = $this->wordpiece_C2Py($keyword);
//
//        foreach( $wordpieces as $wordpiece ){
//            $this->saveXWordIndex( $wordpiece, $xwordtable, 1);
//        }
//
//        $unitofwork->commitAndInit();
//
//    }
//
//    private function updateXWordTable_Medicine_name(){
//        $sql = "select id from medicines";
//        $medicineids = Dao::queryValues($sql);
//
//        foreach( $medicineids as $key=>$medicineid ){
//            $medicine = Medicine::getById($medicineid);
//
//            echo "\n {$key}";
//
//            if( trim($medicine->name) == '' ){
//                continue;
//            }
//            $unitofwork = BeanFinder::get("UnitOfWork");
//
//            $xwordtable = XWordTableDao::getByObjType($medicine,'medicine_name');
//            if( $xwordtable instanceof XWordTable ){
//                if( $medicine->name == $xwordtable->keyword ){
//                    $unitofwork->commitAndInit();
//                    continue;
//                }else{
//                    $xwordtable->keyword = $medicine->name;
//
//                    $xwordindexs = XWordIndexDao::getListByXwordtableid( $xwordtable->id );
//                    foreach( $xwordindexs as $xwordindex){
//                        $xwordindex->remove();
//                    }
//                    $unitofwork->commitAndInit();
//
//                    $this->updateXWordIndex_Patient_name( $xwordtable );
//                }
//            }else{
//                $row = array();
//                $row['keyword'] = $medicine->name;
//                $row['type'] = 'medicine_name';
//                $row['objtype'] = get_class($medicine);
//                $row['objid'] = $medicine->id;
//                $row['weight'] = 1;
//
//                $xwordtable = XWordTable::createByBiz($row);
//                $unitofwork->commitAndInit();
//                $this->updateXWordIndex_Medicine_name( $xwordtable );
//            }
//
//            $unitofwork->commitAndInit();
//        }
//    }
//
//    private function updateXWordIndex_Medicine_name( $xwordtable ){
//        $unitofwork = BeanFinder::get("UnitOfWork");
//
//        $keyword = $xwordtable->keyword;
//
//        $wordpieces = array();
//        $wordpieces = $this->wordpiece_C2Cs($keyword);
//
//        foreach( $wordpieces as $wordpiece ){
//            $this->saveXWordIndex( $wordpiece, $xwordtable, 1);
//        }
//        $unitofwork->commitAndInit();
//
//        $unitofwork = BeanFinder::get("UnitOfWork");
//
//        $wordpieces = array();
//        $wordpieces = $this->wordpiece_C2Pinyins($keyword);
//
//        foreach( $wordpieces as $wordpiece ){
//            $this->saveXWordIndex( $wordpiece, $xwordtable, 1);
//        }
//
//        $unitofwork->commitAndInit();
//
//        $unitofwork = BeanFinder::get("UnitOfWork");
//
//        $wordpieces = array();
//        $wordpieces = $this->wordpiece_C2Py($keyword);
//
//        foreach( $wordpieces as $wordpiece ){
//            $this->saveXWordIndex( $wordpiece, $xwordtable, 1);
//        }
//
//        $unitofwork->commitAndInit();
//
//    }
//
//    // 中文to中文
//    private function wordpiece_C2Cs($keyword){
//        return array_unique($this->getArr2Combination( $keyword ));
//    }
//    // 中文to全拼
//    private function wordpiece_C2Pinyins($keyword){
//        return array_unique($this->getArr2Increase( PinyinUtilNew::Word2PY($keyword,'') ));
//    }
//    // 中文to拼音首字母
//    private function wordpiece_C2Py($keyword){
//        return array_unique($this->getArr2Increase( strtolower(PinyinUtilNew::Word2PY($keyword))));
//    }
//
//    // 字符全排列
//    private function getArr2Combination( $keyword, $step = 1 ){
//        $wordpieces = array();
//        $len = mb_strlen($keyword, "UTF-8");
//        for( $i = $step; $i <= $len; $i++ ){
//            $wordpieces = array_merge ($wordpieces, $this->str_split_unicode($keyword,$i));
//        }
//
//        return $wordpieces;
//    }
//
//    // (1,2,3) => (1,2) (2,3)
//    private function str_split_unicode($str, $l = 0) {
//        if ($l > 0) {
//            $ret = array();
//            $len = mb_strlen($str, "UTF-8");
//            $len = $len - $l + 1;
//            for ($i = 0; $i < $len; $i ++) {
//                $ret[] = mb_substr($str, $i, $l, "UTF-8");
//            }
//            return $ret;
//        }
//        return preg_split("//u", $str, -1, PREG_SPLIT_NO_EMPTY);
//    }
//
//    // 字符串递增排列 (1,2,3) => (1) (1,2) (1,2,3)
//    private function getArr2Increase( $keyword, $start = 2 ){
//        $wordpieces = array();
//        $len = mb_strlen($keyword, "UTF-8");
//        for( $i = $start; $i <= $len; $i++ ){
//            $wordpieces[] = mb_substr ($keyword, 0, $i, "UTF-8");
//        }
//
//        return $wordpieces;
//    }
//
//    private function saveXWordIndex( $wordpiece, $xwordtable, $weight){
//        $row = array();
//        $row['keyword'] = $wordpiece;
//        $row['xwordtableid'] = $xwordtable->id;
//        $row['weight'] = $weight;
//
//        $xwordindex = XWordIndex::createByBiz($row);
//    }
//}
//
//$process = new Update_XWordIndex();
//$process->dowork();
//
