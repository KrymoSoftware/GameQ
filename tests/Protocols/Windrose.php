<?php

/**
 * This file is part of GameQ.
 *
 * GameQ is free software; you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * GameQ is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Lesser General Public License for more details.
 */

namespace GameQ\Tests\Protocols;

/**
 * @author Sascha Greuel <sascha@softcreatr.de>
 */
class Windrose extends Base
{
    public function testWindrosePlusStatusResponseIsParsedWithoutNetworkAccess(): void
    {
        $result = $this->queryTest(
            '127.0.0.1:7777',
            'windrose',
            [],
            false,
            [
                'status_response' => [
                    'server' => [
                        'name' => 'The Crimson Tide',
                        'game' => 'Windrose',
                        'version' => '0.10.0.5.120',
                        'windrose_plus' => '1.3.17',
                        'invite_code' => 'sailaway',
                        'password_protected' => true,
                        'max_players' => 10,
                        'player_count' => 2,
                        'game_port' => 7777,
                    ],
                    'players' => [
                        [
                            'name' => 'Captain Morgan',
                            'player_id' => 12,
                            'session_id' => 'player:12',
                            'actor_id' => 'BP_R5Character_C_12',
                            'alive' => true,
                            'x' => 14520,
                            'y' => -8340,
                            'z' => 102,
                            'health' => 100,
                        ],
                        [
                            'name' => 'Anne Bonny',
                            'player_id' => 17,
                            'alive' => false,
                        ],
                    ],
                    'perf' => ['world_time' => 1352.5],
                    'multipliers' => [
                        'xp' => 3,
                        'loot' => 2,
                        'craft_efficiency' => 1,
                        'cooking_speed' => 1.5,
                        'harvest_yield' => 2,
                    ],
                    'timestamp' => 1_776_019_200,
                ],
            ],
        );

        self::assertTrue($result['gq_online']);
        self::assertSame('The Crimson Tide', $result['name']);
        self::assertSame(10, $result['max_players']);
        self::assertSame(2, $result['player_count']);
        self::assertTrue($result['password_protected']);
        self::assertSame(8780, $result['gq_port_query']);
        self::assertSame('1.3.17', $result['windrose_plus']);
        self::assertSame('sailaway', $result['invite_code']);
        self::assertSame(1352.5, $result['world_time']);
        self::assertSame([
            'xp' => 3.0,
            'loot' => 2.0,
            'craft_efficiency' => 1.0,
            'cooking_speed' => 1.5,
            'harvest_yield' => 2.0,
        ], $result['multipliers']);
        $players = $result['players'] ?? null;
        self::assertIsArray($players);
        self::assertCount(2, $players);

        $firstPlayer = $players[0] ?? null;
        self::assertIsArray($firstPlayer);
        self::assertSame('Captain Morgan', $firstPlayer['name'] ?? null);
        self::assertSame(14520.0, $firstPlayer['x'] ?? null);

        $secondPlayer = $players[1] ?? null;
        self::assertIsArray($secondPlayer);
        self::assertFalse($secondPlayer['alive'] ?? null);
    }

    public function testVanillaWindroseStatusResponseIsNotAccepted(): void
    {
        $result = $this->queryTest(
            '127.0.0.1:7777',
            'windrose',
            [],
            false,
            ['status_response' => ['server' => ['game' => 'Windrose']]],
        );

        self::assertFalse($result['gq_online']);
    }
}
