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

namespace pocketmine\item;

use pocketmine\entity\Effect;

class Fish extends Food{
	const NORMAL = 0;
	const SALMON = 1;
	const CLOWNFISH = 2;
	const PUFFERFISH = 3;


	public function __construct($meta = 0, $count = 1){
		parent::__construct(self::RAW_FISH, $meta, $count, $this->getNameByMeta($meta));
	}

	public function getNameByMeta($meta){
		static $names = [self::NORMAL => "Raw Fish",self::SALMON => "Raw Salmon",self::CLOWNFISH => "Clownfish",self::PUFFERFISH => "Pufferfish",4 => "Unknown Fish"];
		return $names[$meta & 0x04];
	}

	public function getEffects(){
		return $this->meta === self::PUFFERFISH?[[Effect::getEffect(Effect::NAUSEA)->setDuration(15 * 20)->setAmplifier(1), 1],[Effect::getEffect(Effect::HUNGER)->setDuration(15 * 20)->setAmplifier(2), 1],[Effect::getEffect(Effect::POISON)->setDuration(60 * 20)->setAmplifier(3), 1]]:[];
	}

	public function getSaturation(){
		return ($this->meta === self::NORMAL || $this->meta === self::SALMON)?2:(($this->meta === self::CLOWNFISH || $this->meta === self::PUFFERFISH)?1:0);
	}
}