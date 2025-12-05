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
 * Public vacancies page redirect.
 *
 * This file redirects to the main entry point (index.php) with view=public.
 * All access should go through index.php for consistency.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Get any passed parameters to forward.
$id = optional_param('id', 0, PARAM_INT);
$params = ['view' => 'public'];
if ($id) {
    $params['id'] = $id;
}

// Redirect to index.php with public view.
redirect(new moodle_url('/local/jobboard/index.php', $params));
