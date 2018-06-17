<?php
$count_data = PaperService::getAdhd_ivData($_paper);
?>
<div class="adhd_count">
    <p>
        <span class="fb">总得分:</span><?=$count_data['scores']?>分 (无:0，偶尔:1，常常:2，总是:3)
    </p>
    <table class="table table-bordered tc">
        <tbody>
            <tr>
                <th></th>
                <th>无+偶尔</th>
                <th>常常+总是</th>
            </tr>
            <tr>
                <th>注意</th>
                <td><?=$count_data['count']['0']['无'] + $count_data['count']['0']['偶尔']?></td>
                <td><?=$count_data['count']['0']['常常'] + $count_data['count']['0']['总是']?></td>
            </tr>
            <tr>
                <th>多动+冲动</th>
                <td><?=$count_data['count']['1']['无'] + $count_data['count']['1']['偶尔'] + $count_data['count']['2']['无'] + $count_data['count']['2']['偶尔']?></td>
                <td><?=$count_data['count']['1']['常常'] + $count_data['count']['1']['总是'] + $count_data['count']['2']['常常'] + $count_data['count']['2']['总是']?></td>
            </tr>
        </tbody>
    </table>
</div>
