<?php
/**
 * Generate tags for the items.
 */
class HtmlTags {
    
    public function __construct() {
        
        // Temporarily hardcoding the names of the tags. Should replace this 
        // by an API returning the names of the tags.
        $this->tags_names = [
            1 => 'boost',
            2 => 'nourriture',
            3 => 'drogue',
            4 => 'alcool',
            5 => 'ennemi',
        ];
    }
    
    
    /**
     * Display the tags affected to a given item.
     * 
     * @param array $items_caracs The characteristics of the item, as returned 
     *                            by the Azimutant's API.
     * @param string $format Set to "html" or "text" to format the tag name 
     *                       in HTML or raw text.
     * @return string HTML
     */
    public function tags_item($items_caracs, $format='html')
    {
        
        $separator = ' ';
        $result = '';
        
        if(isset($items_caracs['tags'])) {
            foreach($items_caracs['tags'] as $tag_id) {
                $result .= $this->get_tag($this->tags_names[$tag_id], $format).$separator;
            }
        }
        
        return $result;
    }
    
    
    /**
     * Display all the tags existing in the game.
     * 
     * @param string $format Set to "html" or "text" to format the tag name 
     *                       in HTML or raw text.
     * @return string HTML
     */
    public function tags_all($format='html')
    {
        
        $separator = ' ';
        $result = '';
        
        foreach($this->tags_names as $tag_name) {
            $result .= $this->get_tag($tag_name, $format).$separator;
        }
        
        return $result;
    }
    
    
    /**
     * Generate one tag in HTML or raw text.
     * 
     * @param string $tag_name The name of the tag (ex: "boost").
     * @param string $format Set to "html" or "text" to format the tag name 
     *                     in HTML or raw text.
     * @return string
     */
    private function get_tag($tag_name, $format='html') {
        
        $result = false;
        
        if($format === 'html') {
            $result = '<div class="chip">'.$tag_name.'</div>';
        }
        elseif($format === 'text') {
            $result = $tag_name;
        }
        
        return $result;
    }
}
