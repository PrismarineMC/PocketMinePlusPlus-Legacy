<?php

/*                                                                             __
 *                                                                           _|  |_
 *  ____            _        _   __  __ _                  __  __ ____      |_    _|
 * |  _ \ ___   ___| | _____| |_|  \/  (_)_ __   ___      |  \/  |  _ \    __ |__|  
 * | |_) / _ \ / __| |/ / _ \ __| |\/| | | '_ \ / _ \_____| |\/| | |_) | _|  |_  
 * |  __/ (_) | (__|   <  __/ |_| |  | | | | | |  __/_____| |  | |  __/ |_    _|
 * |_|   \___/ \___|_|\_\___|\__|_|  |_|_|_| |_|\___|     |_|  |_|_|      |__|   
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author PocketMine++ Team
 * @link http://pm-plus-plus.tk/
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\item\Tool;
use pocketmine\level;
use pocketmine\level\particle\NoteParticle;
use pocketmine\math\Vector3;
use pocketmine\network\protocol\LevelEventPacket;
use pocketmine\Player;

class NoteBlock extends Solid
{

    protected $id = self::NOTE_BLOCK;
    public $note = -1;

    public function __construct(){
    }

    public function canBeActivated(){
        return \true;
    }

    public function getHardness(){
        return 0.8;
    }

    public function getResistance(){
        return 8;
    }

    public function getToolType(){
        return Tool::TYPE_AXE;
    }

    public function isBreakable(Item $item){
        return \true;
    }

    /**
     * @param $data
     * @param $pl
     */
    public function PlaySound($data, $pl){
        $pk = new LevelEventPacket;
        $pk->evid = 1000;
        $pk->data = $data;
        $pk->x = $pl->x;
        $pk->y = $pl->y;
        $pk->z = $pl->z;
        //echo((string)$pl->getName());
        $this->getLevel()->addSound($pl->dataPacket($pk), $pl);
    }

    /**
     * @param Item $item
     * @param Player|null $player
     * @return bool
     */
    public function onActivate(Item $item, Player $player = \null){
        $this->note++;
        if ($this->note > 24) $this->note = 0;
        $particle = new NoteParticle (new Vector3 ($this->x + 0.5, $this->y + 1, $this->z + 0.5));
        $player->getLevel()->addParticle($particle);
        //$this->PlaySound(200 + ($this->note * 60), $player);
        return true;
    }

    public function getName(){
        return "Note Block";
    }

    public function getDrops(Item $item){
        return [
            [Item::NOTE_BLOCK, 0, 1],
        ];
    }

}
