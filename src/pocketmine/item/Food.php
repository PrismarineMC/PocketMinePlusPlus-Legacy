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

use pocketmine\Player;

abstract class Food extends Item
{
    public $saturation = 0;

    public function getSaturation()
    {
        return $this->saturation;
    }

    /**
     *
     * @param
     *            saturation (float) $float
     */
    public function setSaturation($float)
    {
        return $this->saturation = $float;
    }

    /**
     *
     * @param
     *            array([Effect, chance])
     */
    public function getEffects()
    {
        return [];
    }

    /**
     *
     * @param
     *            Effects (array) $effects
     */
    public function setEffects($effects)
    {
        return $this->effects = $effects;
    }

    /**
     *
     * @param Player $player
     */
    public function giveEffects(Player $player)
    {
        $effects = $this->getEffects();
        foreach ($effects as $effect) {
            $player->addEffect($effect);
        }
    }
}