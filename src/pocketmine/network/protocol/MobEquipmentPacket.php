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

use pocketmine\utils\Binary;

class MobEquipmentPacket extends DataPacket
{
    const NETWORK_ID = Info::MOB_EQUIPMENT_PACKET;

    public $eid;
    public $item;
    public $slot;
    public $selectedSlot;

    public function decode()
    {
        $this->eid = Binary::readLong($this->get(8));
        $this->item = $this->getSlot();
        $this->slot = \ord($this->get(1));
        $this->selectedSlot = \ord($this->get(1));
    }

    public function encode()
    {
        $this->buffer = \chr(self::NETWORK_ID);
        $this->offset = 0;;
        $this->buffer .= Binary::writeLong($this->eid);
        $this->putSlot($this->item);
        $this->buffer .= \chr($this->slot);
        $this->buffer .= \chr($this->selectedSlot);
    }

}