<div class="onesheet sheet-base" data-thisppid=<?= $thispp->id?> data-targetppid=<?= $targetppid?>
    data-picturedatasheettplid=<?= $picturedatasheettplid?> data-picturedatasheetid=<?= $picturedatasheetid?> >
    <p><?= $sheettitle ?></p>
    <?php foreach( $qas as $pair ){?>
        <div class="">
            <label for=""><?= $pair['q'] ?></label>
            <input type="text" class="pair form-control" style="width:80%" data-title="<?= $pair['q'] ?>" value="<?= $pair['a']?>" />
        </div>
    <?php }?>
    <div class="fr mt5">
        <div class="onesheetsave-btn btn btn-primary">Save</div>
        <div class="onesheetdelete-btn btn btn-danger">删除</div>
    </div>
    <div class="clearfix"></div>
</div>
