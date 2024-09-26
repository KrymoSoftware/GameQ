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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace GameQ\Protocols;

use GameQ\Exception\Protocol as Exception;

/**
 * GTA Five M Protocol Class
 *
 * Server base can be found at https://fivem.net/
 *
 * Based on code found at https://github.com/LiquidObsidian/fivereborn-query
 *
 * @author Austin Bischoff <austin@codebeard.com>
 *
 * Adding FiveM Player List by
 * @author Jesse Lukas <eranio@g-one.org>
 */

class Cfxplayers extends Http
{
    /**
     * Packets to send
     */
    protected array $packets = [
        self::PACKET_STATUS => "GET /players.json HTTP/1.0\r\nAccept: */*\r\n\r\n", // Player List
    ];

    /**
     * The protocol being used
     */
    protected string $protocol = 'cfxplayers';

    /**
     * String name of this protocol class
     */
    protected string $name = 'cfxplayers';

    /**
     * Longer string name of this protocol class
     */
    protected string $name_long = "cfxplayers";

    /**
     * Process the response
     *
     * @throws Exception
     */
    public function processResponse(): mixed
    {
        // Make sure we have any players
        if (empty($this->packets_response)) {
            return [];
        }

        // Implode and rip out the JSON
        preg_match('/\{(.*)\}/ms', implode('', $this->packets_response), $matches);

        // Return should be JSON, let's validate
        if (!isset($matches[0]) || ($json = json_decode($matches[0], true)) === null) {
            throw new Exception(__METHOD__ . " JSON response from Stationeers protocol is invalid.");
        }

        // Return json as it should already be well formed
        return [
            'players' => $json,
        ];
    }
}
