<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Jiazhuangxian extends CheckupImp
{
    public function init(array $a)
    {
        $this->out_case_no =$this->getFixStr($a['病历号']);
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院']);
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('jiazhuangxiangongneng', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        //游离三碘甲状腺原氨酸
        $sheets['XQuestionSheet']['106312191']['106312192']['content'] = $a['游离三碘甲状腺原氨酸'];
        $sheets['XQuestionSheet']['106312191']['106312192']['qualitative'] = '';

        //游离甲状腺素
        $sheets['XQuestionSheet']['106312191']['106312193']['content'] = $a['游离甲状腺素'];
        $sheets['XQuestionSheet']['106312191']['106312193']['qualitative'] = '';

        //甲状腺素
        $sheets['XQuestionSheet']['106312191']['106312194']['content'] = $a['三碘甲状腺原氨酸'];
        $sheets['XQuestionSheet']['106312191']['106312194']['qualitative'] = '';

        //三碘甲状腺原氨酸
        $sheets['XQuestionSheet']['106312191']['106312195']['content'] = $a['甲状腺素'];
        $sheets['XQuestionSheet']['106312191']['106312195']['qualitative'] = '';

        //促甲状腺激素
        $sheets['XQuestionSheet']['106312191']['106312196']['content'] = $a['促甲状腺激素'];
        $sheets['XQuestionSheet']['106312191']['106312196']['qualitative'] = '';

        //促甲状腺激素
        $sheets['XQuestionSheet']['106312191']['106312197']['content'] = $a['甲状腺球蛋白抗体'];
        $sheets['XQuestionSheet']['106312191']['106312197']['qualitative'] = '';

        //促甲状腺激素
        $sheets['XQuestionSheet']['106312191']['106312198']['content'] = $a['甲状腺过氧化物酶抗体'];
        $sheets['XQuestionSheet']['106312191']['106312198']['qualitative'] = '';

        //备注
        $sheets['XQuestionSheet']['106312191']['106312199']['content'] = '';

        $this->sheets = $sheets;
    }
}
