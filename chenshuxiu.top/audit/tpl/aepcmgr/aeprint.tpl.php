<?php
$pagetitle = "AEPC详情";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
$pageStyle = <<<STYLE
    body {  margin: 0;  padding: 0; font-family: 'msyh'; }

    /* base */
    h1,h2,h3,h4,h5,h6{font-size:100%;}
    .fl{float:left;display:inline;}
    .fr{float:right;display:inline;}
    .tl{text-align:left;}
    .tc{text-align:center;}
    .tr{text-align:right;}
    .zoom{zoom:1}
    .cb{clear:both;}
    .cl{clear:left;}
    .cr{clear:right;}
    .clear{clear:both;font-size:0;height:0;line-height:0;}
    .clearfix:after{content:".";display:block;height:0;clear:both;visibility:hidden;}
    .clearfix{display:inline-block;}
    * html .clearfix{height:1%;}
    .clearfix{display:block;}
    .bc{margin-left:auto;margin-right:auto;}
    .pr{position:relative;}
    .pa{position:absolute;}
    .none{display:none;}
    .db{display:block;}
    .dil{display:inline-block;}
    .vm{vertical-align:middle;}
    .ov{overflow:auto;zoom:1;}
    .oh{overflow:hidden;}
    .m5{margin:5px;}
    .m10{margin:10px;}
    .m15{margin:15px;}
    .m20{margin:20px;}
    .mt5{margin-top:5px;}
    .mt10{margin-top:10px;}
    .mt15{margin-top:15px;}
    .mt20{margin-top:20px;}
    .mt25{margin-top:25px;}
    .mt30{margin-top:30px;}
    .mt35{margin-top:35px;}
    .mt40{margin-top:40px;}
    .mr5{margin-right:5px;}
    .mr10{margin-right:10px;}
    .mr15{margin-right:15px;}
    .mr20{margin-right:20px;}
    .mr25{margin-right:25px;}
    .mr30{margin-right:30px;}
    .mr35{margin-right:35px;}
    .mr40{margin-right:40px;}
    .mb5{margin-bottom:5px;}
    .mb10{margin-bottom:10px;}
    .mb15{margin-bottom:15px;}
    .mb20{margin-bottom:20px;}
    .mb25{margin-bottom:25px;}
    .mb30{margin-bottom:30px;}
    .mb35{margin-bottom:35px;}
    .mb40{margin-bottom:40px;}
    .ml5{margin-left:5px;}
    .ml10{margin-left:10px;}
    .ml15{margin-left:15px;}
    .ml20{margin-left:20px;}
    .ml25{margin-left:25px;}
    .ml30{margin-left:30px;}
    .ml35{margin-left:35px;}
    .ml40{margin-left:40px;}
    .p5{padding:5px;}
    .p10{padding:10px;}
    .p15{padding:15px;}
    .p20{padding:20px;}
    .pt5{padding-top:5px;}
    .pt10{padding-top:10px;}
    .pt15{padding-top:15px;}
    .pt20{padding-top:20px;}
    .pt25{padding-top:25px;}
    .pt30{padding-top:30px;}
    .pt35{padding-top:35px;}
    .pt40{padding-top:40px;}
    .pr5{padding-right:5px;}
    .pr10{padding-right:10px;}
    .pr15{padding-right:15px;}
    .pr20{padding-right:20px;}
    .pr25{padding-right:25px;}
    .pr30{padding-right:30px;}
    .pr35{padding-right:35px;}
    .pr40{padding-right:40px;}
    .pb5{padding-bottom:5px;}
    .pb10{padding-bottom:10px;}
    .pb15{padding-bottom:15px;}
    .pb20{padding-bottom:20px;}
    .pb25{padding-bottom:25px;}
    .pb30{padding-bottom:30px;}
    .pb35{padding-bottom:35px;}
    .pb40{padding-bottom:40px;}
    .pl5{padding-left:5px;}
    .pl10{padding-left:10px;}
    .pl15{padding-left:15px;}
    .pl20{padding-left:20px;}
    .pl25{padding-left:25px;}
    .pl30{padding-left:30px;}
    .pl35{padding-left:35px;}
    .pl40{padding-left:40px;}
    .white{color:#fff;}
    .gray{color:#ccc;}
    .gray2{color:#999;}
    .gray3{color:#666;}
    .black{color:#000;}
    .red{color:#f00;}
    .red2{color:#c00;}
    .red3{color:#830000;}
    .orange{color:#ff8400;}
    .orange2{color:#f60;}
    .orange3{color:#d53e16;}
    .yellow{color:#ff0;}
    .green{color:#358e00;}
    .blue{color:#09c;}
    .blue2{color:#186db4;}
    .blue3{color:#006699;}
    .f12{font-size:12px;}
    .f14{font-size:14px;}
    .f16{font-size:16px;}
    .f18{font-size:18px;}
    .f20{font-size:20px;}
    .f22{font-size:22px;}
    .f24{font-size:24px;}
    .f26{font-size:26px;}
    .f28{font-size:28px;}
    .f30{font-size:30px;}
    .f32{font-size:32px;}
    .f34{font-size:34px;}
    .f36{font-size:36px;}
    .fb{font-weight:bold;}
    .fn{font-weight:normal;}
    .f-yh{font-family:'微软雅黑';}
    .t2{text-indent:2em;}
    .lh140{line-height:140%;}
    .lh150{line-height:150%;}
    .lh180{line-height:180%;}
    .lh190{line-height:190%;}
    .unl{text-decoration:underline;}
    .no_unl{text-decoration:none;}
    .cp{cursor:pointer;}
    .w60{width:60px;}
    .w70{width:70px;}
    .w80{width:80px;}
    .w90{width:90px;}
    .w100{width:100px;}
    .w150{width:150px;}
    .w200{width:200px;}
    .w960{width:960px;}
    .w{width:100%;}
    .h20{height:20px;}
    .h25{height:25px;}
    .h30{height:30px;}
    .h35{height:35px;}
    .h40{height:40px;}
    .nb{border:none;}

    .container{ border: 1px solid #ddd; margin-left: auto; margin-right: auto; width: auto; font-size: 14px; border-bottom: none;}
    .ae-t{ background: #f1f1f1; text-align: center; border-bottom: 1px solid #f1f1f1; padding: 5px 0px;}
    .border-b{ border-bottom: 1px solid #ddd; }
    .ae-item-t{ border-bottom: 1px solid #ddd; background: #f1f1f1; padding: 5px; }
    .aetype-l{ width: 50%; padding: 5px 0px}
    .aetype-r{ width: 49%; border-left: 1px solid #ddd; padding: 5px 0px}
    .patientMsg-l{ width: 20%; padding: 5px 0px}
    .patientMsg-c{ width: 30%; border-left: 1px solid #ddd; padding: 5px 0px}
    .patientMsg-r{ width: 49%; border-left: 1px solid #ddd; padding: 5px 0px}
    .ae-item{ padding: 5px; border-bottom: 1px solid #ddd; }
    .table{ border-bottom: 1px solid #ddd; width: 100%; border-collapse: collapse;}
    .table td { border-collapse: collapse; border:1px solid #ddd; padding:5px;}
    .table td{ border-top:none; }
    .inputpos{ position: relative; top:5px; }

STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
    	<div class="container lh150">
    		<div class="ae-t">
    			<span class="f16">礼  来  公  司</span><br/>
    			<span>上  市  后  药  物/器  械  不  良  事  件  报  告  表</span>
    		</div>

    		<div class="border-b clearfix aetype">
    			<span class="fl aetype-l"><span class="pl5"><span class="gray2">礼来员工获知事件的日期：</span><?= AepcService::getContent($xanswersheetid, 275145696) ?></span></span>
    			<span class="fl aetype-r">
    				<span>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275148346, 275154046) ? 'checked' : ''?>/> 首次报告
    				</span>
    				<span>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275148346, 275154047) ? 'checked' : ''?> /> 跟踪报告（事件编号）
    				</span>
                    <?php if(AepcService::isChecked($xanswersheetid, 275148346, 275154047)){ ?>
                        <span><?= AepcService::getContent($xanswersheetid, 275148346) ?></span>
                    <?php } ?>
    			</span>
    		</div>

    		<div class="ae-item-t">1、报告者信息</div>

    		<div class="clearfix patientMsg border-b">
    			<span class="patientMsg-l fl">
    				<span class="pl5"><span class="gray2">姓名：</span><?= AepcService::getContent($xanswersheetid, 275151046) ?></span>
    			</span>
    			<span class="patientMsg-c fl">
    				<span class="pl5"><span class="gray2">联系电话：</span><?= AepcService::getContent($xanswersheetid, 275151476) ?></span>
    			</span>
    			<span class="patientMsg-r fl">
    				<span class="pl5"><span class="gray2">联系地址：</span><?= AepcService::getContent($xanswersheetid, 275151596) ?></span>
    			</span>
    		</div>
    		<div class="ae-item">
    			报告者类型：
    			<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275152286, 275155036) ? 'checked' : ''?> /> 医生/药师/护士
    			<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275152286, 275155037) ? 'checked' : ''?> /> 患者本人
    			<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275152286, 275155038) ? 'checked' : ''?> /> 患者家属
                （关系：<?= AepcService::isChecked($xanswersheetid, 275152286, 275155038) ? AepcService::getContent($xanswersheetid, 275152626) : ''?>）
    			<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275152286, 275155766) ? 'checked' : ''?> /> 其他
                <?php if(AepcService::isChecked($xanswersheetid, 275152286, 275155766)){ ?>
                    <span class="pl20"><?= AepcService::getContent($xanswersheetid, 275152286) ?></span>
                <?php } ?>
    		</div>

    		<div class="ae-item">
    			<div>
    				患者或其家属是否愿意提供治疗医生的联系方式：
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275157286, 275157287) ? 'checked' : ''?> /> 是（请填写以下信息）
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275157286, 275157288) ? 'checked' : ''?> /> 否
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275157286, 275157289) ? 'checked' : ''?> /> 联系方式不详
    			</div>
    			<div>
    				<span class="gray2">医生姓名：</span><span><?= AepcService::getContent($xanswersheetid, 275162726) ?></span>
    				<span class="gray2">联系电话：</span><span><?= AepcService::getContent($xanswersheetid, 275163046) ?></span>
    				<span class="gray2">医院/科室：</span><span><?= AepcService::getContent($xanswersheetid, 275163386) ?></span>
    			</div>
    		</div>

    		<div class="ae-item-t">2、患者信息</div>

            <div class="table-responsive">
    			<table class="table">
    				<tr>
    					<td style="border-left:none;"><span class="gray2">姓名：</span><?= AepcService::getContent($xanswersheetid, 275165596) ?></td>
    					<td><span class="gray2">出生日期：</span><span><?= AepcService::getContent($xanswersheetid, 275165946) ?></span>/年龄：<span><?= AepcService::getContent($xanswersheetid, 275263016) ?></span></td>
    					<td><span class="gray2">性别：</span><span><?= AepcService::getSex($xanswersheetid, 275166686) ?></span></td>
    					<td>
    						<span class="gray2">身高：</span><span><?= AepcService::getContent($xanswersheetid, 275168526) ?></span>cm <br/>
    						<span class="gray2">体重：</span><span><?= AepcService::getContent($xanswersheetid, 275168876) ?></span>kg
    					</td>
    					<td><span class="gray2">民族：</span><span><?= AepcService::getContent($xanswersheetid, 275169726) ?></span></td>
    					<td style="border-right:none;"><span class="gray2">联系方式：</span><span><?= AepcService::getContent($xanswersheetid, 275169876) ?></span></td>
    				</tr>
    			</table>
    		</div>

    		<div class="ae-item">
    			<div>
    				<span class="gray2">既往病史(合并疾病，过敏史，家族史)：</span><span><?= AepcService::getContent($xanswersheetid, 275170106) ?></span>
    			</div>
    			<div>
    				<span>
    				既往药品不良反应/事件：
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275172116, 275172117) ? 'checked' : ''?> /> 有
                    <?php if(AepcService::isChecked($xanswersheetid, 275172116, 275172117)){ ?>
                        <span class="pl10"><?= AepcService::getContent($xanswersheetid, 275173226) ?></span>
                    <?php } ?>
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275172116, 275172118) ? 'checked' : ''?> /> 无
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275172116, 275172119) ? 'checked' : ''?> /> 不详
    				</span>
    				<span class="pl20">
    				家族药品不良反应/事件：
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275173936, 275173937) ? 'checked' : ''?> /> 有
                    <?php if(AepcService::isChecked($xanswersheetid, 275173936, 275173937)){ ?>
                        <span class="pl10"><?= AepcService::getContent($xanswersheetid, 275174136) ?></span>
                    <?php } ?>
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275173936, 275173938) ? 'checked' : ''?> /> 无
    				<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275173936, 275173939) ? 'checked' : ''?> /> 不详
    				</span>
    			</div>
    		</div>

    		<div class="ae-item-t">
    			3、怀疑产品<span class="f12">（所使用礼来产品含药物、器械）</span>
    		</div>

            <div class="table-responsive">
    			<table class="table">
    				<tr class="gray2">
    					<td>产品名称</td>
    					<td>生产厂家</td>
    					<td>适应症</td>
    					<td>生产批号</td>
    					<td>剂型</td>
    					<td>单次剂量</td>
    					<td>使用频率</td>
    					<td>给药途径</td>
    					<td>开始时间</td>
    					<td>结束时间</td>
    				</tr>
                    <?php $products = AepcService::getHuaiYiProducts($xanswersheetid) ?>
                    <?php  if( count($products) ){ ?>
                        <?php foreach($products as $a){ ?>
                            <tr>
            					<td><div><?= $a[0] ?></div></td>
            					<td><div><?= $a[1] ?></div></td>
            					<td><div><?= $a[2] ?></div></td>
            					<td><div><?= $a[3] ?></div></td>
            					<td><div><?= $a[4] ?></div></td>
            					<td><div><?= $a[5] ?></div></td>
            					<td><div><?= $a[6] ?></div></td>
            					<td><div><?= $a[7] ?></div></td>
            					<td><div><?= $a[8] ?></div></td>
            					<td><div><?= $a[9] ?></div></td>
            				</tr>
                        <?php } ?>
                    <?php  }else{ ?>
    				<tr>
    					<td></td>
    					<td>礼来</td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    				</tr>
    				<tr>
    					<td></td>
    					<td>礼来</td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    				</tr>
    				<tr>
    					<td></td>
    					<td>礼来</td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    					<td></td>
    				</tr>
                    <?php  } ?>
    			</table>
    		</div>

    		<div class="ae-item-t">
    			4、合并用药<span class="f12">（不良事件发生前/时所使用的除礼来产品以外的药物）</span>
    		</div>

            <div class="table-responsive">
                <?php $medicines = AepcService::getHeBingMedicines($xanswersheetid) ?>
    			<table class="table table-bordered">
    				<tr class="gray2">
    					<td style="width:100px;">产品名称</td>
    					<td style="width:80px;">生产厂家</td>
    					<td style="width:100px;">适应症</td>
    					<td>生产批号</td>
    					<td>剂型</td>
    					<td>单次剂量</td>
    					<td style="width:100px;">使用频率</td>
    					<td style="width:80px;">给药途径</td>
    					<td style="width:80px;">开始时间</td>
    					<td style="width:80px;">结束时间</td>
    				</tr>
                    <?php  if( count($medicines) ){ ?>
                        <?php foreach($medicines as $a){ ?>
                            <tr>
            					<td><div><?= $a[0] ?></div></td>
            					<td><div><?= $a[1] ?></div></td>
            					<td><div><?= $a[2] ?></div></td>
            					<td><div><?= $a[3] ?></div></td>
            					<td><div><?= $a[4] ?></div></td>
            					<td><div><?= $a[5] ?></div></td>
            					<td><div><?= $a[6] ?></div></td>
            					<td><div><?= $a[7] ?></div></td>
            					<td><div><?= $a[8] ?></div></td>
            					<td><div><?= $a[9] ?></div></td>
            				</tr>
                        <?php } ?>
                    <?php  }else{ ?>
                        <tr>
                            <td>　</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td>　</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php  } ?>
    			</table>
    		</div>

    		<div class="ae-item-t">5、不良事件描述</div>

    		<div class="p5 border-b">
    			<div class="gray2">事件开始时间，症状，体征，实验室检查，诊断，事件进展，治疗措施，事件转归，结束时间：</div>
    			<div><?= AepcService::getContent($xanswersheetid, 275179026) ?></div>
    			<div class="mt10">
                    <div>
    				不良事件结果：
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275180286, 275180287) ? 'checked' : ''?> /> 治愈</span>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275180286, 275180288) ? 'checked' : ''?> /> 好转</span>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275180286, 275180289) ? 'checked' : ''?> /> 未好转</span>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275180286, 275180290) ? 'checked' : ''?> /> 有后遗症，请说明：
                        <?php if(AepcService::isChecked($xanswersheetid, 275180286, 275180290)){ ?>
                            <span><?= AepcService::getContent($xanswersheetid, 275180916) ?></span>
                        <?php } ?>
                    </span>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275180286, 275180291) ? 'checked' : ''?> /> 不祥</span>
                    <span><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275180286, 275181656) ? 'checked' : ''?> /> 死亡</span>
                    </div>
    			</div>

                <div>
                    <span><span class="gray2">直接死因：</span><?= AepcService::getContent($xanswersheetid, 275182416) ?></span>
                    <span class="pl20"><span class="gray2">死亡时间：</span><?= AepcService::getContent($xanswersheetid, 275183226) ?></span>
                </div>

    			<div class="mt5">
    				对怀疑用药：
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275185446, 275185447) ? 'checked' : ''?> /> 未采取任何措施</span>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275185446, 275185448) ? 'checked' : ''?> /> 停用药物</span>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275185446, 275185449) ? 'checked' : ''?> /> 剂量改变，请说明：
                        <?php if(AepcService::isChecked($xanswersheetid, 275185446, 275185449)){ ?>
                            <span class="gray2"><?= AepcService::getContent($xanswersheetid, 275185446) ?></span>
                        <?php } ?>
                    </span>
    			</div>
                <?php
                    $xoptions = XOption::getListByXquestionid(275186136);
                ?>
    			<div class="mt5">
    				停药或剂量改变以后，不良事件是否消失或减轻？
                    <?php foreach($xoptions as $a){ ?>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275186136, $a->id) ? 'checked' : ''?> /> <?= $a->content ?></span>
                    <?php } ?>
    			</div>

                <?php
                    $xoptions = XOption::getListByXquestionid(275191906);
                ?>
    			<div class="mt5">
    				再次服药后，不良事件是否再次出现？
                    <?php foreach($xoptions as $a){ ?>
    				<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275191906, $a->id) ? 'checked' : ''?> /> <?= $a->content ?></span>
                    <?php } ?>
    			</div>

    		</div>

            <?php
                $xoptions = XOption::getListByXquestionid(275192196);
            ?>
    		<div class="ae-item-t">
    			报告人相关性评价：
                <?php foreach($xoptions as $a){ ?>
    			<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275192196, $a->id) ? 'checked' : ''?> /> <?= $a->content ?></span>
                <?php } ?>
    		</div>

            <?php
                $xoptions = XOption::getListByXquestionid(275192786);
            ?>

    		<div class="p5 border-b">
    			是否愿意接受电话随访：
                <?php foreach($xoptions as $a){ ?>
    			<span class="pr20"><input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275192786, $a->id) ? 'checked' : ''?> /> <?= $a->content ?></span>
                <?php } ?>
    		</div>
    		<div class="p5 tr border-b">
    			<span class="pr20">填表人：<?= AepcService::getContent($xanswersheetid, 275193266) ?></span>
    			<span>事件编号：<?= $event_no ?></span>
    		</div>
    	</div>
    </section>
</div>
<div class="clear"></div>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
