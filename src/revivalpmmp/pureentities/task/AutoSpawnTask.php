<?php

/*  PureEntitiesX: Mob AI Plugin for PMMP
    Copyright (C) 2017 RevivalPMMP

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>. */


namespace revivalpmmp\pureentities\task;

use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\scheduler\PluginTask;
use revivalpmmp\pureentities\data\Data;
use revivalpmmp\pureentities\PureEntities;
use revivalpmmp\pureentities\PluginConfiguration;
use revivalpmmp\pureentities\utils\PeTimings;


class AutoSpawnTask extends PluginTask{

	private $plugin;
	private $spawnerWorlds = [];

	// Friendly Mobs only generate every 400 ticks
	private $lastFriendlyTick;
	private $spawnFriendlyMobsAllowed;
	private $hostileMobs = 0;
	private $passiveDryMobs = 0;
	private $passiveWetMobs = 0;
	private $mobCap = 255;

	public function __construct(PureEntities $plugin){
		parent::__construct($plugin);
		$this->plugin = $plugin;
		$this->spawnerWorlds = PluginConfiguration::getInstance()->getEnabledWorlds();
	}

	public function onRun(int $currentTick){
		PureEntities::logOutput("AutoSpawnTask: onRun ($currentTick)", PureEntities::DEBUG);
		PeTimings::startTiming("AutoSpawnTask");

		foreach($this->plugin->getServer()->getLevels() as $level){
			if(count($this->spawnerWorlds) > 0 and !in_array($level->getName(), $this->spawnerWorlds)){
				continue;
			}
			$this->hostileMobs = 0;
			$this->passiveDryMobs = 0;
			$this->passiveWetMobs = 0;


			foreach($level->getEntities() as $entity) {
				if(in_array(array_search($entity::NETWORK_ID, Data::NETWORK_IDS), Data::HOSTILE_MOBS)){
					$this->hostileMobs++;
				} elseif(in_array(array_search($entity::NETWORK_ID, Data::NETWORK_IDS), Data::PASSIVE_DRY_MOBS)) {
					$this->passiveDryMobs++;
				} elseif(in_array(array_search($entity::NETWORK_ID, Data::NETWORK_IDS), Data::PASSIVE_WET_MOBS)){
					$this->passiveWetMobs++;
				}
			}
			PureEntities::logOutput("AutoSpawnTask: Hostiles = $this->hostileMobs");
			PureEntities::logOutput("AutoSpawnTask: Passives(Dry) = $this->passiveDryMobs");
			PureEntities::logOutput("AutoSpawnTask: Passives(Wet) = $this->passiveWetMobs");
			$total = ($this->hostileMobs + $this->passiveWetMobs + $this->passiveDryMobs);

			if($total >= $this->mobCap) {
				PureEntities::logOutput("AutoSpawnTask: Stopping AutoSpawn due to MobCap");
				PureEntities::logOutput("AutoSpawnTask: Mob Total = $total");

				PeTimings::stopTiming("AutoSpawnTask");
				return;
			}

			$playerLocations = [];


			if(count($level->getPlayers()) > 0){
				foreach($level->getPlayers() as $player){
					if ($player->spawned){

						/* Intentionally not converting directly to chunks here so
						 * spawn locations can be compared to player locations to meet
						 * distance requirements.
						 */
						array_push($playerLocations, $player->asVector3());
					}
				}

				$this->spawnFriendlyMobsAllowed = false;

				// Check if this pass needs to spawn passive mobs.
				// Passive mobs only attempt to spawn every 20 seconds (400 ticks).
				if($currentTick - $this->lastFriendlyTick >= 400) {
					$this->spawnFriendlyMobsAllowed = true;
					$this->lastFriendlyTick = $currentTick;
				}

				// List of chunks eligible to spawn new mobs.
				$spawnMap = $this->generateSpawnMap($playerLocations);


				foreach($spawnMap as $chunk){
					$center = $this->getRandomLocationInChunk(new Vector2($chunk[0], $chunk[1]));
					$mob = null;
					$type = null;
					if($this->spawnFriendlyMobsAllowed and mt_rand(0,1) === 1){
						// TODO: Spawn water creatures.
						$mob = Data::PASSIVE_DRY_MOBS[array_rand(Data::PASSIVE_DRY_MOBS)];

						$type = "passive";
					} else {
						$mob = Data::HOSTILE_MOBS[array_rand(Data::HOSTILE_MOBS)];
						$type = "hostile";
					}

					$mobId = Data::NETWORK_IDS[$mob];

					if($this->isValidPackCenter($center, $level)){
						$this->spawnPackToLevel($center, $mobId, $level, $type);
					}
				}

			}
		}
		PeTimings::stopTiming("AutoSpawnTask", true);
	}


	/**
	 * Converts player locations to a 15x15 set of chunks centered around each player.
	 * This will not duplicate chunks in the list if 2 players are in close proximity
	 * of one another.
	 *
	 * @param array $playerLocations
	 * @return array
	 */

	private function generateSpawnMap(array $playerLocations) : array{
		$convertedChunkList = [];
		$spawnMap = [];

		if(count($playerLocations) > 0) {
			// This will take the location of each player, determine what chunk
			// they are in, and store the chunk in $convertedChunkList.
			foreach($playerLocations as $playerPos) {
				$chunk = $this->convertPositionToChunk($playerPos);

				// If the chunk is already in the list, there's no need to add it again.
				// This method may need to be updated as it compares 2 Vector2 objects which may not work.
				if(!in_array($chunk, $convertedChunkList)){
					array_push($convertedChunkList, $chunk);
				}
			}

			/*
			 * Add a 15x15 group of chunks centered around each player to the spawn map.
			 * This will avoid adding duplicate chunks when players are in close proximity
			 * to one another.
			 */
			foreach($convertedChunkList as $chunk){
				for($x = -7; $x <= 7; $x++) {
					for($z = -7; $z <= 7; $z++){
						if(!in_array(array(($chunk->x + $x),($chunk->y + $z)), $spawnMap)){
							array_push($spawnMap, array(($chunk->x + $x),($chunk->y + $z)));
						}
					}
				}
			}
		}
		return $spawnMap;
	}

	private function convertPositionToChunk(Vector3 $pos) : Vector2{
		$x = floor($pos->x / 16);
		$y = floor($pos->z / 16);
		return new Vector2($x, $y);
	}

	/**
	 * Returns a random (x,y,z) position inside the provided chunk as a Vector3.
	 *
	 * @param array $chunk
	 * @return Vector3
	 */
	private function getRandomLocationInChunk(Vector2 $chunk) : Vector3 {
		$x = mt_rand($chunk->x * 16,(($chunk->x * 16) + 15));
		$y = mt_rand(0,255);
		$z = mt_rand($chunk->y * 16,(($chunk->y * 16) + 15));

		return new Vector3($x, $y, $z);
	}

	private function isValidPackCenter(Vector3 $center, Level $level) : bool{
		if($level->getBlockAt($center->x, $center->y, $center->z)->isTransparent()){
			return true;
		} else {
			return false;
		}
	}

	protected function spawnPackToLevel(Vector3 $center, int $entityId, Level $level, string $type, bool $isBaby = false) : bool{

		// TODO Update to change $maxPackSize based on Mob
		$maxPackSize = 4;
		$currentPackSize = 0;

		for($attempts = 0; $attempts <= 12 and $currentPackSize < $maxPackSize; $attempts++){
			$x = mt_rand(-20,20) + $center->x;
			$z = mt_rand(-20,20) + $center->z;
			$pos = new Position($x, $center->y, $z, $level);

			if($this->isValidSpawnLocation($pos)){
				PureEntities::logOutput("AutoSpawnTask: Spawning Mob to location: $x, $center->y, $z");
				$currentPackSize++;
				return PureEntities::getInstance()->scheduleCreatureSpawn($pos, $entityId, $level, $type, $isBaby) !== null;
			}
		}
		return false;

	}

	private function isValidSpawnLocation(Position $spawnLocation) {
		if(!$spawnLocation->level->getBlockAt($spawnLocation->x, $spawnLocation->y - 1, $spawnLocation->z)->isTransparent()
			and $spawnLocation->level->getBlockAt($spawnLocation->x, $spawnLocation->y, $spawnLocation->z)->isTransparent()
			and $spawnLocation->level->getBlockAt($spawnLocation->x, $spawnLocation->y + 1, $spawnLocation->z)->isTransparent()) {
			return true;
		}
		return false;
	}
}