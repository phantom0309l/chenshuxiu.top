<?php
    $_revisittkt = $a->obj;
    if($_revisittkt instanceof RevisitTkt){
    ?>
        <table class="table table-bordered">
            <tr>
                <th width=90>加号单</th>
                <td><?= $_revisittkt->id; ?></td>
            </tr>
            <tr>
                <th>所属医生</th>
                <td><?= $_revisittkt->patient->doctor->name; ?></td>
            </tr>
            <tr>
                <th>创建人</th>
                <td><?= $_revisittkt->getCreatebyStr(); ?></td>
            </tr>
            <tr>
                <th>预约日期</th>
                <td><?= $_revisittkt->thedate  ?></td>
            </tr>
            <tr>
                <th>患者说:</th>
                <td><?= $_revisittkt->patient_content  ?></td>
            </tr>
            <tr>
                <th>当前状态</th>
                <td><?= $_revisittkt->getStatusStrWithColor()  ?></td>
            </tr>
            <tr>
                <th>关闭</th>
                <td><?= $_revisittkt->getIsclosedStr()  ?></td>
            </tr>
            <tr>
                <th>审核状态</th>
                <td>
                    <?php if ( $_revisittkt->auditstatus == 1) {?>
                        <span style='color: green;'>已审核通过</span>
                    <?php }else if($_revisittkt->auditstatus == '0'){?>
                        <span style='color: blue;'>审核中</span>
                    <?php } else {?>
                        <span class="red">已拒绝</span>
                        <span>拒绝原因:</span><?=$_revisittkt->auditremark ?>
                        <?php
                    }
                    ?>
                </td>
            </tr>
        </table>
        <?php
    }
?>
