<?php

class ChipPushTask implements ITask
{

    private $clanid = 0;

    private $chipid = 0;

    public function __construct ($clanid, $chipid) {
        $this->clanid = $clanid;
        $this->chipid = $chipid;
    }

    public function dowork () {
        $clan = Clan::getById($this->clanid);

        if (false == $clan instanceof Clan) {
            echo " clan {$this->clanid} is null \n";
            return;
        }

        $chip = Chip::getById($this->chipid, $clan->randno);
        if (false == $chip instanceof Chip) {

            echo "chip {$this->chipid} is null \n";
            return;
        }

        echo " jpushBegin [";
        echo $clan->jpushChip($chip);
        echo "] jpushEnd ";
    }
}
