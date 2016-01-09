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
use pocketmine\level\Level;
use pocketmine\Player;

class DoublePlant extends Flowable{

	protected $id = self::DOUBLE_PLANT;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function canBeReplaced(){
		return \true;
	}

	public function getName(){
		static $names = [
			0 => "Sunflower",
			1 => "Lilac",
			2 => "Double Tallgrass",
			3 => "Large Fern",
			4 => "Rose Bush",
			5 => "Peony"
		];
		return $names[$this->meta & 0x07];
	}


	public function onUpdate($type){
		if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide(0)->isTransparent() === \true && !$this->getSide(0) instanceof DoublePlant){
				$this->getLevel()->setBlock($this, new Air(), \false, \false, \true);

				return Level::BLOCK_UPDATE_NORMAL;
			}
		}

		return \false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = \null){
		$down = $this->getSide(0);
		$up = $this->getSide(1);
		if($down->getId() === self::GRASS){
			$this->getLevel()->setBlock($block, $this, \true);
			$this->getLevel()->setBlock($up, Block::get($this->id, $this->meta ^ 0x08), \true);
			return \true;
		}
		return \false;
	}

	public function onBreak(Item $item){
		$up = $this->getSide(1);
		$down = $this->getSide(0);
		if(($this->meta & 0x08) === 0x08){
			if($up->getId() === $this->id and $up->meta !== 0x08){
				$this->getLevel()->setBlock($up, new Air(), \true, \true);
			}
			elseif($down->getId() === $this->id and $down->meta !== 0x08){
				$this->getLevel()->setBlock($down, new Air(), \true, \true);
			}
		}
		else{
			if($up->getId() === $this->id and ($up->meta & 0x08) === 0x08){
				$this->getLevel()->setBlock($up, new Air(), \true, \true);
			}
			elseif($down->getId() === $this->id and ($down->meta & 0x08) === 0x08){
				$this->getLevel()->setBlock($down, new Air(), \true, \true);
			}
		}
	}

	public function getDrops(Item $item){
		if(($this->meta & 0x08) !== 0x08){
			return [[Item::DOUBLE_PLANT,$this->meta,1]];
		}
		else
			return [];
	}

}