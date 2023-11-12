<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * version.php
 *
 * This is built using the boost template to allow for new theme's using
 * Moodle's new Boost theme engine
 *
 * @package     theme_celoe
 * @copyright   2023 CeLoe Dev Team, celoe.ittelkom-sby.ac.id
 * @author      CeLoe Dev Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

defined('MOODLE_INTERNAL') || die;

// The current module version (Date: YYYYMMDDXX).
$plugin->version   = 2023102705;

// Version's maturity level.
$plugin->maturity = MATURITY_STABLE;

// Plugin release version.
$plugin->release = 'v1.0';

// Requires this Moodle version.
$plugin->requires  = 2019111800;

// Full name of the plugin (used for diagnostics).
$plugin->component = 'theme_celoe';

// Plugin dependencies and dependencies version.
$plugin->dependencies = array(
    'theme_boost'  => 2019022600,
);
