<?php
/**
 * Generates the movement paddle to move the citizen
 */
class HtmlMovementPaddle
{
    
    private $arrows = [
                'northwest' => [
                    'title' => 'Aller au nord-ouest',
                    'icon'  => '▲',
                    'style' => 'margin-left:-0.5em;transform:rotate(-30deg)',
                    ],
                'northeast' => [
                    'title' => 'Aller au nord-est',
                    'icon'  => '▲',
                    'style' => 'margin-right:-0.5em;transform:rotate(30deg)',
                    ],
                'west' => [
                    'title' => 'Aller à l\'ouest',
                    'icon'  => '&#9664;',
                    'style' => '',
                    ],
                'east' => [
                    'title' => 'Aller à l\'est',
                    'icon'  => '&#9654;',
                    'style' => '',
                    ],
                'southwest' => [
                    'title' => 'Aller au sud-ouest',
                    'icon'  => '▼',
                    'style' => 'margin-left:-0.5em;transform:rotate(30deg)',
                    ],
                'southeast' => [
                    'title' => 'Aller au sud-est',
                    'icon'  => '▼',
                    'style' => 'margin-right:-0.5em;transform:rotate(-30deg)',
                    ],
                ];
    
    
    /**
     * Main method: generates the paddle
     * 
     * @param  int $coord_x The column where the player is on the map
     * @param  int $coord_y The row where the player is on the map
     * @return string HTML
     */
    public function paddle($coord_x, $coord_y)
    {

        return '
        <form method="post" action="#Outside" name="move" id="movement_paddle">
        <table>
            <tr>
                <td></td>
                <td>'.$this->arrow('northwest').'</td>
                <td>'.$this->arrow('northeast').'</td>
                <td></td>
            </tr>
            <tr>
                <td>'.$this->arrow('west').'</td>
                <td colspan="2" id="central" onclick="centerMapOnMe()" title="Cliquez sur une flèche pour vous déplacer...">
                    <br>
                    <img src="resources/img/free/human.png" class="me">
                    <span class="coords"></span>
                </td>
                <td>'.$this->arrow('east').'</td>
            </tr>
            <tr>
                <td></td>
                <td>'.$this->arrow('southwest').'</td>
                <td>'.$this->arrow('southeast').'</td>
                <td></td>
            </tr>
        </table>
        </form>';
    }
    
    
    /**
     * Générates an arrow for one of the 6 possible directions 
     * 
     * @param  string $direction A direction chosen in the list defined in this method
     *                           Ex : north / south / southeast...
     * @return string HTML
     */
    private function arrow($direction)
    {
        
        return '<button type="submit" name="direction" value="'.$direction.'" 
                    title="'.$this->arrows[$direction]['title'].'"
                    style="'.$this->arrows[$direction]['style'].'">
                    '.$this->arrows[$direction]['icon'].'
                </button>
            ';
    }
}

