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
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace GameQ\Protocols;

/**
 * Enshrouded Protocol Class
 *
 * @author Sascha Greuel <sascha@softcreatr.de>
 */
class Enshrouded extends Source
{
    protected string $name = 'enshrouded';

    protected string $name_long = 'Enshrouded';

    /**
     * Retained with its original default value for 5.x extension compatibility.
     */
    protected int $port_diff = 1;

    /**
     * The official dedicated server uses its configured query port directly.
     */
    public function portDiff(): int
    {
        return 0;
    }

    /**
     * The official dedicated server advertises its query port as the game port.
     */
    public function findQueryPort(int $clientPort): int
    {
        return $clientPort;
    }
}
