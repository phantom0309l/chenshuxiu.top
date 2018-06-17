<?php
include_once(dirname(__FILE__) . "/../Common/CheckupImp.class.php");

class Naojiye extends CheckupImp
{
    public function getFixOption($selected)
    {
        $options = array();

        /*
            --量表中的选项
            1型: 血清及脑脊液均无条带
            2型: 脑脊液有2条或2条以上条带而血清没有
            3型: 血清与脑脊液均有条带 但脑脊液条带较血清多
            4型: 血清与脑脊液相同条带 呈镜像分布

            --实际导出的选项
            1型:血清及脑脊液均无条带
            2型:脑脊液有2条或2条以上条带而血清没有
            3型:血清与脑脊液均有条带,但脑脊液条带较血清多
            4型:血清与脑脊液 相同条带,呈镜像分布
        */
        $options['1型:血清及脑脊液均无条带'] = '1型：血清及脑脊液均无条带';
        $options['2型:脑脊液有2条或2条以上条带而血清没有'] = '2型：脑脊液有2条或2条以上条带而血清没有';
        $options['3型:血清与脑脊液均有条带,但脑脊液条带较血清多'] = '3型：血清与脑脊液均有条带 但脑脊液条带较血清多';
        $options['4型:血清与脑脊液 相同条带,呈镜像分布'] = '4型：血清与脑脊液相同条带 呈镜像分布';

        return $options[$selected];
    }

    public function init(array $a)
    {
        $this->out_case_no =$this->getFixStr($a['病历号']);
        $this->check_date = $this->getFixStr($a['化验日期'], '0000-00-00');
        $this->createtime = $this->getFixStr($a['录入日期'], '0000-00-00');
        $this->hospitalstr = $this->getFixStr($a['检查医院']);
        $this->checkuptplid = $this->getCheckuptplidByEnameAndDoctorid('naojiyejiancha', 33);
    }

    // >>>>>
    public function createSheets(array $a)
    {
        $sheets = array();

        $sheets['XQuestionSheet']['106312319']['106312320']['content'] = '颜色及压力';

        $sheets['XQuestionSheet']['106312319']['106312321']['options'][0] = XOption::getByXQuestionidAndContent(106312321, $this->getFixStr($a['颜色']))->id;

        $sheets['XQuestionSheet']['106312319']['106312329']['content'] = $this->getFixStr($a['压力']);
        $sheets['XQuestionSheet']['106312319']['106312329']['qualitative'] = '';

        $sheets['XQuestionSheet']['106312319']['106312330']['content'] = $this->getFixStr($a['备注']);

        $sheets['XQuestionSheet']['106312319']['106312331']['content'] = '常规';

        $sheets['XQuestionSheet']['106312319']['106312332']['content'] = $this->getFixStr($a['WBC细胞计数']);
        $sheets['XQuestionSheet']['106312319']['106312332']['qualitative'] = '';

        $sheets['XQuestionSheet']['106312319']['106312333']['content'] = $this->getFixStr($a['细胞总数']);
        $sheets['XQuestionSheet']['106312319']['106312333']['qualitative'] = '';

        $sheets['XQuestionSheet']['106312319']['106312334']['content'] = '生化';

        $sheets['XQuestionSheet']['106312319']['106312335']['content'] = $this->getFixStr($a['葡萄糖定量']);
        $sheets['XQuestionSheet']['106312319']['106312335']['qualitative'] = '';

        $sheets['XQuestionSheet']['106312319']['106312336']['content'] = $this->getFixStr($a['氯化物定量']);
        $sheets['XQuestionSheet']['106312319']['106312336']['qualitative'] = '';

        $sheets['XQuestionSheet']['106312319']['106312337']['content'] = $this->getFixStr($a['蛋白定量']);
        $sheets['XQuestionSheet']['106312319']['106312337']['qualitative'] = '';

        $sheets['XQuestionSheet']['106312319']['106312338']['content'] = 'OCB(寡克隆带）';

        $sheets['XQuestionSheet']['106312319']['106312339'] = $this->selectAndOther(106312339, $this->getFixOption($this->getFixStr($a['OCB(寡克隆带）'])));

        $sheets['XQuestionSheet']['106312319']['106312347']['options'][0] = XOption::getByXQuestionidAndContent(106312347, $this->getFixStr($a['IgG合成率']))->id;

        $sheets['XQuestionSheet']['106312319']['106312351']['content'] = '抗体';

        $sheets['XQuestionSheet']['106312319']['106312352']['options'][0] = XOption::getByXQuestionidAndContent(106312352, $this->getFixStr($a['AQP4IgG']))->id;
        $sheets['XQuestionSheet']['106312319']['106312352']['qualitative'] = '';

        $sheets['XQuestionSheet']['106312319']['106312358']['options'][0] = XOption::getByXQuestionidAndContent(106312358, $this->getFixStr($a['NMO-IgG']))->id;
        $sheets['XQuestionSheet']['106312319']['106312358']['qualitative'] = '';

        //导出的数据中没有MOG-IgG这列
        $sheets['XQuestionSheet']['106312319']['106312364']['options'][0] = '';
        $sheets['XQuestionSheet']['106312319']['106312364']['qualitative'] = '';

        $this->sheets = $sheets;
    }
}
