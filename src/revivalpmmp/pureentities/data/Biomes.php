<?php
/**
 * PureEntitiesX: Mob AI Plugin for PMMP
 * Copyright (C) 2018 RevivalPMMP
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace revivalpmmp\pureentities\data;


class Biomes{
	// Contains biomes that each entity is allowed to
	// spawn into automatically.
	const ALLOWED_BIOMES_BY_ENTITY_NAME = array(
		"bat" => array(),
		"blaze" => 43,
		"cave_spider" => 40,
		"chicken" => 10,
		"cow" => 11,
		"creeper" => 33,
		"donkey" => 24,
		"elder_guardian" => 50,
		"enderman" => 38,
		"fire_ball" => 85,
		"ghast" => 41,
		"guardian" => 49,
		"horse" => 23,
		"husk" => 47,
		"iron_golem" => 20,
		"magma_cube" => 42,
		"mooshroom" => 16,
		"mule" => 25,
		"ocelot" => 22,
		"parrot" => 30,
		"pig" => 12,
		"pig_zombie" => 36,
		"rabbit" => 18,
		"sheep" => 13,
		"silverfish" => 39,
		"skeleton" => 34,
		"slime" => 37,
		"snow_golem" => 21,
		"stray" => 46,
		"spider" => 35,
		"squid" => 17,
		"villager" => 15,
		"wither_skeleton" => 48,
		"wolf" => 14,
		"zombie" => 32,
		"zombie_pigman" => 36,
		"zombie_villager" => 44
	);
}