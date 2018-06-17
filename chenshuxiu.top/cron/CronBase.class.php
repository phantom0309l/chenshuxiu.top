<?php

abstract class CronBase
{

    private $filepath = '';

    private $process_name = '';

    private $crontab = null;

    private $cronlog = null;

    protected $cronlog_brief = '';

    // 需要子类复写
    protected $cronlog_content = '';

    // 需要子类复写
    public function __construct ($file) {
        $arr = explode('/', $file);
        $this->process_name = array_pop($arr);
        $dir2 = array_pop($arr);
        $dir1 = array_pop($arr);
        $this->filepath = "{$dir1}/{$dir2}/{$this->process_name}";
    }

    public function dowork () {
        $this->doBefore();
        $this->doWorkImp();
        $this->doAfter();
    }

    // doBefore
    protected function doBefore () {
        echo "\n\n-----begin----- " . date('Y-m-d H:i:s');
        Debug::trace("=====[cron][beg][{$this->process_name}]=====");

        $unitofwork = BeanFinder::get("UnitOfWork");

        $crontab = CronTabDao::getByProcess_name($this->process_name);

        // 需要子类重载
        $row = $this->getRowForCronTab();

        if (false == $crontab instanceof CronTab) {

            $row["process_name"] = $this->process_name;
            $row["filepath"] = $this->filepath;
            $crontab = CronTab::createByBiz($row);
        } else {
            // 修正一下
            $crontab->type = $row['type'];
            $crontab->when = $row['when'];
            $crontab->title = $row['title'];

            // 修正 commit_fix_cnt
            $unitOfWork = BeanFinder::get('UnitOfWork');
            $commit_fix_cnt = $unitOfWork->getInfoForXunitofwork('commit_fix_cnt');
            $unitOfWork->setInfoForXunitofwork('commit_fix_cnt', $commit_fix_cnt - 1);
        }

        $crontab->lastcrontime = date('Y-m-d H:i:s');

        $this->crontab = $crontab;
        $this->cronlog = CronLog::createByCron($crontab);

        $unitofwork->commitAndInit();
    }

    // doAfter
    protected function doAfter () {
        // cronlog 修改 -----begin-----
        $unitofwork = BeanFinder::get("UnitOfWork");
        $cronlog = CronLog::getById($this->cronlog->id, 'statdb');

        if ($this->needCronlog()) {
            $cronlog->endtime = date('Y-m-d H:i:s');
            $cronlog->brief = trim($this->cronlog_brief);
            $cronlog->content = trim($this->cronlog_content);
        } else {

            // 修正 commit_fix_cnt
            $unitOfWork = BeanFinder::get('UnitOfWork');
            $commit_fix_cnt = $unitOfWork->getInfoForXunitofwork('commit_fix_cnt');
            $unitOfWork->setInfoForXunitofwork('commit_fix_cnt', $commit_fix_cnt - 1);

            $cronlog->remove();
        }
        $unitofwork->commitAndInit();
        // cronlog 修改 -----end-----

        Debug::trace("=====[cron][end][{$this->process_name}]=====");
        if ($this->needFlushXworklog()) {
            Debug::flushXworklog();
        }
        echo "\n-----end----- " . date('Y-m-d H:i:s') . "\n";
    }

    // getRowForCronTab, 需要重载
    // $row = array();
    // $row["type"] = CronTab::optask;
    // $row["when"] = 'daily';
    // $row["title"] = '每天, 10:25, 给昨天未填写信息核对的患者再次发送';
    abstract protected function getRowForCronTab ();

    // 是否记xworklog, 需要重载
    abstract protected function needFlushXworklog ();

    // 是否记cronlog, 需要重载
    abstract protected function needCronlog ();

    // 模板方法的实现, 需要重载
    abstract protected function doworkImp ();
}
