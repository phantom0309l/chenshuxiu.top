<?php
/**
 * Created by PhpStorm.
 * User: fanghanwen
 * Date: 2018/2/24
 * Time: 13:36
 */

class Test
{
    public function dowork(){
        /*
         有一对兔子,从出生后第3个月起每个月都生一对兔子,小兔子长到第三个月后每个月又生一对兔子,假如兔子都不死,请编程输出两年内每个月的兔子总数为多少?
         * */

        $month = 5;

        $this->getResult($month);

        $cnt = $this->fun($month);
        echo $month.'个月后共有'.$cnt.'对兔子' . "\n";
    }

    public function getResult($month){
        $one = 1; //第一个月兔子的对数
        $two = 1; //第二个月兔子的对数
        $sum = 0; //第$month个月兔子的对数
        if($month < 3){
            return ;
        }
        for($i = 2;$i < $month; $i++){
            $sum = $one + $two;
            $one = $two;
            $two = $sum;
        }
        echo $month.'个月后共有'.$sum.'对兔子' . "\n";
    }

    public function fun($n){
        if($n == 1 || $n == 2){
            return 1;
        }else{
            return $this->fun($n-1)+$this->fun($n-2);
        }
    }
}

$test = new Test();
$test->dowork();
