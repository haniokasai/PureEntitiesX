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

namespace revivalpmmp\pureentities\data;


interface Data {

	// Entity Widths
	const WIDTHS = array(

		NetworkIDs::NETWORK_IDS["bat"] => 0.484,
		NetworkIDs::NETWORK_IDS["blaze"] => 1.25,
		NetworkIDs::NETWORK_IDS["cave_spider"] => 1.438,
		NetworkIDs::NETWORK_IDS["chicken"] => 1,
		NetworkIDs::NETWORK_IDS["cow"] => 1.5,
		NetworkIDs::NETWORK_IDS["creeper"] => 0.7,
		NetworkIDs::NETWORK_IDS["donkey"] => 1.2,
		NetworkIDs::NETWORK_IDS["elder_guardian"] => 1.9975,
		NetworkIDs::NETWORK_IDS["ender_charge"] => 1.0,
		NetworkIDs::NETWORK_IDS["ender_dragon"] => 2.5,
		NetworkIDs::NETWORK_IDS["enderman"] => 1.094,
		NetworkIDs::NETWORK_IDS["endermite"] => 0.4,
		NetworkIDs::NETWORK_IDS["evoker"] => 1.031,
		NetworkIDs::NETWORK_IDS["fire_ball"] => 0.5,
		NetworkIDs::NETWORK_IDS["ghast"] => 4.5,
		NetworkIDs::NETWORK_IDS["guardian"] => 0,
		NetworkIDs::NETWORK_IDS["horse"] => 1.3,
		NetworkIDs::NETWORK_IDS["husk"] => 1.031,
		NetworkIDs::NETWORK_IDS["iron_golem"] => 2.688,
		NetworkIDs::NETWORK_IDS["llama"] => 0.9,
		NetworkIDs::NETWORK_IDS["magma_cube"] => 1.2,
		NetworkIDs::NETWORK_IDS["mooshroom"] => 1.781,
		NetworkIDs::NETWORK_IDS["mule"] => 1.2,
		NetworkIDs::NETWORK_IDS["ocelot"] => 0.8,
		NetworkIDs::NETWORK_IDS["parrot"] => 0.5,
		NetworkIDs::NETWORK_IDS["pig"] => 1.5,
		NetworkIDs::NETWORK_IDS["pig_zombie"] => 1.125,
		NetworkIDs::NETWORK_IDS["polar_bear"] => 1.3,
		NetworkIDs::NETWORK_IDS["rabbit"] => 0.4,
		NetworkIDs::NETWORK_IDS["sheep"] => 1.2,
		NetworkIDs::NETWORK_IDS["shulker"] => 1.0,
		NetworkIDs::NETWORK_IDS["silverfish"] => 1.094,
		NetworkIDs::NETWORK_IDS["skeleton"] => 0.875,
		NetworkIDs::NETWORK_IDS["skeleton_horse"] => 1.3,
		NetworkIDs::NETWORK_IDS["slime"] => 1.2,
		NetworkIDs::NETWORK_IDS["snow_golem"] => 1.281,
		NetworkIDs::NETWORK_IDS["stray"] => 0.875,
		NetworkIDs::NETWORK_IDS["spider"] => 2.062,
		NetworkIDs::NETWORK_IDS["squid"] => 0,
		NetworkIDs::NETWORK_IDS["vex"] => 0.4,
		NetworkIDs::NETWORK_IDS["villager"] => 0.938,
		NetworkIDs::NETWORK_IDS["vindicator"] => 0.6,
		NetworkIDs::NETWORK_IDS["witch"] => 0.6,
		NetworkIDs::NETWORK_IDS["wither"] => 0.9,
		NetworkIDs::NETWORK_IDS["wither_skeleton"] => 0.875,
		NetworkIDs::NETWORK_IDS["wolf"] => 1.2,
		NetworkIDs::NETWORK_IDS["zombie"] => 1.031,
		NetworkIDs::NETWORK_IDS["zombie_pigman"] => 2.0,
		NetworkIDs::NETWORK_IDS["zombie_villager"] => 1.031
	);

	// Entity Heights
	const HEIGHTS = array(
		NetworkIDs::NETWORK_IDS["bat"] => 0.5,
		NetworkIDs::NETWORK_IDS["blaze"] => 1.5,
		NetworkIDs::NETWORK_IDS["cave_spider"] => 0.547,
		NetworkIDs::NETWORK_IDS["chicken"] => 0.8,
		NetworkIDs::NETWORK_IDS["cow"] => 1.2,
		NetworkIDs::NETWORK_IDS["creeper"] => 1.7,
		NetworkIDs::NETWORK_IDS["donkey"] => 1.562,
		NetworkIDs::NETWORK_IDS["elder_guardian"] => 1.9975,
		NetworkIDs::NETWORK_IDS["ender_charge"] => 1.0,
		NetworkIDs::NETWORK_IDS["ender_dragon"] => 1.0,
		NetworkIDs::NETWORK_IDS["enderman"] => 2.875,
		NetworkIDs::NETWORK_IDS["endermite"] => 0.3,
		NetworkIDs::NETWORK_IDS["evoker"] => 2.125,
		NetworkIDs::NETWORK_IDS["fire_ball"] => 0.5,
		NetworkIDs::NETWORK_IDS["ghast"] => 4.5,
		NetworkIDs::NETWORK_IDS["guardian"] => 0,
		NetworkIDs::NETWORK_IDS["horse"] => 1.5,
		NetworkIDs::NETWORK_IDS["husk"] => 2.0,
		NetworkIDs::NETWORK_IDS["iron_golem"] => 1.625,
		NetworkIDs::NETWORK_IDS["llama"] => 1.87,
		NetworkIDs::NETWORK_IDS["magma_cube"] => 1.2,
		NetworkIDs::NETWORK_IDS["mooshroom"] => 1.875,
		NetworkIDs::NETWORK_IDS["mule"] => 1.562,
		NetworkIDs::NETWORK_IDS["ocelot"] => 0.8,
		NetworkIDs::NETWORK_IDS["parrot"] => 0.9,
		NetworkIDs::NETWORK_IDS["pig"] => 1.0,
		NetworkIDs::NETWORK_IDS["pig_zombie"] => 2.03,
		NetworkIDs::NETWORK_IDS["polar_bear"] => 1.4,
		NetworkIDs::NETWORK_IDS["rabbit"] => 0.5,
		NetworkIDs::NETWORK_IDS["sheep"] => 0.6,
		NetworkIDs::NETWORK_IDS["shulker"] => 1.0,
		NetworkIDs::NETWORK_IDS["silverfish"] => 0.438,
		NetworkIDs::NETWORK_IDS["skeleton"] => 2.0,
		NetworkIDs::NETWORK_IDS["skeleton_horse"] => 1.5,
		NetworkIDs::NETWORK_IDS["slime"] => 1.2,
		NetworkIDs::NETWORK_IDS["snow_golem"] => 1.875,
		NetworkIDs::NETWORK_IDS["stray"] => 2.0,
		NetworkIDs::NETWORK_IDS["spider"] => 0.781,
		NetworkIDs::NETWORK_IDS["squid"] => 0.0,
		NetworkIDs::NETWORK_IDS["vex"] => 0.8,
		NetworkIDs::NETWORK_IDS["villager"] => 2.0,
		NetworkIDs::NETWORK_IDS["vindicator"] => 1.95,
		NetworkIDs::NETWORK_IDS["witch"] => 1.95,
		NetworkIDs::NETWORK_IDS["wither"] => 3.5,
		NetworkIDs::NETWORK_IDS["wither_skeleton"] => 2.0,
		NetworkIDs::NETWORK_IDS["wolf"] => 0.969,
		NetworkIDs::NETWORK_IDS["zombie"] => 2.01,
		NetworkIDs::NETWORK_IDS["zombie_pigman"] => 2.0,
		NetworkIDs::NETWORK_IDS["zombie_villager"] => 2.125
	);

}
