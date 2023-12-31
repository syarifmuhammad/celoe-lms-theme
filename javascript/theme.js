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
 *
 * @package     theme_celoe
 * @copyright   2023 CeLoe Dev Team, celoe.ittelkom-sby.ac.id
 * @author      CeLoe Dev Team
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 *
 */

(function($) {
    var img = $("#header").find('.avatar').find('img[src$="/u/f2"]');
    var src = img.attr('src');
    img.attr('src', src + '_white');

    /*  ------- Check navbar button status -------- */
    if ($("#header .navbar button").attr('aria-expanded') === "true") {
        $("#header .navbar").find('button').addClass('is-active');
        $("#btn-burger").addClass('is-active');
    }

    /*  ------ Event for change the drawer navbar style  ------ */
    $("#header .navbar button").click(function() {
        var $this = $(this);
        setTimeout(function() {
            if ($this.attr('aria-expanded') == "true") {
                $("#header .navbar").find('button').addClass('is-active');
                $("#btn-burger").addClass('is-active');
                $("#btn-burger").attr('aria-expanded', false);
            } else {
                $("#header .navbar").find('button').removeClass('is-active');
                $("#btn-burger").removeClass('is-active');
                $("#btn-burger").attr('aria-expanded', true);
            }
        }, 200);
    });

    $("#btn-burger").click(function() {
        var $this = $(this);
        setTimeout(function() {
            $("#header .navbar button").click()
        }, 200);
    });
})(jQuery);