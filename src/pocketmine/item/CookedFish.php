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

class CookedFish extends Food
{
    const NORMAL = 0;
    const SALMON = 1;

    public function __construct($meta = 0, $count = 1)
    {
        parent::__construct(self::COOKED_FISH);
        $this->meta = $meta;
        $this->name = $this->getMetaName();
    }

    public function getMetaName()
    {
        static $names = [self::NORMAL => "Cooked Fish", self::SALMON => "Cooked Salmon", 2 => "Unknown Cooked Fish"];
        return $names[$this->meta & 0x02];
    }

    public function getSaturation()
    {
        return ($this->meta === self::NORMAL) ? 5 : (($this->meta === self::SALMON) ? 6 : 0);
    }
}