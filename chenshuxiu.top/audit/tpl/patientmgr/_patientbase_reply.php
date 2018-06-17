<!-- 运营回复 begin -->
<div class="patient_reply">
    <div>
        <div class="grayBgColorBox contentBoxTitle">快捷回复框</div>
        <label>回复给：</label>
        <select class="relation-group">
            <?php
            foreach ($patient->getUsers() as $user) {
                foreach ($user->getWxUsers() as $wxuser) {
                    ?>
                    <option value="<?= $wxuser->openid ?>">
                        <?= $user->shipstr ?> (<?= $wxuser->nickname ?>) of (<?= $wxuser->wxshop->name ?>)
                    </option>
                    <?php
                }
            }
            ?>
        </select>
    </div>
    <div class="ops-reply clearfix">
        <div class="col-md-10 ops-reply-l">
            <textarea name="reply-msg" class="reply-msg" style="width: 100%" rows="6"></textarea>
        </div>
        <div class="col-md-2 ops-reply-r">
            <a href="#" class="btn btn-default reply-topbtn">回复</a>
        </div>
    </div>
    <p class="red reply-notice"></p>
</div>
<!-- 运营回复 end -->
