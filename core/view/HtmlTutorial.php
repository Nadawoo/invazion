<?php
/**
 * Display a tutorial in a modern-style, ie the buttons on the screen are highlighted
 * and a short text describe each one. This is not an old-style tutorial, 
 * ie pages of text in a static pop-up.
 */
class HtmlTutorial {
    
    /**
     * Call this method to create the complete tutorial
     * 
     * @return string HTML
     */
    public function all_steps() {
        
        return
        $this->materialize_fix()
        .$this->step('tuto_dayclock',
                     'Objectif',
                     'Votre but est de survivre le plus longtemps possible face à l\'attaque quotidienne des zombies.')
        .$this->step('tuto_button_move',
                     'Explorer',
                     'Déplacez-vous le désert afin de récolter des ressources vitales.',
                     true)
        .$this->step('tuto_button_dig',
                     'Fouiller',
                     'Vous trouverez les ressources en creusant chaque zone que vous traversez.',
                     true)
        .$this->step('tuto_discuss',
                     'Échanger',
                     'Discutez avec les autres joueurs afin de coordonner vos actions. L\'union fait la force !');
    }
    
    
    /**
     * Insert this template wherever you want in your HTML page before you call
     * the step() method.
     * It's a quick fix for Materialize.css. This HTML should be generated by Materialize 
     * but it is not. See the tutorial() function in Invazion\'s javascript
     * for the detailed explanation.
     * 
     * @return string HTML
     */
    private function materialize_fix() {
        
        return '
            <template id="tplTapTargetWave">
                <div class="tap-target-wave" style="top:323px; left:323px; width:154px; height:154px;">
                    <div class="tap-target-origin"></div>
                </div>
            </template>';
    }

    
    /**
     * The content of one tooltip of the tutorial
     * 
     * @param string $target The HTML ID of the item you describe in this step 
     *                       of the tutorial.
     * @param string $title
     * @param string $description
     * @param string $fix_position Set to "true" if your feature creates an horizontal
     *                             scroll on the page. See the tutorial() 
     *                             javascript function for the explanations.
     * @return string HTML
     */
    private function step($target, $title, $description, $fix_position=false) {
        
        $class_fix_position = ($fix_position === true) ? 'fix-position' : '';
        
        return '
            <div class="tap-target '.$class_fix_position.'" data-target="'.$target.'">
                <div class="tap-target-content">
                    <h5>'.$title.'</h5>
                    <p>'.$description.'</p>
                </div>
            </div>';
    }
}