
<?php foreach ($result as $a) { ?>
    <div class="flow-item" data-pipeid="<?= $a['pipeid']?>" >
        <h4 class="flow-item-title"><span class="flow-time"><?= $a['createtime'] ?></span>[<?= $a['objtype'] ?>]</h4>
        <p class="flow-item-content"><?= $a['content'] ?></p>
        <p><button class="btn btn-default thankQuick" data-toggle="modal" data-target="#thankBox">添加到感谢留言</button></p>
    </div>
<?php } ?>
