<?php
$pagetitle = "患者症状体征列表 PatientRemarks";
$cssFiles = []; //也可以引用第三方文件，注意数组顺序，后面的样式覆盖前面的样式
$jsFiles = []; //填写完整地址
?>
<?php include_once $tpl . '/_header.new.tpl.php'; ?>

    <div class="col-md-12">
        <section class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered tdcenter">
                <thead>
                    <tr>
                        <th width="100">患者</th>
                        <th width="100">thedate</th>
                        <th width="100">revisitrecordid</th>
                        <th width="100">医生</th>
                        <th width="150">类型</th>
                        <th width="100">name</th>
                        <th>具体类容</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $arr = [
                        'symptom' => '症状体征',
                        'adverseevent' => '不良反应',
                    ];
                    foreach ($patientremarks as $a) {
                        ?>
                		    <tr>
        		    		    <td>
    		    		    	    <a href="/patientremarkmgr/list?patientid=<?=$a->patient->id?>"><?=$a->patient->name?></a>
    		    		    	</td>
    		    		    	<td>
                                    <a href="/patientremarkmgr/list?thedate=<?=$a->thedate?>"><?=$a->thedate?></a>
                                </td>
                            	<td>
                                    <a href="/patientremarkmgr/list?revisitrecordid=<?=$a->revisitrecordid?>"><?=$a->revisitrecordid?></a>
                                </td>
        		    		    <td>
    		    		    	    <a href="/patientremarkmgr/list?doctorid=<?=$a->doctorid?>"><?=$a->doctor->name?></a>
    		    		    	</td>
        		    		    <td>
        		    		        <a href="/patientremarkmgr/list?typestr=<?=$a->typestr ?>"><?=$arr["{$a->typestr}"]?></a>
        		    		    </td>
                		    		<td>
        		    		        <a href="/patientremarkmgr/list?name=<?=$a->name ?>"><?=$a->name?></a>
        		    		    </td>
        		    		    <td><?=$a->content?></td>
                		    </tr>
                        <?php
                    }
                    ?>
                    <tr>
                        <td colspan=100 class="pagelink">
                            	<?php include $dtpl . "/pagelink.ctr.php"; ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            </div>
        </section>
    </div>
    <div class="clear"></div>

    <?php include_once $tpl . '/_footer.new.tpl.php'; ?>
