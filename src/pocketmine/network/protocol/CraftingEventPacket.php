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

namespace pocketmine\network\protocol;

class CraftingEventPacket extends DataPacket
{
    const NETWORK_ID = Info::CRAFTING_EVENT_PACKET;

    public $windowId;
    public $type;
    public $id;
    public $input = [];
    public $output = [];

    public function clean()
    {
        $this->input = [];
        $this->output = [];
        return parent::clean();
    }

    public function decode()
    {
        $this->windowId = \ord($this->get(1));
        $this->type = (\PHP_INT_SIZE === 8 ? \unpack("N", $this->get(4))[1] << 32 >> 32 : \unpack("N", $this->get(4))[1]);
        $this->id = $this->getUUID();

        $size = (\PHP_INT_SIZE === 8 ? \unpack("N", $this->get(4))[1] << 32 >> 32 : \unpack("N", $this->get(4))[1]);
        for ($i = 0; $i < $size and $i < 128; ++$i) {
            $this->input[] = $this->getSlot();
        }

        $size = (\PHP_INT_SIZE === 8 ? \unpack("N", $this->get(4))[1] << 32 >> 32 : \unpack("N", $this->get(4))[1]);
        for ($i = 0; $i < $size and $i < 128; ++$i) {
            $this->output[] = $this->getSlot();
        }
    }

    public function encode()
    {

    }

}