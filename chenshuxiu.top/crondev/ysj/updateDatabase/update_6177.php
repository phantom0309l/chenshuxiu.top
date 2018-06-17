<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 1);
ini_set("memory_limit", "2048M");
include_once(dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once(ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);


class update_6177
{

    public function dowork() {
        $unitofwork = BeanFinder::get("UnitOfWork");

        // 直接删除的 tagsName
        $deleteTags = [
            '逻辑思维差', '慢性浅表性胃炎', '髓鞘延迟发育', '没有明确诊断','任性'
        ];

        // targetTagName    =>  FromTagsName
        $mergeTags = [
            '自闭症倾向' => ['未确诊，怀疑是自闭症。', '自闭倾向', '疑是自闭症'],
            '学习障碍' => ['学习技能发育障碍'],
            '自闭症' => ['孤独症', '高功能自闭症'],
            '双向情感障碍' => ['精神压力大导致双向性格'],
            '对立违抗障碍' => ['情绪对抗'],
            '抑郁症' => ['轻度抑郁', '重度抑郁症'],
            '多动症' => ['注意力不集中', '轻度多动症', '注意力缺陷障碍重度'],
            '抽动症' => ['抽动秽语综合症', '慢运动或发声抽动障碍'],
            '生长发育迟缓' => ['发育落后', '身材矮小', '发育迟缓'],
            '智力发育迟缓' => ['右小脑发育不全', '脑部残疾', '智力低下', '大脑发育障碍', '脑发育迟缓', '智力发育迟缓？','智商偏低'],
            '焦虑障碍' => ['焦虑症'],
            '抽动症倾向' => ['未确诊抽动'],
            '夜惊' => ['半夜惊醒']
        ];


        // 直接删除tag 及其对应的 tagrefs
        foreach ($deleteTags as $delTagName) {
            $delTag = TagDao::getByTypestrAndName('patientDiagnosis', $delTagName);
            if ($delTag instanceof Tag == false) {
                Debug::warn("tagName=>'{$delTagName}'  的tag不存在");
                continue;
            }

            // 删除tagRef
            $tagRefs = TagRefDao::getListByObjtypeTagid('Patient', $delTag->id);
            foreach ($tagRefs as $tagRef) {
                $tagRef->remove();
                echo "\n\n----- tagRef=>'{$tagRef->id}'  被删除 -----\n\n";
            }

            // 删除tag
            $delTag->remove();
            echo "\n\n----- tag=>'{$delTag->name}'  被删除 -----\n\n";
        }

        // 合并tag
        foreach ($mergeTags as $targetTagName => $fromTagNameArr) {
            $targetTag = TagDao::getByTypestrAndName('patientDiagnosis', $targetTagName);
            if ($targetTag instanceof Tag == false) {
                $targetTag = $this->createTag($targetTagName);
                $unitofwork->commitAndInit();
                $unitofwork = BeanFinder::get("UnitOfWork");
            }

            foreach ($fromTagNameArr as $fromTagName) {
                $fromTag = TagDao::getByTypestrAndName('patientDiagnosis', $fromTagName);
                if ($fromTag instanceof Tag == false) {
                    Debug::warn("tagName=>'{$fromTagName}'  的tag不存在");
                    continue;
                }
                // 将fromTagRef 合并
                $fromTagRefs = TagRefDao::getListByObjtypeTagid('Patient', $fromTag->id);
                foreach ($fromTagRefs as $fromTagRef) {
                    $tryGetTagRef = TagRefDao::getByObjtypeObjidTagid('Patient', $fromTagRef->objid, $targetTag->id);
                    if($tryGetTagRef instanceof TagRef == false) {
                        $fromTagRef->set4lock('tagid',$targetTag->id);
                        echo "\n\n----- tag=>'{$fromTag->name}'  下的tagRef=>'{$tagRef->id}'  合并到 tag=>'{$targetTag->name}' -----\n\n";
                    }else {
                        $fromTagRef->remove();
                        echo "\n\n----- tagRef=>'{$fromTagRef->id}'  被删除 -----\n\n";
                    }
                    $unitofwork->commitAndInit();
                    $unitofwork = BeanFinder::get("UnitOfWork");
                }

                // 删除fromTag
                $fromTag->remove();
                echo "\n\n----- tag=>'{$fromTag->name}'  被删除 -----\n\n";
            }
        }

        $unitofwork->commitAndInit();
    }


    private function createTag ($tagName) {
        $tagRow = array();
        $maxId = Dao::queryValue("select max(id) as maxid from tags");
        $tagRow["id"] = $maxId + 1;
        $tagRow["typestr"] = 'patientDiagnosis';
        $tagRow["name"] = $tagName;
        $targetTag = Tag::createByBiz($tagRow);
        echo "\n\n----- tag=>{$targetTag->name}不存在，已经生成 -----\n\n";
        return $targetTag;
    }
}

echo "\n\n-----begin----- " . XDateTime::now();
Debug::trace("=====[cron][beg][update_6177.php]=====");

$process = new update_6177();
$process->dowork();

Debug::trace("=====[cron][end][update_6177.php]=====");
Debug::flushXworklog();
echo "\n-----end----- " . XDateTime::now();
