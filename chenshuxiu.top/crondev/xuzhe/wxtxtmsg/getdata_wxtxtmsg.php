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

class GetData_wxtxtmsg
{

    public function dowork () {

        echo "\n [GetData_wxtxtmsg] begin ";

        $this->setPatientids();
        $this->getData();

        echo "\n [GetData_wxtxtmsg] finished \n";

    }

    private $patientids = array();

    private function write2file ( $data ) {
        $file = fopen("result.txt", 'a');

        $filecontent = "##xuzhe##";
        $filecontent .= $data;

        fwrite($file, $filecontent);
    }

    private function setPatientids () {
        $sql = "select  p.id
            from patients p
            join users u on u.patientid=p.id
            where u.id<10000 or u.id>20000
            and p.diseaseid=1";
        $this->patientids = Dao::queryValues($sql);
    }

    private function getData () {

        $patientids = $this->patientids;
        $patients_cnt = count($patientids);

        foreach ($patientids as $index => $patientid) {
            $unitofwork = BeanFinder::get("UnitOfWork");
            $now = date("H:i:s");
            echo "\n [{$now}] {$index}/{$patients_cnt}";

            $temparr = array();
            $cond = " and patientid={$patientid}
            and (( objtype='PushMsg' and objcode='byAuditor' ) or (objtype='WxTxtMsg'))
            order by id asc";

            $pipes = Dao::getEntityListByCond('Pipe',$cond);
            $cnt = count($pipes);
            echo " [{$cnt}]";
            foreach( $pipes as $pipe ){
                if( $pipe->obj == 'PushMsg' ){
                    if( $pipe->obj->send_by_way ==  'template'){
                        continue;
                    }
                }
                $temparr[] = $pipe->obj->content;
            }
            $data = implode('**xuzhe**',$temparr);

            $this->write2file( $data );

            unset($temparr);
            unset($data);
            unset($pipes);
            $unitofwork->Init();
        }

    }

}

$process = new GetData_wxtxtmsg();
$process->dowork();
