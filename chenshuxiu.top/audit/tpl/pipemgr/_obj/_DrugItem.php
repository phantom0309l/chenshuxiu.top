<?php
$_drugitem_t = $a->obj;

?>
<table class="table table-bordered">
    <thead>
        <tr>
            <th>类型</th>
            <th>行为日期</th>
            <th>药名</th>
            <th>剂量</th>
            <th>漏服天数</th>
            <th>备注</th>
        </tr>
    </thead>
    <tbody class="tc">
        <tr>
            <td><?= $_drugitem_t->getTypeDesc() ?></td>
            <td><?= $_drugitem_t->record_date ?></td>
            <td><?= $_drugitem_t->medicine->name ?></td>
            <td><?= $_drugitem_t->value ?></td>
            <td><?= $_drugitem_t->missdaycnt ?></td>
            <td><?= $_drugitem_t->content ?></td>
        </tr>
    </tbody>
</table>
<?php $_drugitem_t = null;?>