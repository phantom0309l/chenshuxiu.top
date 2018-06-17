<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

class Data_wangqian2313
{

    public function dowork () {
        echo " [Data_wangqian2313] begin \n";
        $content = '';
        $content .= "<html lang=\"zh-cn\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" /><body>";

        $sql = "select distinct a.*
            from patients a
            inner join pcards b on b.patientid = a.id
            where b.doctorid = 32  and a.status=1 and a.subscribe_cnt>0 and a.name in ('牛光芳','原萍','郭香兰','刘改生','孙洪波','王后宝','郝淑云','周小燕','王秀强','孙晨丽','周爱民','刘淑华','周红燕','曲淑梅','夏薇','徐广军','张广奎','李超','王宏伟
','马万玲','夏茂珍','赵春燕','姚瑞萍','魏金刚','葛玉静','孙俊芬','徐娟','蔡艳艳','张凤娟','宋怀丰','潘雪勤','李荣','宋春华','菅丽敏','王海林','张红梅','王淑英','张新见','刘述云','白晓丽','汤小毛
','李蛮为','张万荣','梁颖红','樊小沙','樊秀琴','王玲军','李春霞','刘莉') ";
        $patients = Dao::loadEntityList("Patient", $sql);

        // kl6
        $checkuptplid_1 = 103304078;

        // 肺功能
        $checkuptplid_2 = 103284836;

        // HRCT
        $checkuptplid_3 = 103303838;

        // 呼吸困难评分
        $papertplid_1 = 101457243;

        // UCSD医学中心肺部康复项目气短问卷
        $papertplid_2 = 101458417;

        // St’ George医院呼吸问题的调查问卷
        $papertplid_3 = 101459751;

        // SF-36 (健康状况调查表)
        $papertplid_4 = 101457325;

        // EQ-5D-健康问卷数据
        $papertplid_5 = 101457321;
//        echo " [kl6]\n";
//
//        $content .= "<h1>kl6</h1><table>";
//            $content .= $this->echoCheckupByPatientCheckupTplid($patients,$checkuptplid_1);
//        $content .= "</table>";
//        echo " [肺功能]\n";
//
//        $content .= "<h1>肺功能</h1><table>";
//            $content .= $this->echoCheckupByPatientCheckupTplid($patients,$checkuptplid_2);
//        $content .= "</table>";
//        echo " [HRCT]\n";
//
//        $content .= "<h1>HRCT</h1><table>";
//            $content .= $this->echoCheckupByPatientCheckupTplid($patients,$checkuptplid_3);
//        $content .= "</table>";
//        echo " [呼吸困难评分]\n";
//
//        $content .= "<h1>呼吸困难评分</h1><table>";
//            $content .= $this->echoPaperByPatientPaperTplid($patients,$papertplid_1);
//        $content .= "</table>";
//        echo " [UCSD医学中心肺部康复项目气短问卷]\n";
//        $this->write2file($content);
//
//        $content =  "<h1>UCSD医学中心肺部康复项目气短问卷</h1><table>";
//            $content .= $this->echoPaperByPatientPaperTplid($patients,$papertplid_2);
//        $content .= "</table>";
//        echo " [St’ George医院呼吸问题的调查问卷]\n";
//        $this->write2file($content);

//        $content = "<h1>St’ George医院呼吸问题的调查问卷</h1><table>";
//            $content .= $this->echoPaperByPatientPaperTplid($patients,$papertplid_3);
//        $content .= "</table>";
//        echo " [SF-36 (健康状况调查表)]\n";
//
//        $this->write2file($content);
//
//        $content = "<h1>SF-36 (健康状况调查表)</h1><table>";
//            $content .= $this->echoPaperByPatientPaperTplid($patients,$papertplid_4);
//        $content .= "</table>";
//        echo " [EQ-5D-健康问卷数据]\n";
//
//        $this->write2file($content);
//
        $content = "<h1>EQ-5D-健康问卷数据</h1><table>";
            $content .= $this->echoPaperByPatientPaperTplid($patients,$papertplid_5);
        $content .= "</table>";

        $content .= "  </body></html> ";

        $this->write2file($content);
        echo " [Data_wangqian2313] finished \n";

    }

    private function echoCheckupByPatientCheckupTplid($patients, $checkuptplid){
        $tempcontent = '';

        $checkuptpl = CheckupTpl::getById($checkuptplid);

        $xquestionsheet = $checkuptpl->xquestionsheet;
        $questions = $xquestionsheet->getQuestions();
        $tempcontent .= "<tr><th>患者姓名</th><th>检查日期</th>";

        foreach ($questions as $i => $q) {
            if ($q->isSection()) {
                continue;
            }

            if ($q->isMultText()) {
                foreach ($q->getMultTitles() as $t) {
                    $tempcontent .=  "<th>{$q->content}-{$t}</th>";
                }

            } else {
                $tempcontent .=  "<th>{$q->content}</th>";
            }
        }

        $tempcontent .=  "</tr>";

        foreach( $patients as $patient ){
            echo "{$patient->name}\n";
            $checkups = CheckupDao::getListByPatientCheckupTpl($patient, $checkuptpl);
            foreach( $checkups as $k=>$checkup ){
                $xanswersheet = $checkup->xanswersheet;
                if(false == $xanswersheet instanceof XAnswerSheet){
                    continue;
                }
                $tempcontent .=  "<tr><td>{$patient->name}</td><td>{$checkup->check_date}</td>";

                echo "#";

                foreach ($questions as $i => $q) {
                    if ($q->isSection()) {
                        continue;
                    }

                    $xanswer = $xanswersheet->getAnswer($q->id);
                    // 有答案
                    if ($xanswer instanceof XAnswer) {
                        foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                            $tempcontent .=  "<td>{$t}</td>";
                        }
                    } else {
                        if ($q->isMultText()) {
                            foreach ($q->getMultTitles() as $t) {
                                $tempcontent .=  "<td></td>";
                            }

                        } else {
                            $tempcontent .=  "<td></td>";
                        }
                    }
                }

                $tempcontent .=  "</tr>";
            }
        }

        echo "\n";
        return $tempcontent;
    }

    private function echoPaperByPatientPaperTplid($patients, $papertplid){
        $tempcontent = '';

        $papertpl = PaperTpl::getById($papertplid);

        $xquestionsheet = $papertpl->xquestionsheet;
        $questions = $xquestionsheet->getQuestions();
        $tempcontent .=  "<tr><th>患者姓名</th><th>检查日期</th>";

        foreach ($questions as $i => $q) {
            if ($q->isSection()) {
                continue;
            }

            if ($q->isMultText()) {
                foreach ($q->getMultTitles() as $t) {
                    $tempcontent .=  "<th>{$q->content}-{$t}</th>";
                }

            } else {
                $tempcontent .=  "<th>{$q->content}</th>";
            }
        }

        $tempcontent .=  "</tr>";

        foreach( $patients as $patient ){
            echo "{$patient->name}\n";
            $papers = PaperDao::getListByPatientid( $patient->id, " and papertplid={$papertplid} " );
            foreach( $papers as $k=>$paper ){
                $xanswersheet = $paper->xanswersheet;
                if(false == $xanswersheet instanceof XAnswerSheet){
                    continue;
                }
                $tempcontent .=  "<tr><td>{$patient->name}</td><td>{$paper->getCreateDay()}</td>";
                echo "#";

                foreach ($questions as $i => $q) {
                    if ($q->isSection()) {
                        continue;
                    }

                    $xanswer = $xanswersheet->getAnswer($q->id);
                    // 有答案
                    if ($xanswer instanceof XAnswer) {
                        foreach ($xanswer->getQuestionCtr()->getAnswerContents() as $t) {
                            $tempcontent .=  "<td>{$t}</td>";
                        }
                    } else {
                        if ($q->isMultText()) {
                            foreach ($q->getMultTitles() as $t) {
                                $tempcontent .=  "<td></td>";
                            }

                        } else {
                            $tempcontent .=  "<td></td>";
                        }
                    }
                }

                $tempcontent .=  "</tr>";
            }
        }

        echo "\n";
        return $tempcontent;
    }

    private function write2file ($filecontent) {
        $file = fopen("data.html", 'a+');
        fwrite($file, $filecontent);
    }
}

$process = new Data_wangqian2313();
$process->dowork();
