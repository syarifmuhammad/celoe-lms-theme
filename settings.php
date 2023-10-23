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
 * settings.php
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

$settings = null;

if (is_siteadmin()) {
    $settings = new theme_boost_admin_settingspage_tabs('themesettingceloe', get_string('configtitle', 'theme_celoe'));
    $ADMIN->add('themes', new admin_category('theme_celoe', 'Celoe'));

    /* General Settings */
    $temp = new admin_settingpage('theme_celoe_general', get_string('themegeneralsettings', 'theme_celoe'));

    // Logo file setting.
    $name = 'theme_celoe/logo';
    $title = get_string('logo', 'theme_celoe');
    $description = get_string('logodesc', 'theme_celoe');
    $setting = new admin_setting_configstoredfile($name, $title, $description, 'logo');
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Custom CSS file.
    $name = 'theme_celoe/customcss';
    $title = get_string('customcss', 'theme_celoe');
    $description = get_string('customcssdesc', 'theme_celoe');
    $default = '';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Color schemes.
    $colorscheme = get_string('colorscheme', 'theme_celoe');
    $defaultcolor = get_string('default_color', 'theme_celoe');
    $colorhdr = get_string('color_schemes_heading', 'theme_celoe');

    // Theme Color scheme chooser.
    $name = 'theme_celoe/patternselect';
    $title = get_string('patternselect', 'theme_celoe');
    $description = get_string('patternselectdesc', 'theme_celoe');
    $default = 'default';
    $choices = array(
        'default' => get_string('lavender', 'theme_celoe'),
        '1' => get_string('green', 'theme_celoe'),
        '2' => get_string('blue', 'theme_celoe'),
        '3' => get_string('warm_red', 'theme_celoe'),
        '4' => get_string('dark_cyan', 'theme_celoe'),
        '5' => get_string('custom_red', 'theme_celoe')
    );

    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $setting->set_updatedcallback('theme_reset_all_caches');
    $temp->add($setting);

    // Theme Color scheme static content.
    $pimg = array();
    global $CFG;
    $cp = $CFG->wwwroot . '/theme/celoe/pix/color/';
    $pimg = array(
        $cp . 'default.jpg', $cp . 'colorscheme-1.jpg', $cp . 'colorscheme-2.jpg',
        $cp . 'colorscheme-3.jpg', $cp . 'colorscheme-4.jpg', $cp . 'colorscheme-5.jpg'
    );

    $themepattern = '<ul class="thumbnails theme-color-schemes"><li class=""><div class="thumbnail"><img src="' . $pimg[0] . '" alt="default" width="100" height="100"/><h6>' . get_string("default", 'theme_celoe') . '</h6></div></li><li class=""><div class="thumbnail"><img src="' . $pimg[1] . '" alt="pattern1" width="100" height="100"/><h6>' . get_string("color_1", 'theme_celoe') . '</h6></div></li><li class=""><div class="thumbnail"><img src="' . $pimg[2] . '" alt="pattern2" width="100" height="100"/><h6>' . get_string("color_2", 'theme_celoe') . '</h6></div></li><li class=""><div class="thumbnail"><img src="' . $pimg[3] . '" alt="pattern3" width="100" height="100"/><h6>' . get_string("color_3", 'theme_celoe') . '</h6></div></li><li class=""><div class="thumbnail"><img src="' . $pimg[4] . '" alt="pattern4" width="100" height="100"/><h6>' . get_string("color_4", 'theme_celoe') . '</h6></div></li><li class=""><div class="thumbnail"><img src="' . $pimg[5] . '" alt="pattern5" width="100" height="100"/><h6>' . get_string("color_5", 'theme_celoe') . '</h6></div></li></ul>';

    $temp->add(new admin_setting_heading('theme_celoe_patternheading', '', $themepattern));

    // Promoted Courses Start.
    // Promoted Courses Heading.
    $name = 'theme_celoe_promotedcoursesheading';
    $heading = get_string('promotedcoursesheading', 'theme_celoe');
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Enable / Disable Promoted Courses.
    $name = 'theme_celoe/pcourseenable';
    $title = get_string('pcourseenable', 'theme_celoe');
    $description = '';
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $temp->add($setting);

    // Promoted courses Block title.
    $name = 'theme_celoe/promotedtitle';
    $title = get_string('pcourses', 'theme_celoe') . ' ' . get_string('title', 'theme_celoe');
    $description = get_string('promotedtitledesc', 'theme_celoe');
    $default = 'lang:promotedtitledefault';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_celoe/promotedcourses';
    $title = get_string('pcourses', 'theme_celoe');
    $description = get_string('pcoursesdesc', 'theme_celoe');
    $default = array();

    $courses[0] = '';
    $cnt = 0;
    if ($ccc = get_courses('all', 'c.sortorder ASC', 'c.id,c.shortname,c.visible,c.category')) {
        foreach ($ccc as $cc) {
            if ($cc->visible == "0" || $cc->id == "1") {
                continue;
            }
            $cnt++;
            $courses[$cc->id] = $cc->shortname;
            // Set some courses for default option.
            if ($cnt < 8) {
                $default[] = $cc->id;
            }
        }
    }
    $coursedefault = implode(",", $default);
    $setting = new admin_setting_configtextarea($name, $title, $description, $coursedefault);
    $temp->add($setting);
    $settings->add($temp);
    // Promoted Courses End.

    /*Slideshow Settings Start.*/
    $temp = new admin_settingpage('theme_celoe_slideshow', get_string('slideshowheading', 'theme_celoe'));
    $temp->add(new admin_setting_heading(
        'theme_celoe_slideshow',
        get_string('slideshowheadingsub', 'theme_celoe'),
        format_text(get_string('slideshowdesc', 'theme_celoe'), FORMAT_MARKDOWN)
    ));

    // Display Slideshow.
    $name = 'theme_celoe/toggleslideshow';
    $title = get_string('toggleslideshow', 'theme_celoe');
    $description = get_string('toggleslideshowdesc', 'theme_celoe');
    $yes = get_string('yes');
    $no = get_string('no');
    $default = 1;
    $choices = array(1 => $yes, 0 => $no);
    $setting = new admin_setting_configselect($name, $title, $description, $default, $choices);
    $temp->add($setting);

    // Number of slides.
    $name = 'theme_celoe/numberofslides';
    $title = get_string('numberofslides', 'theme_celoe');
    $description = get_string('numberofslides_desc', 'theme_celoe');
    $default = 1;
    $choices = array(
        1 => '1',
        2 => '2',
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
        7 => '7',
        8 => '8',
        9 => '9',
        10 => '10',
        11 => '11',
        12 => '12',
    );
    $temp->add(new admin_setting_configselect($name, $title, $description, $default, $choices));

    $numberofslides = get_config('theme_celoe', 'numberofslides');
    for ($i = 1; $i <= $numberofslides; $i++) {

        // This is the descriptor for Slide One.
        $name = 'theme_celoe/slide' . $i . 'info';
        $heading = get_string('slideno', 'theme_celoe', array('slide' => $i));
        $information = get_string('slidenodesc', 'theme_celoe', array('slide' => $i));
        $setting = new admin_setting_heading($name, $heading, $information);
        $temp->add($setting);

        // Slide Image.
        $name = 'theme_celoe/slide' . $i . 'image';
        $title = get_string('slideimage', 'theme_celoe');
        $description = get_string('slideimagedesc', 'theme_celoe');
        $setting = new admin_setting_configstoredfile($name, $title, $description, 'slide' . $i . 'image');
        $setting->set_updatedcallback('theme_reset_all_caches');
        $temp->add($setting);

        // Slide Caption.
        $name = 'theme_celoe/slide' . $i . 'caption';
        $title = get_string('slidecaption', 'theme_celoe');
        $description = get_string('slidecaptiondesc', 'theme_celoe');
        $default = 'lang:slidecaptiondefault';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $temp->add($setting);

        // Slider button.
        $name = 'theme_celoe/slide' . $i . 'urltext';
        $title = get_string('slidebutton', 'theme_celoe');
        $description = get_string('slidebuttondesc', 'theme_celoe');
        $default = 'lang:knowmore';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_TEXT);
        $temp->add($setting);

        // Slide button link.
        $name = 'theme_celoe/slide' . $i . 'url';
        $title = get_string('slidebuttonurl', 'theme_celoe');
        $description = get_string('slidebuttonurldesc', 'theme_celoe');
        $default = 'http://www.example.com/';
        $setting = new admin_setting_configtext($name, $title, $description, $default, PARAM_URL);
        $temp->add($setting);
    }
    $settings->add($temp);
    /* Slideshow Settings End*/

    /* Marketing Spots */
    $temp = new admin_settingpage('theme_celoe_marketingspots', get_string('marketingspotsheading', 'theme_celoe'));

    /* Marketing Spot 1*/
    $name = 'theme_celoe_mspot1heading';
    $heading = get_string('marketingspot', 'theme_celoe') . ' 1 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Marketing Spot 1 Icon.
    $name = 'theme_celoe/mspot1icon';
    $title = get_string('marketingspot', 'theme_celoe') . ' 1 - ' . get_string('icon', 'theme_celoe');
    $description = get_string('faicondesc', 'theme_celoe');
    $default = 'globe';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 1 Title.
    $name = 'theme_celoe/mspot1title';
    $title = get_string('marketingspot', 'theme_celoe') . ' 1 - ' . get_string('title', 'theme_celoe');
    $description = get_string('mspottitledesc', 'theme_celoe');
    $default = 'lang:mspot1titledefault';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 1 Description.
    $name = 'theme_celoe/mspot1desc';
    $title = get_string('marketingspot', 'theme_celoe') . ' 1 - ' . get_string('description');
    $description = get_string('mspotdescdesc', 'theme_celoe');
    $default = 'lang:mspot1descdefault';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $temp->add($setting);
    /* Marketing Spot 1*/

    /* Marketing Spot 2*/
    $name = 'theme_celoe_mspot2heading';
    $heading = get_string('marketingspot', 'theme_celoe') . ' 2 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Marketing Spot 2 Icon.
    $name = 'theme_celoe/mspot2icon';
    $title = get_string('marketingspot', 'theme_celoe') . ' 2 - ' . get_string('icon', 'theme_celoe');
    $description = get_string('faicondesc', 'theme_celoe');
    $default = 'graduation-cap';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 2 Title.
    $name = 'theme_celoe/mspot2title';
    $title = get_string('marketingspot', 'theme_celoe') . ' 2 - ' . get_string('title', 'theme_celoe');
    $description = get_string('mspottitledesc', 'theme_celoe');
    $default = 'lang:mspot2titledefault';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 2 Description.
    $name = 'theme_celoe/mspot2desc';
    $title = get_string('marketingspot', 'theme_celoe') . ' 2 - ' . get_string('description');
    $description = get_string('mspotdescdesc', 'theme_celoe');
    $default = 'lang:mspot2descdefault';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $temp->add($setting);
    /* Marketing Spot 2*/

    /* Marketing Spot 3*/
    $name = 'theme_celoe_mspot3heading';
    $heading = get_string('marketingspot', 'theme_celoe') . ' 3 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Marketing Spot 3 Icon.
    $name = 'theme_celoe/mspot3icon';
    $title = get_string('marketingspot', 'theme_celoe') . ' 3 - ' . get_string('icon', 'theme_celoe');
    $description = get_string('faicondesc', 'theme_celoe');
    $default = 'bullhorn';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 3 Title.
    $name = 'theme_celoe/mspot3title';
    $title = get_string('marketingspot', 'theme_celoe') . ' 3 - ' . get_string('title', 'theme_celoe');
    $description = get_string('mspottitledesc', 'theme_celoe');
    $default = 'lang:mspot3titledefault';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 3 Description.
    $name = 'theme_celoe/mspot3desc';
    $title = get_string('marketingspot', 'theme_celoe') . ' 3 - ' . get_string('description');
    $description = get_string('mspotdescdesc', 'theme_celoe');
    $default = 'lang:mspot3descdefault';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $temp->add($setting);
    /* Marketing Spot 3*/

    /* Marketing Spot 4*/
    $name = 'theme_celoe_mspot4heading';
    $heading = get_string('marketingspot', 'theme_celoe') . ' 4 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Marketing Spot 4 Icon.
    $name = 'theme_celoe/mspot4icon';
    $title = get_string('marketingspot', 'theme_celoe') . ' 4 - ' . get_string('icon', 'theme_celoe');
    $description = get_string('faicondesc', 'theme_celoe');
    $default = 'mobile';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 4 Title.
    $name = 'theme_celoe/mspot4title';
    $title = get_string('marketingspot', 'theme_celoe') . ' 4 - ' . get_string('title', 'theme_celoe');
    $description = get_string('mspottitledesc', 'theme_celoe');
    $default = 'lang:mspot4titledefault';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Marketing Spot 4 Description.
    $name = 'theme_celoe/mspot4desc';
    $title = get_string('marketingspot', 'theme_celoe') . ' 4 - ' . get_string('description');
    $description = get_string('mspotdescdesc', 'theme_celoe');
    $default = 'lang:mspot4descdefault';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default, PARAM_TEXT);
    $temp->add($setting);
    $settings->add($temp);
    /* Marketing Spot 4*/
    /* Marketing Spots End */

    /* Footer Settings start */
    $temp = new admin_settingpage('theme_celoe_footer', get_string('footerheading', 'theme_celoe'));

    // Footer Block1.
    $name = 'theme_celoe_footerblock1heading';
    $heading = get_string('footerblock', 'theme_celoe') . ' 1 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Enable / Disable Footer logo.
    $name = 'theme_celoe/footerblklogo';
    $title = get_string('footerblklogo', 'theme_celoe');
    $description = '';
    $default = 1;
    $setting = new admin_setting_configcheckbox($name, $title, $description, $default);
    $temp->add($setting);

    /* Footer Footnote Content */
    $name = 'theme_celoe/footnote';
    $title = get_string('footnote', 'theme_celoe');
    $description = get_string('footnotedesc', 'theme_celoe');
    $default = 'lang:footnotedefault';
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $temp->add($setting);
    /* Footer Block1. */

    /* Footer Block2. */
    $name = 'theme_celoe_footerblock2heading';
    $heading = get_string('footerblock', 'theme_celoe') . ' 2 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    $name = 'theme_celoe/footerbtitle2';
    $title = get_string('footerblock', 'theme_celoe') . ' ' . get_string('title', 'theme_celoe') . ' 2 ';
    $description = get_string('footerbtitle_desc', 'theme_celoe');
    $default = 'lang:footerbtitle2default';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_celoe/footerblink2';
    $title = get_string('footerblink', 'theme_celoe') . ' 2';
    $description = get_string('footerblink_desc', 'theme_celoe');
    $default = get_string('footerblink2default', 'theme_celoe');
    $setting = new admin_setting_configtextarea($name, $title, $description, $default);
    $temp->add($setting);
    /* Footer Block2 */

    /* Footer Block3 */
    $name = 'theme_celoe_footerblock3heading';
    $heading = get_string('footerblock', 'theme_celoe') . ' 3 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    $name = 'theme_celoe/footerbtitle3';
    $title = get_string('footerblock', 'theme_celoe') . ' ' . get_string('title', 'theme_celoe') . ' 3 ';
    $description = get_string('footerbtitle_desc', 'theme_celoe');
    $default = 'lang:footerbtitle3default';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    /* Facebook,Pinterest,Twitter,Google+ Settings */
    $name = 'theme_celoe/fburl';
    $title = get_string('fburl', 'theme_celoe');
    $description = get_string('fburldesc', 'theme_celoe');
    $default = get_string('fburl_default', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_celoe/twurl';
    $title = get_string('twurl', 'theme_celoe');
    $description = get_string('twurldesc', 'theme_celoe');
    $default = get_string('twurl_default', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_celoe/gpurl';
    $title = get_string('gpurl', 'theme_celoe');
    $description = get_string('gpurldesc', 'theme_celoe');
    $default = get_string('gpurl_default', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_celoe/pinurl';
    $title = get_string('pinurl', 'theme_celoe');
    $description = get_string('pinurldesc', 'theme_celoe');
    $default = get_string('pinurl_default', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);
    /* Footer Block3. */

    /* Footer Block4. */
    $name = 'theme_celoe_footerblock4heading';
    $heading = get_string('footerblock', 'theme_celoe') . ' 4 ';
    $information = '';
    $setting = new admin_setting_heading($name, $heading, $information);
    $temp->add($setting);

    // Fooer Block Title 4.
    $name = 'theme_celoe/footerbtitle4';
    $title = get_string('footerblock', 'theme_celoe') . ' ' . get_string('title', 'theme_celoe') . ' 4 ';
    $description = get_string('footerbtitle_desc', 'theme_celoe');
    $default = 'lang:footerbtitle4default';
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    /* Address , Phone No ,Email */
    $name = 'theme_celoe/address';
    $title = get_string('address', 'theme_celoe');
    $description = '';
    $default = get_string('defaultaddress', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_celoe/phoneno';
    $title = get_string('phoneno', 'theme_celoe');
    $description = '';
    $default = get_string('defaultphoneno', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $name = 'theme_celoe/emailid';
    $title = get_string('emailid', 'theme_celoe');
    $description = '';
    $default = get_string('defaultemailid', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    // Copyright.
    $name = 'theme_celoe/copyright';
    $title = get_string('copyright', 'theme_celoe');
    $description = '';
    $default = get_string('copyright_default', 'theme_celoe');
    $setting = new admin_setting_configtext($name, $title, $description, $default);
    $temp->add($setting);

    $settings->add($temp);
    /* Footer Block4 */
    /*  Footer Settings end */
}
