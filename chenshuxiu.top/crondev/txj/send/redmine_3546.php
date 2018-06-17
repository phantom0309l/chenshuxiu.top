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

// Debug::$debug = 'Dev';

class Redmine3546
{

    public function dowork () {
        $unitofwork = BeanFinder::get("UnitOfWork");

        $sql = "select id
                from wxusers where wxshopid=1 and subscribe=1 and id not in (
                    select id from wxusers where wxshopid=1 and
                    doctorid in (179,153,25,24,53,483,432,268,394,438,111,286,96,98) and subscribe=1 and createtime <= '2017-05-01'
                )";
        $ids = Dao::queryValues($sql);
        $i = 0;
        foreach ($ids as $id) {
            echo "\nid[{$id}]\n";
            $i ++;
            if ($i >= 100) {
                $i = 0;
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            $wxuser = WxUser::getById($id);
            if($wxuser instanceof WxUser){
                $patient = $wxuser->user->patient;
                if($patient instanceof Patient){

					//无效患者跳过
					if($patient->doubt_type > 0){
						continue;
					}
                    //sunflower项目的优先级高于患者等级业务，如果是合作患者，跳过患者等级脚本。
                    if($patient->isInHezuo("Lilly")){
                        continue;
                    }

					$patientpgroupref = $patient->getPatientPgroupRefByStatus(1);
					$content = "";

					if( $patientpgroupref instanceof PatientPgroupRef && false == $patient->isDruging()){
						$content = $this->getSendContent("noDrugAndStudy");
					}else {
						$content = $this->getSendContent("else");
					}

					$doctor_name = $patient->doctor->name;
		            $str = "{$doctor_name}医生助理";
		            $first = array(
		                "value" => "",
		                "color" => "#ff6600");
		            $keywords = array(
		                array(
		                    "value" => $str,
		                    "color" => "#aaa"),
		                array(
		                    "value" => $content,
		                    "color" => "#ff6600"));
		            $content = WxTemplateService::createTemplateContent($first, $keywords);

		            PushMsgService::sendTplMsgToWxUserBySystem($wxuser, "adminNotice", $content);

                }
            }
        }

        $unitofwork->commitAndInit();
    }

	public function getSendContent ($type) {
		$str = "";

        if($type == "noDrugAndStudy"){
            $str = "亲爱的家长，为了让您更直观的了解平台提供的服务内容，微信公众号的菜单页面做了一些调整，原先【今日任务】中的课程位置变更为菜单栏“自我管理”--“每日一练”。\n如果在使用中遇到什么困难，可点击菜单页面左下角的小键盘切换到输入模式，给助理发送消息。";
        }
        if($type == "else"){
            $str = "亲爱的家长，最新版的特权系统已上线，在接受管理中越配合的家长，可享受到越高级的特权服务！您的专属服务菜单已配置完毕，方便的时候可以逐个点击菜单了解服务内容。\n如果在使用中遇到什么困难，可点击菜单页面左下角的小键盘切换到输入模式，给助理发送消息。";
        }
        return $str;
    }

}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][Redmine3546.php]=====");

$process = new Redmine3546();
$process->dowork();

Debug::trace("=====[cron][end][Redmine3546.php]=====");
//Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
