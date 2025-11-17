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
 * @package   local_edwiserpagebuilder
 * @copyright (c) 2022 WisdmLabs (https://wisdmlabs.com/) <support@wisdmlabs.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @author    Gourav Govande
 */

namespace local_edwiserpagebuilder;

defined('MOODLE_INTERNAL') || die;

global $CFG;
require_once($CFG->dirroot . "/local/edwiserpagebuilder/lib.php");
define_cdn_constants();

use stdClass;
use context_course;
/**
 * content_manager class handles everything related to block contents.
 */
class content_manager {

    public function get_json_file_data($url) {
        global $CFG;

        require_once($CFG->libdir . "/filelib.php");

        try {
            $c = new \curl;
            $html = $c->get($url);

        } catch (\Exception $e) {
            echo $e;
            exit;
        }

        // Encode and then deccode is jugad for one issue we face while updating the blocks.
        return json_decode($html);

    }

    /**
     * Update the block content with optional batching support.
     *
     * @param int|false $limit Number of blocks to process per batch, false for all blocks
     * @param int $offset Starting position for batch processing
     * @return array|false Array with batch information or false on error
     */
    public function update_block_content($limit = false, $offset = 0) {

        // Here we Update all the blocks content.
        $data = $this->get_json_file_data(BLOCKS_LIST_URL);
        // $pages = $this->get_json_file_data(PAGE_LIST_URL);

        if (isset($pages) && $pages) {
            $data->blocks = array_merge($data->blocks, $pages->pages);
        }

        if (is_array($data) || is_object($data)) {
            foreach ($data as $key => $value) {
                if ($key == "cardlayouts") {
                    // Here we update the layouts list.
                    $this->make_entry_by_data($value, true);
                    continue;
                }
                // Here we update the blocks list.
                $result = $this->make_entry_by_data($value, false, $limit, $offset);

                // If we have a result and it's not complete, return it
                if ($result && isset($result['complete']) && !$result['complete']) {
                    return $result;
                }
            }
        }
        // If we reach here, all blocks are processed
        return [
            'limit' => $limit,
            'total' => 0,
            'processed' => 0,
            'remaining' => 0,
            'complete' => true,
            'next_offset' => 0
        ];
    }

    /**
     * Process blocks data with optional batching support.
     *
     * @param array $blocks Array of block objects to process
     * @param bool $islayout Whether these are layout blocks
     * @param int|false $limit Number of blocks to process per batch, false for all blocks
     * @param int $offset Starting position for batch processing
     * @return array Array with batch information including completion status
     */
    public function make_entry_by_data($blocks, $islayout=false, $limit = false, $offset = 0) {
        $bm = new block_handler();
        $pm = new page_manager();
        $reftable = $bm->get_block_table_name();
        $makeentrycall = "make_entry";

        if ($islayout) {
            $reftable = $bm->get_cl_table_name();
            $makeentrycall = "make_entry_layout";
        }

        $existingblocks = $bm->get_record_from_table($reftable, array(), "title,id,version,updateavailable");
        $totalblocks = count($blocks);
        $count = 0;
        $processed = 0;

        // Apply offset if limit is set
        $startindex = $limit ? $offset : 0;
        $endindex = $limit ? min($offset + $limit, $totalblocks) : $totalblocks;

        // Store all block titles for this batch for later deprecation check
        $batchblocktitles = [];

        for ($i = $startindex; $i < $endindex; $i++) {
            $block = $blocks[$i];

            // Store block title for later deprecation check
            $batchblocktitles[] = $block->title;

            // To change the location for.
            $contenturl = BLOCKS_CONTENT_URL;
            if (isset($block->type) && $block->type == "page") {
                $contenturl = BLOCKS_CONTENT_URL . "pages/";
            }
            if (isset($existingblocks[$block->title])) {
                if ($existingblocks[$block->title]->updateavailable) {
                    continue;
                }
                if (($block->version > $existingblocks[$block->title]->version)) {
                    $content = $this->get_json_file_data($contenturl . $block->title . ".json");
                    if ($content) {
                        // Encrypting the content.
                        $content->content = json_encode($content->content);
                        $recordid = $bm->$makeentrycall($content);
                        if (isset($content->type) && $content->type == "page" && $recordid != null) {
                            $pm->update_page_content($recordid, $content);
                        }
                    }
                }
            } else {
                $content = $this->get_json_file_data($contenturl . $block->title . ".json");
                if ($content) {
                    // Encrypting the content.
                    $content->content = json_encode($content->content);
                    $recordid = $bm->$makeentrycall($content);
                    if (isset($content->type) && $content->type == "page" && $recordid != null) {
                        $pm->update_page_content($recordid, $content);
                    }
                }
            }
            $count++;
            $processed++;
        }
        // Store batch block titles for final deprecation check
        if ($limit) {
            $batchkey = "batch_blocks_{$reftable}_{$offset}";
            set_config($batchkey, json_encode($batchblocktitles), 'local_edwiserpagebuilder');
        } else {
            // No batching - store all block titles for immediate deprecation check
            $allblockskey = "all_blocks_{$reftable}";
            set_config($allblockskey, json_encode($batchblocktitles), 'local_edwiserpagebuilder');
        }

        // Only process deprecated blocks in final batch or when no batching
        if (!$limit || ($offset + $limit >= $totalblocks)) {
            $this->processfinaldeprecation($reftable, $bm, $islayout);
        }

        // Return batch information
        if ($limit) {
            // Calculate actual processed count for this batch
            $actualprocessed = min($limit, $totalblocks - $offset);
            $remaining = max(0, $totalblocks - ($offset + $actualprocessed));
            $iscomplete = ($offset + $actualprocessed) >= $totalblocks;

            return [
                'limit' => $limit,
                'total' => $totalblocks,
                'processed' => $actualprocessed,
                'remaining' => $remaining,
                'complete' => $iscomplete,
                'next_offset' => $offset + $actualprocessed
            ];
        } else {
            // No limit - all blocks processed
            return [
                'limit' => 0,
                'total' => $totalblocks,
                'processed' => $totalblocks,
                'remaining' => 0,
                'complete' => true,
                'next_offset' => 0
            ];
        }
    }

    /**
     * Process final deprecation check with complete information from all batches
     * This method is called only once when all blocks have been processed
     */
    private function processfinaldeprecation($reftable, $bm, $islayout) {
        // Get all batch block titles for this table
        $allbatchtitles = [];

        // First, try to get all blocks from non-batching mode
        $allblockskey = "all_blocks_{$reftable}";
        $allblocksconfig = get_config('local_edwiserpagebuilder', $allblockskey);

        if ($allblocksconfig) {
            // Non-batching mode: use the stored all blocks
            $allbatchtitles = json_decode($allblocksconfig, true);
            // Clean up the all blocks config
            unset_config($allblockskey, 'local_edwiserpagebuilder');
        } else {
            // Batching mode: collect from all batch configs
            $tableprefix = "batch_blocks_{$reftable}_";
            $allconfigs = get_config('local_edwiserpagebuilder');

            foreach ($allconfigs as $key => $value) {
                if (strpos($key, $tableprefix) === 0) {
                    $batchtitles = json_decode($value, true);
                    if (is_array($batchtitles)) {
                        $allbatchtitles = array_merge($allbatchtitles, $batchtitles);
                    }
                }
            }

            // Clean up batch configs for this table
            foreach ($allconfigs as $key => $value) {
                if (strpos($key, $tableprefix) === 0) {
                    unset_config($key, 'local_edwiserpagebuilder');
                }
            }
        }

        // Get existing blocks from database
        $existingblocks = $bm->get_record_from_table($reftable, array(), "title,id,version,updateavailable");

        // Find truly deprecated blocks (exist in database but not in any batch)
        foreach ($existingblocks as $title => $blockdata) {
            if (!in_array($title, $allbatchtitles)) {
                $bm->deprecate_block($reftable, $blockdata);
            }
        }
    }

    public function update_block_content_by_name($blockname, $islayout = false) {
        $bm = new block_handler();
        if ($blockname != "") {
            // Here we update the block content by block name.
            $content = $this->get_json_file_data(BLOCKS_CONTENT_URL . $blockname . ".json");

            if ($content) {
                // Encrypting the content
                $content->content = json_encode($content->content);
                return $bm->update_block_content($content, $islayout);// true to update the content.
            } else {
                return get_string("unabletofetchjson", "local_edwiserpagebuilder");
            }
        } else {
            return get_string("provideproperblockname", "local_edwiserpagebuilder");
        }
    }
    public function can_edit_systemlevel_modules() {
        $context = context_course::instance(1); // System level course.
        if (has_capability('moodle/course:manageactivities', $context)) {
            return true;
        }

        return false;
    }

    public function generate_add_block_modal() {
        global $PAGE, $CFG, $OUTPUT;

        require_once($CFG->libdir . '/blocklib.php');

        $blockslist = [];
        $layoutlist = [];
        if (check_plugin_available("block_edwiseradvancedblock")) {
            $bm = new block_handler();
            $blocks = $bm->fetch_blocks_list(array("type" => "block")); // Fetching Edwiser Blocks

            $templatecontext['edwpageurl'] = strstr($PAGE->url->out(false), "?");
            $templatecontext['can_fetch_blocks'] = true;
            foreach ($blocks as $key => $block) {
                $obj = new stdClass();
                $obj->id = $block->id;
                $actionurl = $PAGE->url->out(false, array('bui_addblock' => '', 'sesskey' => sesskey()));
                $obj->url = strstr($actionurl, "?");// removes string upto substring i.e. "?"
                $obj->name = "edwiseradvancedblock";
                $obj->section = $block->title;
                $obj->title = $block->label;
                $obj->additionalclass = "isblock";
                $obj->thumbnail = str_replace("{{>cdnurl}}", CDNIMAGES, $block->thumbnail);
                $obj->updateavailable = $block->updateavailable;
                $obj->visible = $block->visible;
                if ($block->updateavailable || !$block->visible) {
                    $obj->hasextrabutton = true;
                }
                $blockslist[] = $obj;
            }

            if ($this->can_edit_systemlevel_modules() && check_plugin_available("mod_page")) {
                $templatecontext['can_fetch_pages'] = true;
            }
        }

        $bm = new \block_manager($PAGE);
        $bm->load_blocks(); // Loading all block plugins
        $coreblocks = $bm->get_addable_blocks();

        $blockslist = array_merge($blockslist, $coreblocks); // Fetching other block plugins

        foreach ($blockslist as $key => $block) {
            $actionurl = $PAGE->url->out(false, array('bui_addblock' => '', 'sesskey' => sesskey()));
            $block->url = strstr($actionurl, "?");// removes string upto substring i.e. "?"

            if (!isset($block->thumbnail)) {
                $block->thumbnail = $OUTPUT->image_url('default', 'local_edwiserpagebuilder');
            }

            // Remove edwiseradvancedblock from list
            if (!isset($block->section) && $block->name == "edwiseradvancedblock") {
                unset($blockslist[$key]);
            }

            if (!isset($block->section) && $block->name == "remuiblck") {
                $block->section = " ";
                $block->thumbnail = $OUTPUT->image_url('edwiser', 'local_edwiserpagebuilder');
            }
        }

        $templatecontext['blocks'] = array_values($blockslist);

        return $OUTPUT->render_from_template('local_edwiserpagebuilder/custom_modal', $templatecontext);
    }

    public function create_floating_add_a_block_button() {
        global $OUTPUT;

        $context['buttons']['ele_id'] = 'epbaddblockbutton';
        $context['buttons']['bgcolor'] = '#11c26d';
        $context['buttons']['title'] = get_string('addblock', 'core');
        $context['buttons']['icon'] = 'fa fa-plus';

        return $OUTPUT->render_from_template('local_edwiserpagebuilder/floating_buttons', $context);
    }
}
