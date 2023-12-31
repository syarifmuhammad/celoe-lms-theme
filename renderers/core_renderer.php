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
 * core_renderer.php
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

defined('MOODLE_INTERNAL') || die();

/**
 * This class has function for renderer user menu and login page
 * @copyright  2015 onwards LMSACE Dev Team (http://www.lmsace.com)
 * @author    LMSACE Dev Team
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class theme_celoe_core_renderer extends theme_boost\output\core_renderer
{

    /**
     * This function have the code to create the earlier user menu from the settings.
     * @return string
     */
    public function earlier_user_menu()
    {
        global $USER, $CFG, $OUTPUT;

        if ($CFG->branch > "27") {
            return '';
        }
        $uname = fullname($USER, true);
        $dlink = new moodle_url("/my");
        $plink = new moodle_url("/user/profile.php", array("id" => $USER->id));
        $lo = new moodle_url('/login/logout.php', array('sesskey' => sesskey()));
        $dashboard = get_string('myhome');
        $profile = get_string('profile');
        $logout = get_string('logout');

        $content = '<li class="dropdown no-divider"><a class="dropdown-toggle"
        data-toggle="dropdown" href="#">' . $uname . '<i class="fa fa-chevron-down"></i><span class="caretup"></span></a><ul class="dropdown-menu"><li><a href="' . $dlink . '">' . $dashboard . '</a></li><li><a href="' . $plink . '">' . $profile . '</a></li><li><a href="' . $lo . '">' . $logout . '</a></li></ul></li>';

        return $content;
    }

    /**
     * Render the login page template.
     * @param \core_auth\output\login $form
     * @return string
     */
    public function render_login(\core_auth\output\login $form)
    {
        global $CFG, $PAGE, $SITE, $OUTPUT;

        $context = $form->export_for_template($this);

        // Override because rendering is not supported in template yet.
        $context->cookieshelpiconformatted = $this->help_icon('cookiesenabled');
        $context->errorformatted = $this->error_text($context->error);
        $url = $OUTPUT->image_url('login/amico', 'theme');
        // if ($url) {
        //     $url = $url->out(false);
        // }
        $context->logourl = $url;
        $context->sitename = format_string($SITE->fullname, true, ['
            context' => context_course::instance(SITEID), "
            escape" => false]);
        $maincontent = $this->render_from_template('theme_celoe/login_form', $context);
        return $maincontent;
    }

    public function block(block_contents $bc, $region) {
        $bc = clone($bc); // Avoid messing up the object passed in.
        if (empty($bc->blockinstanceid) || !strip_tags($bc->title)) {
            $bc->collapsible = block_contents::NOT_HIDEABLE;
        }

        $id = !empty($bc->attributes['id']) ? $bc->attributes['id'] : uniqid('block-');
        $context = new stdClass();
        $context->skipid = $bc->skipid;
        $context->blockinstanceid = $bc->blockinstanceid ?: uniqid('fakeid-');
        $context->dockable = $bc->dockable;
        $context->id = $id;
        $context->hidden = $bc->collapsible == block_contents::HIDDEN;
        $context->skiptitle = strip_tags($bc->title);
        $context->showskiplink = !empty($context->skiptitle);
        $context->arialabel = $bc->arialabel;
        $context->ariarole = !empty($bc->attributes['role']) ? $bc->attributes['role'] : 'complementary';
        $context->class = $bc->attributes['class'];
        $context->type = $bc->attributes['data-block'];
        $title_array = explode(' ', $bc->title);
        for ($i=ceil(count($title_array)/2); $i < count($title_array); $i++) {
            $title_array[$i] = '<span class="sub-card-title">'.$title_array[$i].'</span>';
        }
        $context->title = implode(' ', $title_array);
        $context->content = $bc->content;
        $context->annotation = $bc->annotation;
        $context->footer = $bc->footer;
        $context->hascontrols = !empty($bc->controls);
        if ($context->hascontrols) {
            $context->controls = $this->block_controls($bc->controls, $id);
        }

        return $this->render_from_template('theme_celoe/core/block', $context);
    }

    public function full_header() {
        global $PAGE;

        if ($PAGE->include_region_main_settings_in_header_actions() && !$PAGE->blocks->is_block_present('settings')) {
            // Only include the region main settings if the page has requested it and it doesn't already have
            // the settings block on it. The region main settings are included in the settings block and
            // duplicating the content causes behat failures.
            $PAGE->add_header_action(html_writer::div(
                $this->region_main_settings_menu(),
                'd-print-none',
                ['id' => 'region-main-settings-menu']
            ));
        }

        $header = new stdClass();
        $header->settingsmenu = $this->context_header_settings_menu();
        $header->contextheader = $this->context_header();
        $header->hasnavbar = empty($PAGE->layout_options['nonavbar']);
        $header->navbar = $this->navbar();
        $header->pageheadingbutton = $this->page_heading_button();
        $header->courseheader = $this->course_header();
        $header->headeractions = $PAGE->get_header_actions();
        $header->title = $this->page_title();
        return $this->render_from_template('theme_celoe/core/full_header', $header);
    }
}
