<?php

/*  PureEntitiesX: Mob AI Plugin for PMMP
    Copyright (C) 2017  RevivalPMMP

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

namespace revivalpmmp\pureentities\entity;

use pocketmine\block\Block;
use pocketmine\block\Fence;
use pocketmine\block\FenceGate;
use pocketmine\block\Liquid;
use pocketmine\block\Stair;
use pocketmine\block\StoneSlab;
use pocketmine\entity\Creature;
use pocketmine\entity\Effect;
use pocketmine\item\Item;
use pocketmine\entity\Item as EntityItem;
use pocketmine\math\Math;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\Player;
use revivalpmmp\pureentities\features\IntfCanEquip;
use revivalpmmp\pureentities\features\IntfTameable;
use revivalpmmp\pureentities\PureEntities;
use pocketmine\utils\TextFormat as TF;


abstract class JumpingEntity extends BaseEntity{

	/*
	 * Intended for creatures whose normal movement is jumping or hopping.
	 * Examples would be Rabbits, Slimes, and Magma Cubes.
	 */

	protected function checkTarget(bool $checkSkip = true){
		if(($checkSkip and $this->isCheckTargetAllowedBySkip()) or !$checkSkip){
			if($this->isKnockback()){
				return;
			}

			if(($this->moveTime <= 0 or !($this->getBaseTarget() instanceof Vector3)) and $this->motionY == 0){
				if(!$this->idlingComponent->checkAndSetIdling()){
					$this->findRandomLocation();
				}
			}
		}
	}

	public function updateMove($tickDiff){
		// TODO
		if($this->isClosed() or $this->getLevel() == null or !$this->isMovement()){
			return null;
		}

		if($this->isKnockback()){
			$this->move($this->motionX * $tickDiff, $this->motionY, $this->motionZ * $tickDiff);
			$this->motionY -= 0.2 * $tickDiff;
			$this->updateMovement();
			return null;
		}

		if($this->idlingComponent->isIdling() and !$this->idlingComponent->stopIdling($tickDiff)){
			$this->idlingComponent->doSomeIdleStuff($tickDiff);
			return null;
		}

		$startingTarget = $this->getBaseTarget();
		$this->checkTarget();
		$updatedTarget = $this->getBaseTarget();

		if($updatedTarget instanceof Creature or $updatedTarget instanceof Block or $startingTarget !== $updatedTarget and
			$updatedTarget !== null
		){
			$targetX = $updatedTarget->x - $this->x;
			$targetY = $updatedTarget->y - $this->y;
			$targetZ = $updatedTarget->z - $this->z;

			$distance = $this->distanceSquared($updatedTarget);

			if($this instanceof IntfTameable and
				$updatedTarget instanceof Player and
				$this->isTamed() and
				$distance <= 6
			){ // before moving nearer to the player, check if distance
				// this entity is tamed and the target is the owner - hold distance 4 blocks
				$this->stayTime = 50; // rest for 50 ticks before moving on ...
			}
			$diff = abs($targetX) + abs($targetZ);
			if($targetX ** 2 + $targetZ ** 2 < 0.7){
				$this->motionX = 0;
				$this->motionZ = 0;
			}elseif($diff > 0){
				$this->motionX = $this->getSpeed() * 0.15 * ($targetX / $diff);
				$this->motionZ = $this->getSpeed() * 0.15 * ($targetZ / $diff);
				$this->yaw = -atan2($targetX / $diff, $targetZ / $diff) * 180 / M_PI;
			}
			$this->pitch = $targetY == 0 ? 0 : rad2deg(-atan2($targetY, sqrt($targetX ** 2 + $targetZ ** 2)));
		}

		$dx = $this->motionX * $tickDiff;
		$dz = $this->motionZ * $tickDiff;
		$isJump = false;
		if($this->isCollidedHorizontally or $this->isInsideOfWater()){
			$isJump = $this->checkJump($dx, $dz);
		}
		if($this->stayTime > 0){
			$this->stayTime -= $tickDiff;
			$this->move(0, $this->motionY * $tickDiff, 0);
		}else{
			$futureLocation = new Vector2($this->x + $dx, $this->z + $dz);
			$this->move($dx, $this->motionY * $tickDiff, $dz);
			$myLocation = new Vector2($this->x, $this->z);
			if(($futureLocation->x != $myLocation->x || $futureLocation->y != $myLocation->y) && !$isJump){
				$this->moveTime -= 90 * $tickDiff;
			}
		}

		if(!$isJump){
			if($this->isOnGround()){
				if($this->motionX > 0.1 or $this->motionZ > 0.1){
					$this->jump();
				} else {
					$this->motionY = 0;
				}
			}else if($this->motionY > -$this->gravity * 4){
				if(!($this->getLevel()->getBlock(new Vector3(Math::floorFloat($this->x), (int) ($this->y + 0.8), Math::floorFloat($this->z))) instanceof Liquid)){
					$this->motionY -= $this->gravity * 1;
				}
			}else{
				$this->motionY -= $this->gravity * $tickDiff;
			}
		}
		$this->updateMovement();
		return $this->getBaseTarget();
	}

	/**
	 * This method checks the jumping for the entity. It should only be called when isCollidedHorizontally is set to
	 * true on the entity.
	 *
	 * @param int $dx
	 * @param int $dz
	 *
	 * @return bool
	 */
	protected function checkJump($dx, $dz){
		PureEntities::logOutput("$this: entering checkJump [dx:$dx] [dz:$dz]");
		if($this->motionY == $this->gravity * 2){ // swimming
			PureEntities::logOutput("$this: checkJump(): motionY == gravity*2");
			return $this->getLevel()->getBlock(new Vector3(Math::floorFloat($this->x), (int) $this->y, Math::floorFloat($this->z))) instanceof Liquid;
		}else{ // dive up?
			if($this->getLevel()->getBlock(new Vector3(Math::floorFloat($this->x), (int) ($this->y + 0.8), Math::floorFloat($this->z))) instanceof Liquid){
				PureEntities::logOutput("$this: checkJump(): instanceof liquid");
				$this->motionY = $this->gravity * 2; // set swimming (rather walking on water ;))
				return true;
			}
		}

		/*if ($this->motionY > 0.1 or $this->stayTime > 0) { // when entities are "hunting" they sometimes have a really small y motion (lesser than 0.1) so we need to take this into account
			PureEntities::logOutput("$this: checkJump(): onGround:" . $this->isOnGround() . ", stayTime:" . $this->stayTime . ", motionY:" . $this->motionY);
			return false;
		} */

		if($this->getDirection() === null){ // without a direction jump calculation is not possible!
			PureEntities::logOutput("$this: checkJump(): no direction given ...");
			return false;
		}

		PureEntities::logOutput("$this: checkJump(): position is [x:" . $this->x . "] [y:" . $this->y . "] [z:" . $this->z . "]");

		// sometimes entities overlap blocks and the current position is already the next block in front ...
		// they overlap especially when following an entity - you can see it when the entity (e.g. creeper) is looking
		// in your direction but cannot jump (is stuck). Then the next line should apply
		$blockingBlock = $this->getLevel()->getBlock($this->getPosition());
		if($blockingBlock->canPassThrough()){ // when we can pass through the current block then the next block is blocking the way
			try{
				$blockingBlock = $this->getTargetBlock(2); // just for correction use 2 blocks ...
			}catch(\InvalidStateException $ex){
				PureEntities::logOutput("Caught InvalidStateException for getTargetBlock", PureEntities::DEBUG);
				return false;
			}
		}
		if($blockingBlock != null and !$blockingBlock->canPassThrough() and $this->getMaxJumpHeight() > 0){
			// we cannot pass through the block that is directly in front of entity - check if jumping is possible
			$upperBlock = $this->getLevel()->getBlock($blockingBlock->add(0, 1, 0));
			$secondUpperBlock = $this->getLevel()->getBlock($blockingBlock->add(0, 2, 0));
			PureEntities::logOutput("$this: checkJump(): block in front is $blockingBlock, upperBlock is $upperBlock, second Upper block is $secondUpperBlock");
			// check if we can get through the upper of the block directly in front of the entity
			if($upperBlock->canPassThrough() && $secondUpperBlock->canPassThrough()){
				if($blockingBlock instanceof Fence || $blockingBlock instanceof FenceGate){ // cannot pass fence or fence gate ...
					$this->motionY = $this->gravity;
					PureEntities::logOutput("$this: checkJump(): found fence or fence gate!", PureEntities::DEBUG);
				}else if($blockingBlock instanceof StoneSlab or $blockingBlock instanceof Stair){ // on stairs entities shouldn't jump THAT high
					$this->motionY = $this->gravity * 4;
					PureEntities::logOutput("$this: checkJump(): found slab or stair!", PureEntities::DEBUG);
				}else if($this->motionY < ($this->gravity * 3.2)){ // Magic
					PureEntities::logOutput("$this: checkJump(): set motion to gravity * 3.2!", PureEntities::DEBUG);
					$this->motionY = $this->gravity * 3.2;
				}else{
					PureEntities::logOutput("$this: checkJump(): nothing else!", PureEntities::DEBUG);
					$this->motionY += $this->gravity * 0.25;
				}
				return true;
			}elseif(!$upperBlock->canPassThrough()){
				PureEntities::logOutput("$this: checkJump(): cannot pass through the upper blocks!", PureEntities::DEBUG);
				$this->yaw = $this->getYaw() + mt_rand(-120, 120) / 10;
			}
		}elseif($this->isOnGround() and ($this->motionX != 0 or $this->motionZ != 0)){
			$this->motionY += $this->gravity * 2.2;
			return true;
		}else{
			PureEntities::logOutput("$this: checkJump(): no need to jump. Block can be passed! [canPassThrough:" . $blockingBlock->canPassThrough() . "] " .
				"[jumpHeight:" . $this->getMaxJumpHeight() . "] [checkedBlock:" . $blockingBlock . "]", PureEntities::DEBUG);

		}
		return false;
	}

	/**
	 * Finds the next random location starting from current x/y/z and sets it as base target
	 */
	public function findRandomLocation(){
		PureEntities::logOutput("$this(findRandomLocation): entering");

		// TODO: Fix 'magic' numbers in findRandomLocation for x and z by finding blocks within the entities line of sight.
		/* Magic numbers are not ok for AI.  Selecting a "random" target can still have
		 * measurable or biased parameters.  For instance, an entity won't choose to move towards
		 * something it can't necessarily see.  This means that the next "random" location
		 * needs to be something the entity can see.
		 *
		 * Scenario: A sheep wants to go to the other side of a hill because it's curious if there's wheat.
		 * The sheep cannot see the other side of the hill and has to first go to the top of the hill (which
		 * it can see) to make sure there's something on the other side to go to.  Sheep's next target becomes
		 * the top of the hill where the sheep can then determine if it's safe to continue beyond the hill.
		 */

		$x = mt_rand(20, 100);
		$z = mt_rand(20, 100);

		// Once magic x and z numbers are fixed, moveTime will become obsolete.  Instead, we will check if
		// target is reached. If not, check if we're moving by comparing last previous position to new position.
		// If new position is same as old position, increase blockedMovementTicks counter. Check how long we've been stuck
		// and decide on new direction if necessary.
		$this->moveTime = mt_rand(300, 1200);

		// set a real y coordinate ...
		$yPos = PureEntities::getSuitableHeightPosition($x, $this->y, $z, $this->getLevel());

		$this->setBaseTarget(new Vector3(
			mt_rand(0, 1) ? $this->x + $x : $this->x - $x,
			$yPos !== null ? $yPos->y : $this->y,
			mt_rand(0, 1) ? $this->z + $z : $this->z - $z));
	}
}