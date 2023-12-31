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
 * lib.php
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
 * Page init functions runs every time page loads.
 * @param moodle_page $page
 * @return null
 */
function theme_celoe_page_init(moodle_page $page)
{
    $page->requires->jquery();
    $page->requires->js('/theme/celoe/javascript/theme.js');
}

/**
 * Loads the CSS Styles and replace the background images.
 * If background image not available in the settings take the default images.
 *
 * @param string $css
 * @param string $theme
 * @return string $css
 */
function theme_celoe_process_css($css, $theme)
{
    // Set the background image for the logo.
    $logo = $theme->setting_file_url('logo', 'logo');

    // Set custom CSS.
    if (!empty($theme->settings->customcss)) {
        $customcss = $theme->settings->customcss;
    } else {
        $customcss = null;
    }
    //$css = theme_celoe_pre_css_set_fontwww($css);
    $css = theme_celoe_set_fontwww($css);
    $css = theme_celoe_get_pattern_color($css, $theme);
    $css = theme_celoe_set_customcss($css, $customcss);

    return $css;
}

/**
 * Serves any files associated with the theme settings.
 *
 * @param stdClass $course
 * @param stdClass $cm
 * @param context $context
 * @param string $filearea
 * @param array $args
 * @param bool $forcedownload
 * @param array $options
 * @return bool
 */
function theme_celoe_pluginfile($course, $cm, $context, $filearea, $args, $forcedownload, array $options = array())
{
    static $theme;

    if (empty($theme)) {
        $theme = theme_config::load('celoe');
    }
    if ($context->contextlevel == CONTEXT_SYSTEM) {
        if ($filearea === 'logo') {
            return $theme->setting_file_serve('logo', $args, $forcedownload, $options);
        } else if ($filearea === 'footerlogo') {
            return $theme->setting_file_serve('footerlogo', $args, $forcedownload, $options);
        } else if ($filearea === 'style') {
            theme_celoe_serve_css($args[1]);
        } else if ($filearea === 'pagebackground') {
            return $theme->setting_file_serve('pagebackground', $args, $forcedownload, $options);
        } else if (preg_match("/slide[1-9][0-9]*image/", $filearea) !== false) {
            return $theme->setting_file_serve($filearea, $args, $forcedownload, $options);
        } else {
            send_file_not_found();
        }
    } else {
        send_file_not_found();
    }
}

/**
 * Serves CSS for image file updated to styles.
 *
 * @param string $filename
 * @return string
 */
function theme_celoe_serve_css($filename)
{
    global $CFG;
    if (!empty($CFG->themedir)) {
        $thestylepath = $CFG->themedir . '/celoe/style/';
    } else {
        $thestylepath = $CFG->dirroot . '/theme/celoe/style/';
    }
    $thesheet = $thestylepath . $filename;

    /* http://css-tricks.com/snippets/php/intelligent-php-cache-control/ - rather than /lib/csslib.php as it is a static file who's
      contents should only change if it is rebuilt.  But! There should be no difference with TDM on so will see for the moment if
      that decision is a factor. */

    $etagfile = md5_file($thesheet);
    // File.
    $lastmodified = filemtime($thesheet);
    // Header.
    $ifmodifiedsince = (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : false);
    $etagheader = (isset($_SERVER['HTTP_IF_NONE_MATCH']) ? trim($_SERVER['HTTP_IF_NONE_MATCH']) : false);

    if ((($ifmodifiedsince) && (strtotime($ifmodifiedsince) == $lastmodified)) || $etagheader == $etagfile) {
        theme_celoe_send_unmodified($lastmodified, $etagfile);
    }
    theme_celoe_send_cached_css($thestylepath, $filename, $lastmodified, $etagfile);
}

/**
 * Set browser cache used in php header.
 * @param string $lastmodified
 * @param string $etag
 *
 */
function theme_celoe_send_unmodified($lastmodified, $etag)
{
    $lifetime = 60 * 60 * 24 * 60;
    header('HTTP/1.1 304 Not Modified');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Content-Type: text/css; charset=utf-8');
    header('Etag: "' . $etag . '"');
    if ($lastmodified) {
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    }
    die;
}

/**
 * Cached css.
 * @param string $path
 * @param string $filename
 * @param integer $lastmodified
 * @param string $etag
 */
function theme_celoe_send_cached_css($path, $filename, $lastmodified, $etag)
{
    global $CFG;
    require_once($CFG->dirroot . '/lib/configonlylib.php');
    // For min_enable_zlib_compression.
    // 60 days only - the revision may get incremented quite often.
    $lifetime = 60 * 60 * 24 * 60;

    header('Etag: "' . $etag . '"');
    header('Content-Disposition: inline; filename="' . $filename . '"');
    header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastmodified) . ' GMT');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $lifetime) . ' GMT');
    header('Pragma: ');
    header('Cache-Control: public, max-age=' . $lifetime);
    header('Accept-Ranges: none');
    header('Content-Type: text/css; charset=utf-8');
    if (!min_enable_zlib_compression()) {
        header('Content-Length: ' . filesize($path . $filename));
    }

    readfile($path . $filename);
    die;
}

/**
 * Adds any custom CSS to the CSS before it is cached.
 *
 * @param string $css The original CSS.
 * @param string $customcss The custom CSS to add.
 * @return string The CSS which now contains our custom CSS.
 */
function theme_celoe_set_customcss($css, $customcss)
{
    $tag = '[[setting:customcss]]';
    $replacement = $customcss;
    if (is_null($replacement)) {
        $replacement = '';
    }

    $css = str_replace($tag, $replacement, $css);
    return $css;
}

/**
 * Returns an object containing HTML for the areas affected by settings.
 *
 * Do not add Clean specific logic in here, child themes should be able to
 * rely on that function just by declaring settings with similar names.
 *
 * @param renderer_base $output Pass in $OUTPUT.
 * @param moodle_page $page Pass in $PAGE.
 * @return stdClass An object with the following properties:
 *      - navbarclass A CSS class to use on the navbar. By default ''.
 *      - heading HTML to use for the heading. A logo if one is selected or the default heading.
 *      - footnote HTML to use as a footnote. By default ''.
 */
function theme_celoe_get_html_for_settings(renderer_base $output, moodle_page $page)
{
    global $CFG;
    $return = new stdClass;

    $return->navbarclass = '';
    if (!empty($page->theme->settings->invert)) {
        $return->navbarclass .= ' navbar-inverse';
    }

    if (!empty($page->theme->settings->logo)) {
        $return->heading = html_writer::link($CFG->wwwroot, '', array('title' => get_string('home'), 'class' => 'logo'));
    } else {
        $return->heading = $output->page_heading();
    }

    $return->footnote = '';
    if (!empty($page->theme->settings->footnote)) {
        $return->footnote = '<div class="footnote text-center">' . format_text($page->theme->settings->footnote) . '</div>';
    }

    return $return;
}

/**
 * Loads the CSS Styles and put the font path
 *
 * @return string $fontwww
 */
/*function theme_celoe_set_fontwww() {
    global $CFG, $PAGE;

    $themewww = $CFG->wwwroot."/theme";
    $theme = theme_config::load('celoe');
    $fontwww = '$fontwww: "'. $themewww.'/celoe/fonts/"'.";\n";
    return $fontwww;
}*/
function theme_celoe_set_fontwww($css)
{
    global $CFG, $PAGE;
    if (empty($CFG->themewww)) {
        $themewww = $CFG->wwwroot . "/theme";
    } else {
        $themewww = $CFG->themewww;
    }

    $tag = '[[setting:fontwww]]';
    $theme = theme_config::load('celoe');
    $css = str_replace($tag, $themewww . '/celoe/fonts/', $css);
    return $css;
}


/**
 * Logo Image URL Fetch from theme settings
 *
 * @param string $type
 * @return image $logo
 */
function theme_celoe_get_logo_url($type = 'header')
{
    global $OUTPUT;
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('celoe');
    }

    if ($type == "header") {
        $logo = $theme->setting_file_url('logo', 'logo');
        $logo = empty($logo) ? $OUTPUT->image_url('home/logo', 'theme') : $logo;
    } else if ($type == "footer") {
        $logo = $theme->setting_file_url('footerlogo', 'footerlogo');
        $logo = empty($logo) ? $OUTPUT->image_url('home/footerlogo', 'theme') : $logo;
    }
    return $logo;
}

/**
 * Renderer the slider images.
 * @param integer $p
 * @param string $sliname
 * @return null
 */
function theme_celoe_render_slideimg($p, $sliname)
{
    global $PAGE, $OUTPUT;

    $nos = theme_celoe_get_setting('numberofslides');
    $i = $p % 3;
    $slideimage = $OUTPUT->image_url('home/slide' . $i, 'theme');

    // Get slide image or fallback to default.
    if (theme_celoe_get_setting($sliname)) {
        $slideimage = $PAGE->theme->setting_file_url($sliname, $sliname);
    }
    return $slideimage;
}

/**
 * Functions helps to get the admin config values which are related to the
 * theme
 * @param array $setting
 * @param bool $format
 * @return bool
 */
function theme_celoe_get_setting($setting, $format = true)
{
    global $CFG;
    require_once($CFG->dirroot . '/lib/weblib.php');
    static $theme;
    if (empty($theme)) {
        $theme = theme_config::load('celoe');
    }
    if (empty($theme->settings->$setting)) {
        return false;
    } else if (!$format) {
        return $theme->settings->$setting;
    } else if ($format === 'format_text') {
        return format_text($theme->settings->$setting, FORMAT_PLAIN);
    } else if ($format === 'format_html') {
        return format_text($theme->settings->$setting, FORMAT_HTML, array('trusted' => true, 'noclean' => true));
    } else {
        return format_string($theme->settings->$setting);
    }
}

/**
 * Return the current theme url
 *
 * @return string
 */
function theme_celoe_theme_url()
{
    global $CFG, $PAGE;
    $themeurl = $CFG->wwwroot . '/theme/' . $PAGE->theme->name;
    return $themeurl;
}

/**
 * Display Footer Block Custom Links
 * @param string $menuname Footer block link name.
 * @return string The Footer links are return.
 */
function theme_celoe_generate_links($menuname = '')
{
    global $CFG, $PAGE;
    $htmlstr = '';
    $menustr = theme_celoe_get_setting($menuname);
    $menusettings = explode("\n", $menustr);
    foreach ($menusettings as $menukey => $menuval) {
        $expset = explode("|", $menuval);
        if (!empty($expset) && isset($expset[0]) && isset($expset[1])) {
            list($ltxt, $lurl) = $expset;
            $ltxt = trim($ltxt);
            $ltxt = theme_celoe_lang($ltxt);
            $lurl = trim($lurl);
            if (empty($ltxt)) {
                continue;
            }
            if (empty($lurl)) {
                $lurl = 'javascript:void(0);';
            }

            $pos = strpos($lurl, 'http');
            if ($pos === false) {
                $lurl = new moodle_url($lurl);
            }
            $icon = '<svg width="7" height="10" viewBox="0 0 10 13" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M9.4146 6.63175L2.37002 0.831055L0.712891 2.19581L6.10385 6.63561L0.712891 11.0687L2.37002 12.4334L9.4146 6.63175Z" fill="white"/>
            </svg>
            ';
            $htmlstr .= '<li><a href="' . $lurl . '">'. $icon . $ltxt . '</a></li>' . "\n";
        }
    }
    return $htmlstr;
}

/**
 * Fetch the hide course ids
 *
 * @return array
 */
function theme_celoe_hidden_courses_ids()
{
    global $DB;
    $hcourseids = array();
    $result = $DB->get_records_sql("SELECT id FROM {course} WHERE visible='0' ");
    if (!empty($result)) {
        foreach ($result as $row) {
            $hcourseids[] = $row->id;
        }
    }
    return $hcourseids;
}

/**
 * Remove the html special tags from course content.
 * This function used in course home page.
 *
 * @param string $text
 * @return string
 */
function theme_celoe_strip_html_tags($text)
{
    $text = preg_replace(
        array(
            // Remove invisible content.
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<object[^>]*?.*?</object>@siu',
            '@<embed[^>]*?.*?</embed>@siu',
            '@<applet[^>]*?.*?</applet>@siu',
            '@<noframes[^>]*?.*?</noframes>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu',
            '@<noembed[^>]*?.*?</noembed>@siu',
            // Add line breaks before and after blocks.
            '@</?((address)|(blockquote)|(center)|(del))@iu',
            '@</?((div)|(h[1-9])|(ins)|(isindex)|(p)|(pre))@iu',
            '@</?((dir)|(dl)|(dt)|(dd)|(li)|(menu)|(ol)|(ul))@iu',
            '@</?((table)|(th)|(td)|(caption))@iu',
            '@</?((form)|(button)|(fieldset)|(legend)|(input))@iu',
            '@</?((label)|(select)|(optgroup)|(option)|(textarea))@iu',
            '@</?((frameset)|(frame)|(iframe))@iu',
        ),
        array(
            ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ', ' ',
            "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0", "\n\$0",
            "\n\$0", "\n\$0",
        ),
        $text
    );
    return strip_tags($text);
}

/**
 * Cut the Course content.
 *
 * @param string $str
 * @param integer $n
 * @param char $end_char
 * @return string $out
 */
function theme_celoe_course_trim_char($str, $n = 500, $endchar = '&#8230;')
{
    if (strlen($str) < $n) {
        return $str;
    }

    $str = preg_replace("/\s+/", ' ', str_replace(array("\r\n", "\r", "\n"), ' ', $str));
    if (strlen($str) <= $n) {
        return $str;
    }

    $out = "";
    $small = substr($str, 0, $n);
    $out = $small . $endchar;
    return $out;
}

/**
 * Returns the language values from the given lang string or key.
 * @param string $key
 * @return string
 */
function theme_celoe_lang($key = '')
{
    $pos = strpos($key, 'lang:');
    if ($pos !== false) {
        list($l, $k) = explode(":", $key);
        if (get_string_manager()->string_exists($k, 'theme_celoe')) {
            $v = get_string($k, 'theme_celoe');
            return $v;
        } else {
            return $key;
        }
    } else {
        return $key;
    }
}

function theme_celoe_get_pattern_color($css, $type = '')
{
    global $OUTPUT;

    $rtl  = (right_to_left()) ? '_rtl' : '';

    $patterncolors = array(
        'default' => array(
            'color_primary' => '#AA0000',
            'color_secondary' => '#ffffff',
            'color_blackcurrant_approx' => '#5e1e15',
            'color_plum_approx' => '#70271e',
            'color_blackcurrant_90_approx' => 'rgba(90, 30, 21, .9)',
            'color_french_lilac_approx' => '#dec4c1',
            'color_snuff_approx' => '#f7e3e1',
            'color_tutu_approx' => '#fff1ef',
            'color_blackcurrant_25_approx' => 'rgba(90, 30, 21, .25)',
            'collapsed_empty' => $OUTPUT->image_url('default/t/collapsed_empty', 'theme'),
            'collapsed' => $OUTPUT->image_url('default/t/collapsed', 'theme'),
            'collapsed_rtl' => $OUTPUT->image_url('default/t/collapsed_rtl', 'theme'),
            'expanded' => $OUTPUT->image_url('default/t/expanded', 'theme')
        ),

        '1' => array(
            'color_primary' => '#426e17',
            'color_secondary' => '#7abb3b',
            'color_blackcurrant_approx' => '#2f510f',
            'color_plum_approx' => '#528125',
            'color_blackcurrant_90_approx' => 'rgba(47, 81, 15, .9)',
            'color_french_lilac_approx' => '#cedec0',
            'color_snuff_approx' => '#bad3a3',
            'color_tutu_approx' => '#f2fde8',
            'color_blackcurrant_25_approx' => 'rgba(47, 81, 15, .25)',
            'collapsed_empty' => $OUTPUT->image_url('cs01/t/collapsed_empty', 'theme'),
            'collapsed' => $OUTPUT->image_url('cs01/t/collapsed', 'theme'),
            'collapsed_rtl' => $OUTPUT->image_url('cs01/t/collapsed_rtl', 'theme'),
            'expanded' => $OUTPUT->image_url('cs01/t/expanded', 'theme')
        ),

        '2' => array(
            'color_primary' => '#2b4e84',
            'color_secondary' => '#3e65a0',
            'color_blackcurrant_approx' => '#183054',
            'color_plum_approx' => '#3b5f96',
            'color_blackcurrant_90_approx' => 'rgba(24, 48, 84, .9)',
            'color_french_lilac_approx' => '#ccd8e8',
            'color_snuff_approx' => '#c0ccdc',
            'color_tutu_approx' => '#e8f0fb',
            'color_blackcurrant_25_approx' => 'rgba(24, 48, 84, .25)',
            'collapsed_empty' => $OUTPUT->image_url('cs02/t/collapsed_empty', 'theme'),
            'collapsed' => $OUTPUT->image_url('cs02/t/collapsed', 'theme'),
            'collapsed_rtl' => $OUTPUT->image_url('cs02/t/collapsed_rtl', 'theme'),
            'expanded' => $OUTPUT->image_url('cs02/t/expanded', 'theme')
        ),

        '3' => array(
            'color_primary' => '#561209',
            'color_secondary' => '#a64437',
            'color_blackcurrant_approx' => '#5e1e15',
            'color_plum_approx' => '#70271e',
            'color_blackcurrant_90_approx' => 'rgba(90, 30, 21, .9)',
            'color_french_lilac_approx' => '#dec4c1',
            'color_snuff_approx' => '#f7e3e1',
            'color_tutu_approx' => '#fff1ef',
            'color_blackcurrant_25_approx' => 'rgba(90, 30, 21, .25)',
            'collapsed_empty' => $OUTPUT->image_url('cs03/t/collapsed_empty', 'theme'),
            'collapsed' => $OUTPUT->image_url('cs03/t/collapsed', 'theme'),
            'collapsed_rtl' => $OUTPUT->image_url('cs03/t/collapsed_rtl', 'theme'),
            'expanded' => $OUTPUT->image_url('cs03/t/expanded', 'theme')
        ),

        '4' => array(
            'color_primary' => '#20897b',
            'color_secondary' => '#4ba89c',
            'color_blackcurrant_approx' => '#103430',
            'color_plum_approx' => '#17786b',
            'color_blackcurrant_90_approx' => 'rgba(16, 52, 48, .9)',
            'color_french_lilac_approx' => '#c2e8e5',
            'color_snuff_approx' => '#c0dcdb',
            'color_tutu_approx' => '#e4f7f6',
            'color_blackcurrant_25_approx' => 'rgba(16, 52, 48, .25)',
            'collapsed_empty' => $OUTPUT->image_url('cs04/t/collapsed_empty', 'theme'),
            'collapsed' => $OUTPUT->image_url('cs04/t/collapsed', 'theme'),
            'collapsed_rtl' => $OUTPUT->image_url('cs04/t/collapsed_rtl', 'theme'),
            'expanded' => $OUTPUT->image_url('cs04/t/expanded', 'theme')
        ),
        '5' => array(
            'color_primary' => '#8e558e',
            'color_secondary' => '#a55ba5',
            'color_blackcurrant_approx' => '#382738',
            'color_plum_approx' => '#764076',
            'color_blackcurrant_90_approx' => 'rgba(56, 39, 56, 0.9)',
            'color_french_lilac_approx' => '#ead1ea',
            'color_snuff_approx' => '#edd3ed',
            'color_tutu_approx' => '#fef',
            'color_blackcurrant_25_approx' => 'rgba(56, 39, 56, .25)',
            'collapsed_empty' => $OUTPUT->image_url('cs05/t/collapsed_empty', 'theme'),
            'collapsed' => $OUTPUT->image_url('cs05/t/collapsed', 'theme'),
            'collapsed_rtl' => $OUTPUT->image_url('cs05/t/collapsed_rtl', 'theme'),
            'expanded' => $OUTPUT->image_url('cs05/t/expanded', 'theme')
        ),
    );

    $selectedpattern = theme_celoe_get_setting('patternselect');
    foreach ($patterncolors[$selectedpattern] as $key => $value) {
        $tag = '[[' . $key . ']]';
        $replacement = $value;
        $css = str_replace($tag, $replacement, $css);
    }
    return $css;
}



/**
 * Function returns the rgb format with the combination of passed color hex and opacity.
 * @param type|string $hexa
 * @param type|int $opacity
 * @return type|string
 */
function theme_celoe_get_hexa($hexa, $opacity)
{
    if (!empty($hexa)) {
        list($r, $g, $b) = sscanf($hexa, "#%02x%02x%02x");
        if ($opacity == '') {
            $opacity = 0.0;
        } else {
            $opacity = $opacity / 10;
        }
        return "rgba($r, $g, $b, $opacity)";
    }
}
