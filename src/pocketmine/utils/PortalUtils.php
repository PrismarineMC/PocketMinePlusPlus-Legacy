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
 *
 */

namespace pocketmine\utils;

use pocketmine\block\Block;
use pocketmine\level\Level;
use pocketmine\math\Vector3;

abstract class PortalUtils{

	/*
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @param Level $level
	 *
	 * @return string|bool
	 */

	public static function checkPortal($x,$y,$z,$level) {
		if($level->getBlockIdAt($x,$y,$z)==49&&$level->getBlockIdAt($x-1,$y+1,$z)==49&&$level->getBlockIdAt($x-1,$y+2,$z)==49&&$level->getBlockIdAt($x-1,$y+3,$z)==49&&
			$level->getBlockIdAt($x,$y+4,$z)==49&&$level->getBlockIdAt($x+1,$y+4,$z)==49&&$level->getBlockIdAt($x+2,$y+3,$z)==49&&$level->getBlockIdAt($x+2,$y+2,$z)==49&&$level->getBlockIdAt($x+2,$y+1,$z)==49&&
			$level->getBlockIdAt($x+1,$y,$z)==49) return "x-";
		if($level->getBlockIdAt($x,$y,$z)==49&&$level->getBlockIdAt($x+1,$y+1,$z)==49&&$level->getBlockIdAt($x+1,$y+2,$z)==49&&$level->getBlockIdAt($x+1,$y+3,$z)==49&&
			$level->getBlockIdAt($x,$y+4,$z)==49&&$level->getBlockIdAt($x-1,$y+4,$z)==49&&$level->getBlockIdAt($x-2,$y+3,$z)==49&&$level->getBlockIdAt($x-2,$y+2,$z)==49&&$level->getBlockIdAt($x-2,$y+1,$z)==49&&
			$level->getBlockIdAt($x-1,$y,$z)==49) return "x+";
		if($level->getBlockIdAt($x,$y,$z)==49&&$level->getBlockIdAt($x,$y+1,$z-1)==49&&$level->getBlockIdAt($x,$y+2,$z-1)==49&&$level->getBlockIdAt($x,$y+3,$z-1)==49&&
			$level->getBlockIdAt($x,$y+4,$z)==49&&$level->getBlockIdAt($x,$y+4,$z+1)==49&&$level->getBlockIdAt($x,$y+3,$z+2)==49&&$level->getBlockIdAt($x,$y+2,$z+2)==49&&$level->getBlockIdAt($x,$y+1,$z+2)==49&&
			$level->getBlockIdAt($x,$y,$z+1)==49) return "z-";
		if($level->getBlockIdAt($x,$y,$z)==49&&$level->getBlockIdAt($x,$y+1,$z+1)==49&&$level->getBlockIdAt($x,$y+2,$z+1)==49&&$level->getBlockIdAt($x,$y+3,$z+1)==49&&
			$level->getBlockIdAt($x,$y+4,$z)==49&&$level->getBlockIdAt($x,$y+4,$z-1)==49&&$level->getBlockIdAt($x,$y+3,$z-2)==49&&$level->getBlockIdAt($x,$y+2,$z-2)==49&&$level->getBlockIdAt($x,$y+1,$z-2)==49&&
			$level->getBlockIdAt($x,$y,$z-1)==49) return "z+";
		return \false;
	}

	/*
	 * @param float $x
	 * @param float $y
	 * @param float $z
	 * @param Level $level
	 *
	 * @return bool
	 */

	public static function buildPortal($x,$y,$z,$level){
		$p = Block::get(90);
		$cp = self::checkPortal($x,$y,$z,$level);
		if($cp=="x+") {
			$level->setBlock(new Vector3($x,$y+1,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+2,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+3,$z), clone $p);
			$level->setBlock(new Vector3($x-1,$y+1,$z), clone $p);
			$level->setBlock(new Vector3($x-1,$y+2,$z), clone $p);
			$level->setBlock(new Vector3($x-1,$y+3,$z), clone $p);
		}elseif($cp=="x-") {
			$level->setBlock(new Vector3($x,$y+1,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+2,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+3,$z), clone $p);
			$level->setBlock(new Vector3($x+1,$y+1,$z), clone $p);
			$level->setBlock(new Vector3($x+1,$y+2,$z), clone $p);
			$level->setBlock(new Vector3($x+1,$y+3,$z), clone $p);
		}elseif($cp=="z+") {
			$level->setBlock(new Vector3($x,$y+1,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+2,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+3,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+1,$z-1), clone $p);
			$level->setBlock(new Vector3($x,$y+2,$z-1), clone $p);
			$level->setBlock(new Vector3($x,$y+3,$z-1), clone $p);
		}elseif($cp=="z-") {
			$level->setBlock(new Vector3($x,$y+1,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+2,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+3,$z), clone $p);
			$level->setBlock(new Vector3($x,$y+1,$z+1), clone $p);
			$level->setBlock(new Vector3($x,$y+2,$z+1), clone $p);
			$level->setBlock(new Vector3($x,$y+3,$z+1), clone $p);
		}elseif(!$cp){
			return \false;
		}
		return \true;
	}

	/*
	 * @param Player $player
	 *
	 * @return void
	 */

	public static function teleportPlayer(Player $player){
		$level = $player->getServer()->getLevelByName($player->getServer()->getDefaultLevel()->getFolderName() . "_nether");
		$player->teleport(Position::fromObject($level->getSpawnLocation(), $level));
	}
}