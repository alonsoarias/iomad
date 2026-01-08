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
 * Faculty reviewer management redirect.
 *
 * This page has been consolidated into the roles management page.
 * Redirects to the dean role management in roles.php.
 *
 * @package   local_jobboard
 * @copyright 2024-2026 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();

// Redirect to the roles page with dean role selected.
// Faculty assignment for deans is now integrated into the roles page.
redirect(new moodle_url('/local/jobboard/admin/roles.php', ['role' => 'jobboard_dean']));
