<?php
    $offsetpipetime_str = strtotime($offsetpipetime);
?>
<input type="hidden" id="pipe_cnt_<?=$offsetpipetime_str?>" value="<?=count($dwx_pipes)?>">

<?php
    foreach ($dwx_pipes as $a) {
        if ($a->objtype == 'Dwx_txtmsg') {
            ?>
                <li>
                    <div class="list-timeline-time"><?=$a->createtime?></div>
                    <i class="fa fa-comments-o list-timeline-icon bg-warning"></i>
                    <div class="list-timeline-content">
                        <p class="font-w600"><?=$a->doctor->name?>医生:</p>
                        <p class="font-s13 bg-warning-light" style="border-radius:4px;padding:10px;line-height:1.8"><?=$a->obj->content?></p>
                    </div>
                </li>
            <?php
        } elseif ($a->objtype == 'Dwx_picmsg') {
            ?>
                <li>
                    <div class="list-timeline-time"><?=$a->createtime?></div>
                    <i class="fa fa-image list-timeline-icon bg-warning"></i>
                    <div class="list-timeline-content">
                        <p class="font-w600"><?=$a->doctor->name?>医生:</p>
                        <p class="font-s13"></p>
                        <!-- Gallery (.js-gallery class is initialized in App() -> uiHelperMagnific()) -->
                        <!-- For more info and examples you can check out http://dimsemenov.com/plugins/magnific-popup/ -->
                        <div class="row items-push js-gallery">
                            <div class="col-sm-6 col-lg-4">
                                <a class="img-link" target="_blank" href="<?= $a->obj->picture->getSrc(1000, 1000) ?>">
                                    <img class="img-responsive" src="<?= $a->obj->picture->getSrc(80, 80) ?>" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </li>
            <?php
        } elseif ($a->objtype == 'Dwx_voicemsg') { // si-volume-2
            ?>
                <li>
                    <div class="list-timeline-time"><?=$a->createtime?></div>
                    <i class="fa fa-microphone list-timeline-icon bg-warning"></i>
                    <div class="list-timeline-content">
                        <p class="font-w600"><?=$a->doctor->name?>医生:</p>
                        <div class="btn-group">
                            <button data-url="<?=$a->obj->voice->getUrl()?>" class="btn btn-default amrbtn"><i class="fa fa-play"></i></button>
                        </div>
                    </div>
                </li>
            <?php
        } elseif ($a->objtype == 'Dwx_kefumsg') {
            ?>
                <li>
                    <div class="list-timeline-time"><?=$a->createtime?></div>
                    <i class="fa fa-comments-o list-timeline-icon bg-info"></i>
                    <div class="list-timeline-content">
                        <p class="font-w600"><?=$a->obj->auditor->name?>医助:</p>
                        <p class="font-s13 bg-info-light" style="border-radius:4px;padding:10px;line-height:1.8"><?=$a->obj->content?></p>
                    </div>
                </li>
            <?php
        }
    }
?>
