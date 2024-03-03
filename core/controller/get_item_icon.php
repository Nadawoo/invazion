<?php
/**
 * Gives the image (PNG, JPG...) of an item or construction, or its textual icon 
 * (HTML entity or emoji), or a default icon.
 * 
 * @param string $icon_path The path to the image icon, from /resources/img/
 *                          Ex: "copyrighted/buildings/104.png"
 * @param string $icon_html The HTML entity or emoji of the icon
 *                          Ex : "&#127751;"
 * @param int $icon_size The dimension of the image in px, ex: "32"
 *                       Only one number because height = width.
 * @return string
 */
function get_item_icon($icon_path, $icon_html, $icon_size=null) {
    
    $icon_size = ($icon_size !== null) ? $icon_size : 48;
    
    if($icon_path !== null and $icon_path !== '') {
        return  '<img src="../resources/img/'.$icon_path.'" class="item_icon" '
                . 'height="'.$icon_size.'" width="'.$icon_size.'" alt="">';
    }
    elseif($icon_html !== null and $icon_html !== '') {
        return $icon_html;
    }
    else {
        // The "?" emoji
        return '&#10067;';
    }
}
