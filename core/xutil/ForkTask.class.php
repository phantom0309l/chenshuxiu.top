<?php
dl('pcntl.so');
// 机制
class ForkTask
{
    /*
     * 进入 /home/soft/php-5.2.4/ext/pcntl /usr/local/php5/bin/phpize ./configure
     * --with-php-config=/usr/local/php5/bin/php-config make&&make install
     */

    private $job;

    private $func;

    private $maxprocess;

    private static $process_count = 0;

    private function __construct ($job, $func, $maxprocess = 10) {
        $this->maxprocess = $maxprocess;
        $this->job = $job;
        $this->func = $func;
    }

    private function run () {
        $pid = pcntl_fork();

        // 父进程和子进程都必须清理一下
        BeanFinder::clear();

        if ($pid < 0)
            die("could not fork");
        elseif ($pid)         // we are the parent
        {
            self::$process_count ++;
            if (self::$process_count >= $this->maxprocess) {
                $childPid = pcntl_wait($status);
                self::$process_count --;
            }
        } else         // we are the child
        {
            $args = func_get_args();
            $method = $this->func;
            call_user_func_array(array(
                $this->job,
                $method), $args);
            exit();
        }
    }

    // 外部调用接口
    public static function createTask ($job, $func, $args, $maxprocess = 10) {
        if (Config::getConfig("iswindows")) {
            Debug::warn("iswindows");
            call_user_func_array(array(
                $job,
                $func), array(
                $args));
        } else {
            $task = new ForkTask($job, $func, $maxprocess);
            $task->run($args);
        }
    }
}

// 具体job类
class MyForkJob
{

    public function abc ($ids) {
        foreach ($ids as $id) {
            echo "$id ";
            sleep(1);
        }
    }

    public static function main () {
        for ($i = 0; $i < 100; $i ++) {
            $ids = array();

            $rand = rand(1, 3);
            for ($j = $i; $j < $i + $rand; $j ++) {
                $ids[] = $j;
            }

            ForkTask::createTask(new MyForkJob(), 'abc', $ids);
        }
    }
}
