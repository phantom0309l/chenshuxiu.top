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

class Set_menzhen_offset_daycnt
{

    public function dowork () {

        $unitofwork = BeanFinder::get("UnitOfWork");

        $namestr = "'方妍彤','杭州市第一人民医院','赵冰','李锋','蒋茂莹','高维佳','潘黎明','刘畅','韩晶晶','王迎红','冯斌','李华伟','郭慧荣','李春力','单海军','杨阳','丁力','黄波','郭素芹','郭敬华','万瑞','王敏','袁秀丽','周圆月','张美燕','林鄞','汪玲华','查彩慧','林海生','欧婉杏','杨丽新','曾海辉','张洪宇','梅其霞','郭向阳','刘灵','王亚萍','郑肖玲','蔡丽如','黄柏青','郑芸芸','黄建琪','林桂秀','陈燕惠','殷晓荣','王桂芝','陈敏榕','薛漳','黄林娟','刘文龙','施灵敏','李伟','黄正勇','李建峰','钱沁芳','张琦光','许白叶','卢和丽','陈雄','肖苗','龚俊','任榕娜','庄昭明','袁兆红','王浙东','张悦秋','李洁','张华','张波','杨志伟','曾海辉','郑庆梅'";
        $sql = "select id from doctors where name in ({$namestr})";

        /*$sql = "select a.id from doctors a
                    inner join doctordiseaserefs b on b.doctorid = a.id
                    where a.hospitalid in (77,78,79,80,81,92,93,94,96,200,305,310,317,352,361,423,425) and b.diseaseid=1";*/
        $ids = Dao::queryValues($sql);
        $i = 0;

        foreach($ids as $id){
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $doctor = Doctor::getById($id);
            if($doctor instanceof Doctor){
                $doctordiseaseref = DoctorDiseaseRefDao::getByDoctoridDiseaseid($id, 1);
                if($doctordiseaseref instanceof DoctorDiseaseRef){
                    $doctor->menzhen_offset_daycnt = 28;
                    $cnt = $this->getCntByName($doctor->name);
                    if($cnt > 1){
                        echo "\n\n{$doctor->name}";
                    }
                }
            }
        }
        $unitofwork->commitAndInit();
    }

    private function getCntByName($name){
        $doctors = DoctorDao::getListByName($name);
        return count($doctors);
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Set_menzhen_offset_daycnt.php]=====");

$process = new Set_menzhen_offset_daycnt();
$process->dowork();

Debug::trace("=====[cron][end][Set_menzhen_offset_daycnt.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
