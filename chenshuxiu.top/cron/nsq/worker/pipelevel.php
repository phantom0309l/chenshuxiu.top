<?php
ini_set("arg_seperator.output", "&amp;");
ini_set("magic_quotes_gpc", 0);
ini_set("magic_quotes_sybase", 0);
ini_set("magic_quotes_runtime", 0);
ini_set('display_errors', 0);
ini_set("memory_limit", "2048M");
include_once (dirname(__FILE__) . "/../../../sys/PathDefine.php");
include_once (ROOT_TOP_PATH . "/cron/Assembly.php");
mb_internal_encoding("UTF-8");

TheSystem::init(__FILE__);

Config::setConfig("update_need_check_version", false);

class pipelevelworker
{
    // 测试方法：执行fangcunyisheng.com/cron/nsq/client/pipelevel/batch_pipelevel_classify脚本
    // 在fangcunyisheng.com/cron/nsq/client/pipelevel/ 路径下执行 ./batch_pipelevel_classify
    public function run ($param) {
        $j = 0;
        Debug::trace('param ' . $param);
        while ($j < 2) {
            try {
                $unitofwork = BeanFinder::get("UnitOfWork");

                $arr = json_decode($param, true);
                $pipelevelids = $arr['pipelevelids'];
                $labels = $arr['labels'];

                if(false == is_array($pipelevelids) || false == is_array($labels)){
                    Debug::warn("接收到的参数格式不对:[$arr]");
                    break;
                }

                if(count($pipelevelids) != count($labels)){
                    Debug::warn("pipelevelids与labels的数量不一致:[$arr]");
                    break;
                }

                foreach ($pipelevelids as $k => $pipelevelid) {
                    $pipelevel = PipeLevel::getById($pipelevelid);
                    $lable = $labels[$k];
                    if($pipelevel instanceof PipeLevel){
                        if($pipelevel->is_urgent > 0){
                            continue;
                        }
                        // 更新pipelevel
                        $this->updatePipeLevel($pipelevel, $lable);
                        // 更新消息任务的等级
                        $this->updateMsgOpTaskLevel($pipelevel);
                    }
                }

                $unitofwork->commitAndInit();
                break; // 跳出外层循环
            } catch (Exception $e) {
                $j ++;
                BeanFinder::clearBean("UnitOfWork");
                BeanFinder::clearBean("DbExecuter");
                $dbExecuter = BeanFinder::get('DbExecuter');
                $dbExecuter->reConnection();
                Debug::warn('pipelevel fail ' . $j);
            }
        }
        Debug::flushXworklog();

        return true;
    }

    // 更新pipelevel
    private function updatePipeLevel (PipeLevel $pipelevel, $lable) {
        $pipelevel->is_urgent = $lable + 1;
    }

    // 更新消息任务等级
    private function updateMsgOpTaskLevel ($pipelevel) {
        $optask = $pipelevel->optask;
        $is_urgent = $pipelevel->is_urgent;
        if($optask instanceof OpTask){
            // pipelevel为紧急
            if(2 == $is_urgent){
                $this->updateMsgOpTaskLevelImp($optask, 'because_ai');
            }
            // pipelevel为不紧急，再进行规则判断
            if(1 == $is_urgent){
                $this->updateMsgOpTaskLevelByRule($optask, $pipelevel);
            }
        } else {
            Debug::warn("pipelevel没有绑定到optaskid,pipelevelid[$pipelevel->id]");
        }
    }

    // 根据 规则 更新消息任务等级
    private function updateMsgOpTaskLevelByRule ($optask, $pipelevel) {
        $pipe = $pipelevel->pipe;
        if(false == $pipe->obj instanceof WxTxtMsg){
            return;
        }
        $content = $pipe->obj->content;

        //场景1
        if($this->isSectionOne($content)){
            $this->updateMsgOpTaskLevelImp($optask, 'urgent_word');
            return;
        }
        //场景2
        if($this->isSectionTwo($content)){
            $this->updateMsgOpTaskLevelImp($optask, 'trans_question');
            return;
        }
        //场景3
        if($this->isSectionThree($content)){
            $this->updateMsgOpTaskLevelImp($optask, 'trans_worry');
            return;
        }
        //场景4
        if($this->isSectionFour($content)){
            $this->updateMsgOpTaskLevelImp($optask, 'menzhen_queation');
            return;
        }
    }

    private function isSectionOne ($content) {
        $urgent_words = ['快递费', '请求回复', '请回复', '着急', '我要买药', '我想买药'];
        if($this->hasWord($content, $urgent_words)){
            return true;
        }
        return false;
    }

    private function isSectionTwo ($content) {
        $trans_words = ['快递', '顺丰', '中通', '申通'];
        $question_words = ['？', '吗', '什么', '呀', '讶'];
        if($this->hasWord($content, $trans_words) && $this->hasWord($content, $question_words)){
            return true;
        }
        return false;
    }

    private function isSectionThree ($content) {
        $trans_words = ['快递', '顺丰', '中通', '申通'];
        $worry_words = ['出问题', '害怕', '才', '查不到'];
        if($this->hasWord($content, $trans_words) && $this->hasWord($content, $worry_words)){
            return true;
        }
        return false;
    }

    private function isSectionFour ($content) {
        $menzhen_words = ['门诊'];
        $question_words = ['？', '吗', '什么', '呀', '讶'];
        if($this->hasWord($content, $menzhen_words) && $this->hasWord($content, $question_words)){
            return true;
        }
        return false;
    }

    private function hasWord ($content, $arr) {
        foreach($arr as $k => $v){
            // strpos没有匹配到返回false，匹配到返回字符串中首次出现的索引位置：0，1，2，，，
            if(false !== strpos($content,$v)){
                return true;
            }
        }
        return false;
    }

    private function updateMsgOpTaskLevelImp ($optask, $str) {
        $level = $optask->level;
        $optask->level = $level < 4 ? 4 : $level;
        $optask->level_remark .= OpTask::getLevelRemark($str) . "\n";
    }

}
if ($argc < 2) {
    echo "usage: php " . basename(__FILE__) . "\n";
    exit(1);
}

$param = $argv[1];
echo "recieved data $param", "\n";
if (! $param) {
    echo "param is empty\n";
    exit(2);
}

$obj = new pipelevelworker();
$obj->run($param);

Debug::flushXworklog();
