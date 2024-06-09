<?php
/**
 * HTML to generate collapsible lists for Materialize.css
 * Official documentation: https://materializecss.com/collapsible.html
 * If the items can't be unfolded, ensure that you have initialized the Materlialize's
 * collapsible module in javascript (see the documentation above)
 */
class HtmlCollapsible
{    
    
    /**
     * Call this method to generate the full list of collapsible items
     * 
     * @param array $items The list of items. Each item is structured as a subarray:
     *                     [
     *                      'icon'  => '',
     *                      'title' => '',
     *                      'text'  => ''
     *                     ]
     * @return string HTML
     */
    public function items($items) {
        
        $html_items = '';
        foreach($items as $item) {
            $html_items .= $this->item($item['icon'], $item['title'], $item['text']);
        }
        
        return '
            <ul class="collapsible">
                '.$html_items.'
            </ul>';
    }
    
    
    /**
     * Generate one collapsible element
     * 
     * @param string $icon The icon (HTML entity or Materialize icon)
     * @param string $title The title of the element when folded
     * @param string $text The text of the element displayed when unfolded
     * @return string HTML
     */
    private function item($icon, $title, $text) {
        
        return '
            <li>
                <div class="collapsible-header">
                    '.$icon.' <strong>'.$title.'</strong>
                    <i class="material-icons">chevron_right</i>
                </div>
                <div class="collapsible-body">
                    '.$text.'
                </div>
            </li>';
    }
}
