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
use pocketmine\entity\Entity;
use pocketmine\Player;
use pocketmine\item\Tool;

class NetherPortal extends Flowable {

	protected $id = self::NETHER_PORTAL;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Nether Portal";
	}

	public function hasEntityCollision(){
		return \true;
	}

	public function isSolid(){
		return \false;
	}

	public function getHardness(){
		return 0;
	}

	public function getLightLevel(){
		return 15;
	}

	public function onEntityCollide(Entity $entity){
		if($entity instanceof Player){
			if(!isset($this->entities[$entity->getId()])) $this->entities[$entity->getId()] = 5;
			--$this->entities[$entity->getId()];
			if($this->entities[$entity->getId()] === 0) PortalUtils::teleportPlayer($entity);
		}
	}

	public function onUpdate($type){
		foreach($this->entities as $ent) if($ent->distance($this) >= 1) unset($this->entities[$ent->getId()]);

		return \false;
	}


	public function getDrops(Item $item){
		return [];
	}
}