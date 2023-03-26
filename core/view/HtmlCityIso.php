<?php
/**
 * HTML elements to build an isometric representation of the city interior
 */
class HtmlCityIso {
    
    // The size of the assets is fixed by HTML (<img height=... width=...>) 
    const IMAGE_DEFAULT_SIZE  = 256;
    // The buildings are positioned in CSS with a position:absolute
    const IMAGE_POSITION_TOP  = -6;
    const IMAGE_POSITION_LEFT = -4;
    
    // Define here the characteristics of the images you want to place 
    // on the isometric view of the city. The key MUST be named the same as
    // an HTML id in the block #city_contents (otherwise the JS onclick() will fail).
    // Sample structure for each item of the array:
    //'resources' => [
    //    'name'  => 'Ressources',
    //    'image' => 'copyrighted/items/scrap.png',
    //    'image_size' => 170,
    //    'image_position_top'  => -1,
    //    'image_position_left' => 4,
    //    'action_blocks' => [['name'=>'Inexploré', 'amount'=>100],
    //                        ['name'=>'Exploré', 'amount'=>0],
    //                        ['name'=>'Épuisé', 'amount'=>0]
    //                        ]
    private $cityIsoBlocks = [
        'city_fellows' => [
            'name'  => 'Habitants',
            'image' => 'copyrighted/buildings/Ho-042.png',
            'action_blocks' => [['name'=>'En ville', 'amount'=>1],
                                ['name'=>'Hors ville', 'amount'=>1],
                                ['name'=>'Morts', 'amount'=>0]
                                ]
        ],
        'city_constructions' => [
            'name'  => 'Chantiers',
            'image' => 'copyrighted/buildings/Ho-026.png',
            'action_blocks' => [['name'=>'Disponibles', 'amount'=>3],
                                ['name'=>'Achevés', 'amount'=>0]
                                ]
        ],
        'city_defenses' => [
            'name'  => 'Défenses',
            'image' => 'copyrighted/buildings/Ho-163.png',
            'image_size' => 380,
            'image_position_top'  => -10,
            'image_position_left' => -3.9,
            'action_blocks' => [['name'=>'Défenses', 'amount'=>0],
                                ['name'=>'Manque', 'amount'=>-47]
                                ]
        ],
        'discuss' => [
            'name'  => 'Communications',
            'image' => 'copyrighted/buildings/Ho-052.png',
            'action_blocks' => [['name'=>'À lire', 'amount'=>0]]
        ],
        'city_door' => [
            'name'  => 'Grande porte',
            'image' => 'copyrighted/buildings/Ho-164.png',
            'image_size' => 300,
            'image_position_top'  => -5,
            'image_position_left' => -1,
            'action_blocks' => [['name'=>'État', 'amount'=>'&#128275;']]
        ],
        'my_home' => [
            'name'  => 'Chez moi',
            'image' => 'copyrighted/buildings/Ho-161.png',
            'action_blocks' => [['name'=>'Défenses', 'amount'=>0],
                                ['name'=>'Décoration', 'amount'=>0]
                                ]
        ],        
        'explore' => [
            'name'  => 'Ressources',
            'image' => 'copyrighted/items/scrap.png',
            'image_size' => 200,
            'image_position_top'  => 0,
            'image_position_left' => 0,
            'action_blocks' => [['name'=>'Inexploré', 'amount'=>100],
                                ['name'=>'Exploré', 'amount'=>0],
                                ['name'=>'Épuisé', 'amount'=>0]
                                ]
        ],
        'city_storage' => [
            'name'  => 'Dépôt',
            'image' => 'copyrighted/buildings/Ho-046.png',
            'action_blocks' => [['name'=>'Objets', 'amount'=>0]]
        ],
        'city_well' => [
            'name'  => 'Puits',
            'image' => 'copyrighted/buildings/Ho-043.png',
            'action_blocks' => [['name'=>'Eau', 'amount'=>0]]
        ],
        'city_workshop' => [
            'name'  => 'Atelier',
            'image' => 'copyrighted/buildings/Ho-231.png',
            'action_blocks' => [['name'=>'Composants', 'amount'=>0]]
        ],
        'zombies' => [
            'name'  => 'La horde !',
            'image' => 'motiontwin/zombie9.gif',
            'image_size' => 180,
            'image_position_top'  => 0,
            'image_position_left' => -4,
            'action_blocks' => [['name'=>'Zombies', 'amount'=>47],
                                ['name'=>'Attaque dans', 'amount'=>'12h']
                                ]
        ],
    ];
    
    
    function __construct() {

        $this->imageSize         = self::IMAGE_DEFAULT_SIZE;
        $this->imagePositionTop  = self::IMAGE_POSITION_TOP;
        $this->imagePositionLeft = self::IMAGE_POSITION_LEFT;
        $this->actionBlocks = '';
    }

    
    /**
     * Main function. Generates the complete HTML of the isometric city map. 
     * 
     * @return string HTML
     */
    public function city() {
        
        return '
            <table>
                <tr>
                    <td>'.$this->building('city_storage').'</td>
                    <td>'.$this->building('city_fellows').'</td>
                    <td>'.$this->building('my_home').'</td>
                    <td>'.$this->wall(4, 'right').'</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>'.$this->building('city_well').'</td>
                    <td>'.$this->building('discuss').'</td>
                    <td></td>
                    <td>'.$this->building('city_door').'</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>'.$this->building('city_workshop').'</td>
                    <td>'.$this->building('city_constructions').'</td>
                    <td></td>
                    <td>'.$this->wall(7, 'right').'</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>'.$this->wall(4, 'left').'</td>
                    <td>'.$this->wall(4, 'left').'</td>
                    <td>'.$this->wall(7, 'left').'</td>
                    <td>'.$this->building('city_defenses').'</td>
                    <td>'.$this->building('explore').'</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>'.$this->building('zombies').'</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>';
    }
    
    
    /**
     * The horizontal bar showing the main resources of the city
     * 
     * @return string HTML
     */
    public function resources_bar() {
        
        return '
            <ul class="resources_bar">
                <li onclick="switchCitySubmenu(\'city_defenses\');hide(\'city_iso\')"
                    title="Défenses de la ville contre l\'attaque du soir">
                    <div class="icon">&#128737;&#65039;</div> <div class="amount">?</div>
                </li>
                <li onclick="switchCitySubmenu(\'city_storage\');hide(\'city_iso\')"
                    title="Objets dans le dépôt de la ville">
                    <div class="icon" style="bottom:0.25rem">&#129520;</div> <div class="amount">?</div>
                </li>
                <li onclick="switchCitySubmenu(\'city_well\');hide(\'city_iso\')"
                    title="Rations d\'eau dans le puits de la ville">
                    <div class="icon">&#128167;</div> <div class="amount">?</div>
                </li>
                <li onclick="switchCitySubmenu(\'explore\');hide(\'city_iso\')">
                    <div style="font-size:1.5rem">&#10060;</div>
                </li>
            </ul>';
    }
    
    
    /**
     * Call this method (facultative)  before the asset() method if you want to set 
     * an image size different from the default value.
     * 
     * @param int $size The size in pixels. Will be used both for width ant height
     * @return null
     */
    private function setImageSize($size) {
        
        $this->imageSize = $size;
    }
    
    
    /**
     * Call this method (facultative)  before the asset() method if you want to set 
     * a position for the image different from the default value.
     * 
     * @param int $top The CSS property "top:", in em unit. For example, "-6" means:
     *                 position:absolute; top:-6em;
     * @param int $left Idem but for the "left:" property
     * @return null
     */
    private function setImagePosition($top, $left) {
        
        $this->imagePositionTop = $top;
        $this->imagePositionLeft = $left;
    }
    
    
    /**
     * Call this method (facultative) before the asset() method to add action blocks
     * over the image of the building.
     * 
     * @param array $actionblocks An array containing the data of each action block.
     *                            Each action block is itself a sub-array.
     *                            Set to null id the building doesn't need any action block.
     *                  [
     *                      ['name'=>'Defenses', 'amount'=>35],
     *                      ['name'=>'Decoration', 'amount'=>17],
     *                      ...
     *                  ]
     * @return null The HTML generated will be visible by calling the asset() method 
     */
    private function setActionBlock($actionblocks) {

        // Resets the eventual blocks previously binded to another asset 
        $this->actionBlocks = '';

        // If the asset has no action block, we still need to reset the previous binding
        if($actionblocks === null) {
            return;
        }

        foreach($actionblocks as $actionblock) {                
            $this->actionBlocks .= $this->assetActionBlocks($actionblock['name'], $actionblock['amount']);
        }
    }
    
    
    /**
     * Puts a building on the isomtric city map.
     * 
     * @param string $alias The alias of the building to place. Must exist 
     *                      in the class property $cityIsoBlocks (see above)
     * @return string HTML
     */
    private function building($alias) {
        
        $asset = $this->cityIsoBlocks[$alias];
        
        $this->setActionBlock($asset['action_blocks']);
        
        if(isset($asset['image_size'])) {
            $this->setImageSize($asset['image_size']);
        }
        
        if(isset($asset['image_position_top'])) {
            $this->setImagePosition($asset['image_position_top'], $asset['image_position_left']);
        }
        
        return $this->asset($alias, $asset['name'], $asset['image']);
    }
    
    
    /**
     * Draws a wall around the city enclosure
     * 
     * @param int $length The number of portion of the desired wall
     * @param string $side 'left' or 'right' to change the orientation of the wall
     * @return string HTML
     */
    private function wall($length, $side) {

        $wall_size_px = 200; // Dimensions of the picture
        $top  = -9.5; // Initial position of the wall
        $left = -10;
        $add_px_top  = 1.2; // Shift the wall for each new portion
        $add_px_left = 2.6;

        $wall_image  = 'Ho-092.png';
        $result = '';

        // By default, the wall goes along the south-west side of the map. This 
        // modification makes it go along the south-east side.
        if($side === 'right') {
            $wall_image  = 'Ho-092-right.png';
            $left = 5.5;
            // Turns the shifting negative with the same value, to build 
            // the wall from right to left
            $add_px_left = -$add_px_left;
        }

        for($i=0;$i<$length;$i++) {
            $result .= '<img src="resources/img/copyrighted/buildings/'.$wall_image.'"'
                     . ' height="'.$wall_size_px.'" width="'.$wall_size_px.'"'
                     . ' style="position:absolute;top:'.$top.'rem;left:'.$left.'rem">';
            $top  += $add_px_top;
            $left += $add_px_left; 
        }

        return '<div class="citywall citywall_'.$side.'" style="">'.$result.'</div>';
    }
    
    
    /**
     * Generates the HTML of the building to put on the isometric city map
     * 
     * @param string $name The name of the building for display (e.g. "Big door")
     * @param string $image_path The path to the image of the building
     * @return string HTML
     */
    private function asset($alias, $name, $image_path) {
        
        $size = $this->imageSize;
        $top  = $this->imagePositionTop;
        $left = $this->imagePositionLeft;
        // Resets the values to default for the next call to the method
        $this->setImageSize(self::IMAGE_DEFAULT_SIZE);
        $this->setImagePosition(self::IMAGE_POSITION_TOP, self::IMAGE_POSITION_LEFT);
        
        return '
            <div class="asset '.$alias.'" style="top:'.$top.'em;left:'.$left.'em;"
                 onclick="switchCitySubmenu(\''.$alias.'\');hide(\'city_iso\')">
                <div class="actions">
                    <div class="name">'.$name.'</div>
                    <div class="actionblocks">
                        '.$this->actionBlocks.'
                    </div>
                </div>
                <img src="resources/img/'.$image_path.'" height="'.$size.'" width="'.$size.'">
            </div>';
    }


    /**
     * The little square blocks under an active building of the city
     * (e.g.: after clicking on the workshop)
     * 
     * @param string $name The title of the block
     * @param int $amount The number to display in the block, 
     *                    e.g. the amount of items in the storage
     * @return string HTML
     */
    private function assetActionBlocks($name, $amount) {

        // Displays in red the negative numbers, or the time before the daily attack
        $red = ($amount < 0 or is_string($amount)) ? 'red' : '';

        return '
            <div class="actionblock">
                <div class="name">'.$name.'</div>
                <div class="number '.$red.'">'.$amount.'</div>
            </div>';
    }    
}
    