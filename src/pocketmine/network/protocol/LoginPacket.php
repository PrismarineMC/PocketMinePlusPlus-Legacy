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

class LoginPacket extends DataPacket{
	const NETWORK_ID = Info::LOGIN_PACKET;

	public $username;
	public $protocol1;
	public $protocol2;
	public $clientId;

	public $clientUUID;
	public $serverAddress;
	public $clientSecret;

	public $slim = \false;
	public $transparent = \false;
	public $skinname = "";
	public $oldclient = \false;
	public $skin = \null;

	public function decode(){
		$this->username = $this->getString();
		$this->protocol1 = $this->getInt();
		$this->protocol2 = $this->getInt();
		if($this->protocol1 < Info::CURRENT_PROTOCOL){ //New fields!
			$this->setBuffer(\null, 0); //Skip batch packet handling
			return;
		}
		$this->clientId = $this->getLong();
		$this->clientUUID = $this->getUUID();
		$this->serverAddress = $this->getString();
		$this->clientSecret = $this->getString();

		$extrasize1 = strlen($this->buffer) - ($this->offset + 64 * 32 * 4 + 2);
    $extrasize2 = strlen($this->buffer) - ($this->offset + 64 * 64 * 4 + 2);

    if($extrasize1 > $extrasize2){
      $extrasize1 = $extrasize2;
    }

    if($extrasize1 === 2){
      $this->oldclient = \true;
      $this->slim = $this->getByte() > 0;
      $this->transparent = $this->getByte() > 0;
			if($this->slim){
				$this->skinname = "Standard_Alex";
			}else{
				$this->skinname = "Standard_Steve";
			}
			if($this->transparent){
				$this->skinname = "PvPWarriors_TundraStray";
			}
    }else{
      $this->skinname = $this->getString();
      if(strpos($this->skinname, "_Slim") !== \false or strpos($this->skinname, "_Alex") !== \false){
        $this->slim = \true;
      }
      if($this->skinname === "PvPWarriors_TundraStray"){//TODO: not check
        $this->transparent = \true;
      }
    }
    $this->skin = $this->getString();
	}

	public function encode(){

	}

}