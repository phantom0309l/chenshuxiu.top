<?php $drip_greenchannel = $a->obj; ?>
<div>
    <table class="green_channel">
        <tbody>
        <tr>
            <th>疾病</th>
            <td><?= $drip_greenchannel->diseasestr ?></td>
        </tr>
        <tr>
            <th>城市</th>
            <td><?= $drip_greenchannel->xcity->name ?></td>
        </tr>
<!--        <tr>-->
<!--            <th>医院</th>-->
<!--            <td>--><?//= $drip_greenchannel->hospital->name ?><!--</td>-->
<!--        </tr>-->
        <tr>
            <th>期望日期</th>
            <td><?= $drip_greenchannel->expecteddate ?> 至 <?= $drip_greenchannel->bounddate ?></td>
        </tr>
        <?php if($drip_greenchannel->status == 2) { ?>
            <tr>
                <th>实际日期</th>
                <td><?= $drip_greenchannel->actualdate ?></td>
            </tr>
            <tr>
                <th>发送内容</th>
                <td><?= $drip_greenchannel->content ?></td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>