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

namespace GameQ\Protocols;

/**
 * Current identifier for Luanti, formerly known as Minetest.
 *
 * @author Sascha Greuel <sascha@softcreatr.de>
 */
class Luanti extends Minetest
{
    protected string $protocol = 'luanti';
    protected string $name = 'luanti';
    protected string $name_long = 'Luanti';
}
