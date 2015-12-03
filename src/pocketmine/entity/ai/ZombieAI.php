<?php

namespace pocketmine\entity\ai;

use pocketmine\Player;
use pocketmine\math\Vector3;
use pocketmine\math\Vector2;
use pocketmine\entity\Entity;
use pocketmine\entity\Zombie;
use pocketmine\scheduler\CallbackTask;
use pocketmine\network\protocol\SetEntityMotionPacket;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\Server;
use pocketmine\item\Item;

class ZombieAI extends EntityAI{
    
    public $width = 0.4;
    protected $dif = 0;

    public $hatred_r = 16;  //Hate radius
    public $zo_hate_v = 1.4; //Zombies hate walking speed mode

		public function onUpdate($currentTick){
		    $config = Server::getInstance()->getMobConfig();
			if($currentTick % 10 == 0) {
				$this->ZombieRandomWalkCalc();
				$this->ZombieHateWalk();
				$this->ZombieHateFinder();
			}
			if($currentTick % 40 == 0) $this->ZombieFire();
			$this->ZombieRandomWalk();
			
		}

    /**
     * Zombie initialization routine and freely walking mode cycle timer
     * Timer：20 ticks
     */
    public function ZombieRandomWalkCalc() {
        $this->dif = Server::getInstance()->getDifficulty();
        //$this->getLogger()->info(count($this->plugin->zombie));
                $zo = $this->entity;
                    if ($this->willMove()) {
                        if (!isset($this->data)){
                            $this->data = array(
                                'ID' => $zo->getId(),
                                'IsChasing' => false,
                                'motionx' => 0,
                                'motiony' => 0,
                                'motionz' => 0,
                                'hurt' => 10,
                                'time'=>10,
                                'x' => 0,
                                'y' => 0,
                                'z' => 0,
                                'oldv3' => $zo->getLocation(),
                                'yup' => 20,
                                'up' => 0,
                                'yaw' => $zo->yaw,
                                'pitch' => 0,
                                'level' => $zo->getLevel()->getName(),
                                'xxx' => 0,
                                'zzz' => 0,
                                'gotimer' => 10,
                                'swim' => 0,
                                'jump' => 0.01,
                                'canjump' => true,
                                'drop' => false,
                                'canAttack' => 0,
                                'knockBack' => false,
                            );
                            $zom = &$this->data;
                            $zom['x'] = $zo->getX();
                            $zom['y'] = $zo->getY();
                            $zom['z'] = $zo->getZ();
                        }
                        $zom = &$this->data;

                        if ($zom['IsChasing'] === false) {  //Walk mode
                            if ($zom['gotimer'] == 0 or $zom['gotimer'] == 10) {
                                //Limit rotation rate
                                $newmx = mt_rand(-5,5)/10;
                                while (abs($newmx - $zom['motionx']) >= 0.7) {
                                    $newmx = mt_rand(-5,5)/10;
                                }
                                $zom['motionx'] = $newmx;

                                $newmz = mt_rand(-5,5)/10;
                                while (abs($newmz - $zom['motionz']) >= 0.7) {
                                    $newmz = mt_rand(-5,5)/10;
                                }
                                $zom['motionz'] = $newmz;
                            }
                            elseif ($zom['gotimer'] >= 20 and $zom['gotimer'] <= 24) {
                                $zom['motionx'] = 0;
                                $zom['motionz'] = 0;
                                //Zombie stop
                            }

                            $zom['gotimer'] += 0.5;
                            if ($zom['gotimer'] >= 22) $zom['gotimer'] = 0;  //Reset timer walk

                            //$zom['motionx'] = mt_rand(-10,10)/10;
                            //$zom['motionz'] = mt_rand(-10,10)/10;
                            $zom['yup'] = 0;
                            $zom['up'] = 0;

                            //$width = $this->width;
                            $pos = new Vector3 ($zom['x'] + $zom['motionx'], floor($zo->getY()) + 1,$zom['z'] + $zom['motionz']);  //目标坐标
                            $zy = $this->ifjump($zo->getLevel(),$pos);
                            if ($zy === false) {  //if can not move forward
                                $pos2 = new Vector3 ($zom['x'], $zom['y'] ,$zom['z']);  //Target coordinates
                                if ($this->ifjump($zo->getLevel(),$pos2) === false) { //Original coordinate
                                    $pos2 = new Vector3 ($zom['x'], $zom['y']-1,$zom['z']);  //decline
                                    $zom['up'] = 1;
                                    $zom['yup'] = 0;
                                }
                                else {
                                    $zom['motionx'] = - $zom['motionx'];
                                    $zom['motionz'] = - $zom['motionz'];
                                    //He turned 180 degrees
                                    $zom['up'] = 0;
                                }
                            }
                            else {
                                $pos2 = new Vector3 ($zom['x'] + $zom['motionx'], $zy - 1 ,$zom['z'] + $zom['motionz']);  //Target coordinates
                                if ($pos2->y - $zom['y'] < 0) {
                                    $zom['up'] = 1;
                                }
                                else {
                                    $zom['up'] = 0;
                                }
                            }

                            if ($zom['motionx'] == 0 and $zom['motionz'] == 0) {  //Zombie stop
                            }
                            else {
                                //Steering computing
                                $yaw = $this->getyaw($zom['motionx'], $zom['motionz']);
                                //$zo->setRotation($yaw,0);
                                $zom['yaw'] = $yaw;
                                $zom['pitch'] = 0;
                            }

                            //Update zombie coordinates
                            if (!$zom['knockBack']) {
                                $zom['x'] = $pos2->getX();
                                $zom['z'] = $pos2->getZ();
                                $zom['y'] = $pos2->getY();
                            }
                            $zom['motiony'] = $pos2->getY() - $zo->getY();
                            //echo($zo->getY()."\n");
                            //var_dump($pos2);
                            //var_dump($zom['motiony']);
                            $zo->setPosition($pos2);
                            //echo "SetPosition \n";
                        }
                    }
                
            
        
    }

    /**
     * Zombies hate refresh timer
     * Update timer：10 ticks
     */
    public function ZombieHateFinder() {
                $zo = $this->entity;
                    if (isset($this->data)) {
                        $zom = &$this->data;
                        $h_r = $this->hatred_r;  //Hate radius
                        $pos = new Vector3($zo->getX(), $zo->getY(), $zo->getZ());
                        $hatred = false;
                        foreach ($zo->getViewers() as $p) {  //Being close to the players
                            if ($p->distance($pos) <= $h_r) {  //Players within a radius of hatred
                                if ($hatred === false) {
                                    if ($p instanceof Player)
                                     {
                                      if (($p -> getGamemode () == 0) || ($p -> getGamemode () == 2)) $hatred = $p;
                                     }
                                } elseif ($hatred instanceof Player) {
                                    if (($p->distance($pos) <= $hatred->distance($pos)) && (($p -> getGamemode () == 0) || ($p -> getGamemode () == 2))) {  //A more recent than
                                        $hatred = $p;
                                    }
                                }
                            }
                        }
                        //echo ($zom['IsChasing']."\n");
                        if ($hatred == false or $this->dif == 0) {
                            $zom['IsChasing'] = false;
                        } else {
                            $zom['IsChasing'] = $hatred->getName();
                        }
                    }
                
            
        
    }

    /**
     * Hate Zombie coordinates update timer
     * Timer：10 ticks
     */
    public function ZombieHateWalk() {
                $zo = $this->entity;
$level = $zo->getLevel();
                    if (isset($this->data)) {
                        $zom = &$this->data;
                        //$zom['yup'] = $zom['yup'] - 1;
                        if (!$zom['knockBack']) {
                            $zom['oldv3'] = $zo->getLocation();
                            $zom['canjump'] = true;

                            //Zombie collision detection
                            foreach ($level->getEntities() as $zo0) {
                                if ($zo0 instanceof Zombie and !($zo0 == $zo)) {
                                    if ($zo->distance($zo0) <= $this->width * 2) {
                                        $dx = $zo->x - $zo0->x;
                                        $dz = $zo->z - $zo0->z;
                                        if ($dx == 0) {
                                            $dx = mt_rand(-5,5) / 5;
                                            if ($dx == 0) $dx = 1;
                                        }
                                        if ($dz == 0) {
                                            $dz = mt_rand(-5,5) / 5;
                                            if ($dz == 0) $dz = 1;
                                        }
                                        $zo->knockBack($zo0,0,$dx/5,$dz/5,0);
                                        $newpos = new Vector3($zo->x + $dx * 5, $zo->y, $zo->z + $dz * 5);
                                        $zom['x'] = $newpos->x;
                                        $zom['y'] = $newpos->y;
                                        $zom['z'] = $newpos->z;
                                        Server::getInstance()->getScheduler()->scheduleDelayedTask(new CallbackTask([$this,"knockBackover"],[$newpos]),5);
                                    }
                                }

                            }

                            if ($zom['IsChasing'] !== false) {
                                $p = Server::getInstance()->getPlayer($zom['IsChasing']);
                                if (($p instanceof Player) === false) {
                                    $zom['IsChasing'] = false;  //Cancel hate mode
                                } else {
                                    //The real traveling direction calculation
                                    $xx = $p->getX() - $zo->getX();
                                    $zz = $p->getZ() - $zo->getZ();
                                    $yaw = $this->getyaw($xx,$zz);
                                    /*
                                     * x = $xxx, z = $zzz
                                     * x0 = $xx, z0 = $zz
                                     * x^2 + z^2 = 0.7
                                     * x/z = x0/z0 => x = bi * z
                                     * =>
                                     * bi^2 * z^2 + z^2 = 0.7
                                     * z^2 * (bi^2 + 1) = 0.7
                                     * */
                                    if ($zz != 0) {
                                        $bi = $xx/$zz;
                                    }else{
                                        $bi = 0;
                                    }

                                    //zombie walk faster
                                    if ($zo->getHealth() == $zo->getMaxHealth()) {
                                        $zzz = sqrt(($this->zo_hate_v / 2.5) / ($bi * $bi + 1));
                                    }else{
                                        $zzz = sqrt(($this->zo_hate_v / 2) / ($bi * $bi + 1));
                                    }

                                    if ($zz < 0) $zzz = -$zzz;
                                    $xxx = $zzz * $bi;

                                    $zo_v2 = new Vector2($zo->getX(),$zo->getZ());
                                    $p_v2 = new Vector2($p->getX(),$p->getZ());
                                    if ($zo_v2->distance($p_v2) <= $this->zo_hate_v/2) {
                                        $xxx = $xx;
                                        $zzz = $zz;
                                    }

                                    $zom['xxx'] = $xxx;
                                    $zom['zzz'] = $zzz;

                                    //Calculating the y axis
                                    //$width = $this->width;
                                    $pos0 = new Vector3 ($zo->getX(), $zo->getY() + 1, $zo->getZ());  //Original coordinate
                                    $pos = new Vector3 ($zo->getX() + $xxx, $zo->getY() + 1, $zo->getZ() + $zzz);  //Target coordinates
                                    //I used to write Zombie width
                                    //$v = $this->zo_hate_v/2;
                                    //$pos_front = new Vector3 ($zo->getX() + ($xxx/$v*($v+$this->width)), $zo->getY() + 1, $zo->getZ() + ($zzz/$v*($v+$this->width)));  //Front coordinates
                                    //$pos_back = new Vector3 ($zo->getX() + (-$xxx/$v*(-$v-$this->width)), $zo->getY() + 1, $zo->getZ() + (-$zzz/$v*(-$v-$this->width)));  //Rear coordinates
                                    $zy = $this->ifjump($zo->getLevel(), $pos, true);

                                    if ($zy === false or ($zy !== false and $this->ifjump($zo->getLevel(), $pos0, true, true) == 'fall')) {  //Front can not move forwar
                                        if ($this->ifjump($zo->getLevel(), $pos0, false) === false) { //Original coordinate is still vacant
                                            if ($zom['drop'] === false) {
                                                $zom['drop'] = 0;  //Zombie falling speed
                                            }
                                            $pos2 = new Vector3 ($zo->getX(), $zo->getY() - ($zom['drop'] / 2 + 1.25), $zo->getZ());  //decline
                                        } else {
                                            $zom['drop'] = false;
                                            if ($this->whatBlock($level, $pos0) == "climb") {  //ladder
                                                $zy = $pos0->y + 0.7;
                                                $pos2 = new Vector3 ($zo->getX(), $zy - 1, $zo->getZ());  //Target coordinates
                                            }
                                            elseif ($xxx != 0 and $zzz != 0) {  //To the closest distance
                                                if ($this->ifjump($zo->getLevel(), new Vector3($zo->getX() + $xxx, $zo->getY() + 1, $zo->getZ()), true) !== false) {
                                                    $pos2 = new Vector3($zo->getX() + $xxx, floor($zo->getY()), $zo->getZ());  //Target coordinates
                                                } elseif ($this->ifjump($zo->getLevel(), new Vector3($zo->getX(), $zo->getY() + 1, $zo->getZ() + $zzz), true) !== false) {
                                                    $pos2 = new Vector3($zo->getX(), floor($zo->getY()), $zo->getZ() + $zzz);  //Target coordinates
                                                } else {
                                                    $pos2 = new Vector3 ($zo->getX() - $xxx / 5, floor($zo->getY()), $zo->getZ() - $zzz / 5);  //Target coordinates
                                                    //He turned 180 degrees, to go
                                                }
                                            } else {
                                                $pos2 = new Vector3 ($zo->getX() - $xxx / 5, floor($zo->getY()), $zo->getZ() - $zzz / 5);  //Target coordinates
                                            }
                                        }
                                    } else {
                                        $pos2 = new Vector3 ($zo->getX() + $xxx, $zy - 1, $zo->getZ() + $zzz);  //Target coordinates
                                    }
                                    $zo->setPosition($pos2);

                                    $pos3 = $pos2;
                                    $pos3->y = $pos3->y + 2.62;
                                    $ppos = $p->getLocation();
                                    $ppos->y = $ppos->y + $p->getEyeHeight();
                                    $pitch = $this->getpitch($pos3,$ppos);

                                    $zom['yaw'] = $yaw;
                                    $zom['pitch'] = $pitch;
                                    if (!$zom['knockBack']) {
                                        $zom['x'] = $zo->getX();
                                        $zom['y'] = $zo->getY();
                                        $zom['z'] = $zo->getZ();
                                    }
                                }
                            }
                        }
                    }
                
            
        
    }

    /**
     * High-intensive contracting timer
     * - Sending packets
     * - Calculation Freefall related data
     * Cycle interval: 1 tick
     */
    public function ZombieRandomWalk() {
      						$zo = $this->entity;
$level = $zo->getLevel();
                    if (isset($this->data)) {
                        $zom = &$this->data;
                        if ($zom['canAttack'] != 0) {
                            $zom['canAttack'] -= 1;
                        }
                        $pos = $zo->getLocation();
                        //echo ($zom['IsChasing']."\n");

                        //The real free-fall
                        if ($zom['drop'] !== false) {
                            $olddrop = $zom['drop'];
                            $zom['drop'] += 0.5;
                            $drop = $zom['drop'];
                            //echo $drop."\n";
                            $dropy = $zo->getY() - ($olddrop * 0.05 + 0.0125);
                            $posd0 = new Vector3 (floor($zo->getX()), floor($dropy), floor($zo->getZ()));
                            $posd = new Vector3 ($zo->getX(), $dropy, $zo->getZ());
                            if ($this->whatBlock($zo->getLevel(), $posd0) == "air") {
                                $zo->setPosition($posd);  //Decline
                            } else {
                                for ($i = 1; $i <= $drop; $i++) {
                                    $posd0->y++;
                                    if ($this->whatBlock($zo->getLevel(), $posd0) != "block") {
                                        $posd->y = $posd0->y;
                                        $zo->setPosition($posd);  //Lowering completion
                                        $h = $zom['drop'] * $zom['drop'] / 20;
                                        $damage = $h - 3;
                                        //echo($h . ": " . $damage . "\n");
                                        if ($damage > 0) {
                                            //$zo->attack($damage, EntityDamageEvent::CAUSE_FALL);
											$zo->setHealth($zo->getHealth() - $damage);
                                        }
                                        $zom['drop'] = false;
                                        break;
                                    }
                                }
                            }
                        } else {
                            $drop = 0;
                        }

                        if ($zom['IsChasing'] !== false) {
                            if (!$zom['knockBack']) {
                                //echo $zy;
                                $zom['up'] = 0;
                                if ($this->whatBlock($level, $pos) == "water") {
                                    $zom['swim'] += 1;
                                    if ($zom['swim'] >= 20) $zom['swim'] = 0;
                                } else {
                                    $zom['swim'] = 0;
                                }

                                if(abs($zo->getY() - $zom['oldv3']->y) == 1 and $zom['canjump'] === true){
                                    $zom['canjump'] = false;
                                    $zom['jump'] = 0.5;
                                }
                                else {
                                    if ($zom['jump'] > 0.01) {
                                        $zom['jump'] -= 0.1;
                                    }
                                    else {
                                        $zom['jump'] = 0.01;
                                    }
                                }



                                $pk3 = new SetEntityMotionPacket;
                                $pk3->entities = [
                                    [$zo->getID(), $zom['xxx'] / 10, -$zom['swim'] / 100 + $zom['jump'] - $drop, $zom['zzz'] / 10]
                                ];
                                foreach ($zo->getViewers() as $pl) {
                                    $pl->dataPacket($pk3);
                                }

                                $p = Server::getInstance()->getPlayer($zom['IsChasing']);
                                if ($p instanceof Player) {
                                    if ($p->distance($pos) <= 1.3) {
                                        //Fire from the zombies ignites human
                                        if ($zo->fireTicks > 0) {
                                            $p->setOnFire(5);
                                        }
                                    }
                                    if ($p->distance($pos) <= 1.5) {

                                        if ($zom['canAttack'] == 0) {
                                            $zom['canAttack'] = 20;
                                            @$p->knockBack($zo, 0, $zom['xxx'] / 10, $zom['zzz'] / 10);
                                            if ($p->isSurvival()) {
                                                $zoDamage = $this->getZombieDamage($zo->getHealth());
                                                $damage = $this->getPlayerDamage($p, $zoDamage);
                                                //echo $zoDamage."-".$damage."\n";
												$p->setHealth($p->getHealth() - $damage);
                                                $p->attack($damage, new EntityDamageByEntityEvent($zo, $p, EntityDamageEvent::CAUSE_ENTITY_ATTACK, 0));
                                            }
                                        }
                                    }
                                }
                            }

                        } else {
                            if ($zom['up'] == 1) {
                                if ($zom['yup'] <= 10) {
                                    $pk3 = new SetEntityMotionPacket;
                                    $pk3->entities = [
                                        [$zo->getID(), $zom['motionx'] / 10, $zom['motiony'] / 10, $zom['motionz'] / 10]
                                    ];
                                    foreach ($zo->getViewers() as $pl) {
                                        $pl->dataPacket($pk3);
                                    }
                                } else {
                                    $pk3 = new SetEntityMotionPacket;
                                    $pk3->entities = [
                                        [$zo->getID(), $zom['motionx'] / 10 - $zom['motiony'] / 10, $zom['motionz'] / 10]
                                    ];
                                    foreach ($zo->getViewers() as $pl) {
                                        $pl->dataPacket($pk3);
                                    }
                                }
                            } else {

                                $pk3 = new SetEntityMotionPacket;
                                $pk3->entities = [
                                    [$zo->getID(), $zom['motionx'] / 10, -$zom['motiony'] / 10, $zom['motionz'] / 10]
                                ];
                                foreach ($zo->getViewers() as $pl) {
                                    $pl->dataPacket($pk3);

                                }
                            }
                        }
$zo->yaw = $zom['yaw'];
$zo->pitch = $zom['pitch'];
                    }
                
            
        
    }

    /**
     * Zombie fire timer
     */
    public function ZombieFire() {
                $zo = $this->entity;
$level = $zo->getLevel();
                    //var_dump($p->getLevel()->getTime());
                    if(0 < $level->getTime() and $level->getTime() < 13500){
                        $v3 = new Vector3($zo->getX(), $zo->getY(), $zo->getZ());
                        $ok = true;
                        for ($y0 = $zo->getY() + 2; $y0 <= $zo->getY()+10; $y0++) {
                            $v3->y = $y0;
                            if ($level->getBlock($v3)->getID() != 0) {
                                $ok = false;
                                break;
                            }
                        }
                        if ($this->whatBlock($level,new Vector3($zo->getX(), floor($zo->getY() - 1), $zo->getZ())) == "water") $ok = false;
                        if ($ok) $zo->setOnFire(2);
                    }
                
            
        
    }

}
