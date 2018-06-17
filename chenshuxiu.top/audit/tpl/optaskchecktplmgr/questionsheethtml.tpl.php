<div>
    <form action="" name="optaskCheckForm">
        <input type="hidden" name="optask_check_id" id="optask_check_id" value="<?= $optaskCheck->id?>">

        <?php foreach ($optaskCheckItems as $key=>$optaskCheckItem) {
            ?>

                <div class="<?= $key+1 == count($optaskCheckItems)? 'col-md-9 fl question-textarea-box':'col-md-6' ?> question-item">
                <div>
                    <?php echo $optaskCheckItem->getHtml()?>
                </div>
            </div>
        <?php } ?>
        <div>
            <button class="btn btn-minw btn-primary my-btn-submit" id="btn-submit">完成评价</button>
        </div>
    </form>
</div>