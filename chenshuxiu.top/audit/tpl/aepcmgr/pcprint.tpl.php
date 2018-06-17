<?php
$pagetitle = "微信服务号[{$wxshop->name}]菜单";
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

    .container{ margin-left: auto; margin-right: auto; width: auto; font-size: 14px; }
    .pc-t{ background: #f1f1f1; text-align: center; border-bottom: 1px solid #f1f1f1; padding: 5px 0px;}
    .border-b{ border-bottom: 1px solid #ddd; }
    .table{ border: 1px solid #ddd; width: 100%; border-collapse: collapse;}
    .table td { border-collapse: collapse; border:1px solid #ddd; padding:5px;}
    .table td{ border-top:none; }
    .inputpos{ position: relative; top:5px; }
    .bg{ background: #f1f1f1; }

STYLE;
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

<div class="col-md-12">
    <section class="col-md-12">
    	<div class="container lh150">
    		<div class="pt5 pb5 tc">
    			<span class="f18">产品投诉信息收集表</span>
    		</div>

    		<div class="mt10 mb10">获知日期：<?= AepcService::getContent($xanswersheetid, 275209806) ?></div>

            <div class="table-responsive">
    		    <table class="table">
    			<tr class="tc bg f16">
    				<td colspan="3">投诉人（患者）信息</td>
    			</tr>

    			<tr>
    				<td rowspan="2" width="20%" class="tc bg">
    					投诉来源<br/><span class="f12">(选择一项)</span>
    				</td>
    				<td width="30%">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275217786, 275217787) ? 'checked' : ''?> /> 患者投诉
    				</td>
    				<td>患者姓名：<?= AepcService::getContent($xanswersheetid, 275218586) ?></td>
    			</tr>

    			<tr>
    				<td>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275217786, 275217788) ? 'checked' : ''?> /> 非患者投诉
    				</td>
    				<td>投诉人姓名：<?= AepcService::getContent($xanswersheetid, 275218806) ?></td>
    			</tr>

    			<tr>
    				<td width="50%" colspan="2">联系电话：<?= AepcService::getContent($xanswersheetid, 275219426) ?></td>
    				<td>地址：<?= AepcService::getContent($xanswersheetid, 275219646) ?></td>
    			</tr>

    			<tr>
    				<td width="20%">
    					性别：<?= AepcService::getSex($xanswersheetid, 275273166) ?>
    				</td>
    				<td>城市/省份：<?= AepcService::getContent($xanswersheetid, 275273396) ?></td>
    				<td>
    					国家：<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275273766, 275273767) ? 'checked' : ''?> /> 中国
                        <input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275273766, 275273768) ? 'checked' : ''?> /> 其他：
                        <?php if(AepcService::isChecked($xanswersheetid, 275273766, 275273768)){ ?>
                            <span class="pl20"><?= AepcService::getContent($xanswersheetid, 275273766) ?></span>
                        <?php } ?>
    				</td>
    			</tr>

    			<tr>
    				<td rowspan="3" width="20%" class="tc bg">
    					投诉人类型<br/><span class="f12">(选择一项)</span>
    				</td>
    				<td width="30%">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275274686, 275274687) ? 'checked' : ''?> /> 卫生保健专业人士
    				</td>
    				<td>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275274686, 275274688) ? 'checked' : ''?> /> 使用者
    				</td>
    			</tr>

    			<tr>
    				<td width="30%">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275274686, 275274689) ? 'checked' : ''?> /> 礼来员工
    				</td>
    				<td>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275274686, 275274690) ? 'checked' : ''?> /> 机构(医院，药房，经销商，政府机关等)
    				</td>
    			</tr>

    			<tr>
    				<td colspan="2">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275274686, 275274691) ? 'checked' : ''?> /> 其他
                        <?php if(AepcService::isChecked($xanswersheetid, 275274686, 275274691)){ ?>
                            <span class="pl20"><?= AepcService::getContent($xanswersheetid, 275274686) ?></span>
                        <?php } ?>
    				</td>
    			</tr>

    		</table>
            </div>

            <div class="table-responsive">
    		    <table class="table" style="border-top:none;">
    			<tr class="tc bg f16">
    				<td colspan="3">报告人信息</td>
    			</tr>

    			<tr>
    				<td rowspan="2" width="20%" class="tc">
    					报告人来源<br/><span class="f12">(选择一项)</span>
    				</td>
    				<td width="30%">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275275716, 275275717) ? 'checked' : ''?> /> 外部
    				</td>
    				<td>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275275716, 275275718) ? 'checked' : ''?> /> 内部
    				</td>
    			</tr>

    			<tr>
    				<td colspan="2">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275275716, 275275719) ? 'checked' : ''?> /> 与投诉人一致（如果一致，以下信息可不填写）
    				</td>
    			</tr>

    			<tr>
    				<td width="50%" colspan="2">报告人姓名：<?= AepcService::getContent($xanswersheetid, 275276006) ?></td>
    				<td>性别：<?= AepcService::getSex($xanswersheetid, 275276106) ?></td>
    			</tr>

    			<tr>
    				<td width="50%" colspan="2">联系电话：<?= AepcService::getContent($xanswersheetid, 275276326) ?></td>
    				<td>地址：<?= AepcService::getContent($xanswersheetid, 275276576) ?></td>
    			</tr>

    			<tr>
    				<td width="50%" colspan="2">城市/省份：<?= AepcService::getContent($xanswersheetid, 275276706) ?></td>
    				<td>国家：<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275277026, 275277027) ? 'checked' : ''?> /> 中国
                        <input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275277026, 275277028) ? 'checked' : ''?> /> 其他：
                        <?php if(AepcService::isChecked($xanswersheetid, 275277026, 275277028)){ ?>
                            <span class="pl20"><?= AepcService::getContent($xanswersheetid, 275277026) ?></span>
                        <?php } ?>
                    </td>
    			</tr>

    			<tr>
    				<td rowspan="3" width="20%" class="tc bg">
    					报告人类型<br/><span class="f12">(选择一项)</span>
    				</td>
    				<td width="30%">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275277596, 275277597) ? 'checked' : ''?> /> 卫生保健专业人士
    				</td>
    				<td>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275277596, 275277598) ? 'checked' : ''?> /> 使用者
    				</td>
    			</tr>

    			<tr>
    				<td width="30%">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275277596, 275277599) ? 'checked' : ''?> /> 礼来员工
    				</td>
    				<td>
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275277596, 275277600) ? 'checked' : ''?> /> 机构(医院，药房，经销商，政府机关等)
    				</td>
    			</tr>

    			<tr>
    				<td colspan="2">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275277596, 275277601) ? 'checked' : ''?> /> 其他
                        <?php if(AepcService::isChecked($xanswersheetid, 275277596, 275277601)){ ?>
                            <span class="pl20"><?= AepcService::getContent($xanswersheetid, 275277596) ?></span>
                        <?php } ?>
    				</td>
    			</tr>

    		</table>
            </div>

            <div class="table-responsive">
    		    <table class="table" style="border-top:none;">
    			<tr class="tc bg f16">
    				<td colspan="4">投诉内容</td>
    			</tr>

    			<tr class="f12 bg">
    				<td colspan="4">为了保证得到足够的信息以对投诉进行调查，请至少填写下面的表格中带*的信息：</td>
    			</tr>

    			<tr>
    				<td width="25%" class="bg">*产品名称</td>
    				<td colspan="3"><?= AepcService::getContent($xanswersheetid, 275279216) ?></td>
    			</tr>

    			<tr>
    				<td class="bg">*生产批号</td>
    				<td width="25%"><?= AepcService::getContent($xanswersheetid, 275279256) ?></td>
    				<td rowspan="2" width="25%" class="bg">*涉及的产品数量<br/><span class="f12">（涉及多个批号时需标注批号及对应的数量）</span></td>
    				<td rowspan="2"><?= AepcService::getContent($xanswersheetid, 275279586) ?></td>
    			</tr>

    			<tr>
    				<td class="bg">*分包装批号<span class="f12">（如适用）</span></td>
    				<td><?= AepcService::getContent($xanswersheetid, 275279326) ?></td>
    			</tr>

    			<tr>
    				<td class="bg">*生产日期</td>
    				<td><?= AepcService::getContent($xanswersheetid, 275279636) ?></td>
    				<td class="bg">*有效期</td>
    				<td><?= AepcService::getContent($xanswersheetid, 275279796) ?></td>
    			</tr>

    			<tr>
    				<td class="bg">*电子监管码<span class="f12">（如适用）</span></td>
    				<td><?= AepcService::getContent($xanswersheetid, 275280056) ?></td>
    				<td class="bg">生产工厂</td>
    				<td><?= AepcService::getContent($xanswersheetid, 275280146) ?></td>
    			</tr>

    			<tr>
    				<td class="bg">*购买地址</td>
    				<td colspan="3"><?= AepcService::getContent($xanswersheetid, 275280226) ?></td>
    			</tr>

    			<tr>
    				<td class="bg">产品规格</td>
    				<td><?= AepcService::getContent($xanswersheetid, 275280346) ?></td>
    				<td class="bg">产品代码</td>
    				<td><?= AepcService::getContent($xanswersheetid, 275280526) ?></td>
    			</tr>

    			<tr>
    				<td class="bg">剂型</td>
    				<td colspan="3">
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275280896, 275280897) ? 'checked' : ''?>/> 片剂
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275280896, 275280898) ? 'checked' : ''?>/> 胶囊剂
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275280896, 275280899) ? 'checked' : ''?>/> 口服混悬剂
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275280896, 275280900) ? 'checked' : ''?>/> 注射剂
    					<input type="checkbox" <?= AepcService::isChecked($xanswersheetid, 275280896, 275280901) ? 'checked' : ''?>/> 医疗器械
    				</td>
    			</tr>

    			<tr class="bg">
    				<td colspan="4">请按照投诉人原话描述投诉内容 <span class="f12">(如，发生了什么事，如何发生，产品在使用前还是使用后出现的问题，客户是否愿意返还投诉产品等)。</span></td>
    			</tr>

    			<tr>
    				<td colspan="4">
    				<?= AepcService::getContent($xanswersheetid, 275281716) ?>
    				</td>
    			</tr>

    		</table>
            </div>
            <div class="p5 tr">
    			<span>事件编号：<?= $xanswersheetid ?></span>
    		</div>
    	</div>
    </section>
</div>
<div class="clear"></div>

<?php include_once $tpl . '/_footer.new.tpl.php'; ?>
