<?php
/**
 * Affiche le HTML des flèches pour déplacer le citoyen
 */
function movement_paddle($coord_x, $coord_y)
{
    
    return '
    <table id="movement_paddle">
        <tr>
            <td></td>
            <td>
                <form method="post" action="#Outside">
                    <input type="hidden" name="api_name" value="zone">
                    <input type="hidden" name="action" value="move">
                    <input type="hidden" name="params[to]" value="northwest">
                    <input type="submit" title="Aller au nord-ouest" value="▲" style="margin-left:-0.5em;transform:rotate(-30deg)">
                </form>
            </td>
            <td>
                <form method="post" action="#Outside">
                    <input type="hidden" name="api_name" value="zone">
                    <input type="hidden" name="action" value="move">
                    <input type="hidden" name="params[to]" value="northeast">
                    <input type="submit" title="Aller au nord-est" value="▲" style="margin-right:-0.5em;transform:rotate(30deg)">
                </form>
            </td>
            <td></td>
        </tr>
        <tr>
            <td>
                <form method="post" action="#Outside">
                    <input type="hidden" name="api_name" value="zone">
                    <input type="hidden" name="action" value="move">
                    <input type="hidden" name="params[to]" value="west">
                    <input type="submit" title="Aller à l\'ouest" value="&#9664;">
                </form>
            </td>
            <td colspan="2" id="central" onclick="toggle(\'my_bubble\')" title="Cliquez sur une flèche pour vous déplacer...">
                ' . $coord_x . ':' . $coord_y . '
            </td>
            <td>
                <form method="post" action="#Outside">
                    <input type="hidden" name="api_name" value="zone">
                    <input type="hidden" name="action" value="move">
                    <input type="hidden" name="params[to]" value="east">
                    <input type="submit" title="Aller à l\'est" value="&#9654;">
                </form>
            </td>
        </tr>
        <tr>
            <td></td>
            <td>
                <form method="post" action="#Outside">
                    <input type="hidden" name="api_name" value="zone">
                    <input type="hidden" name="action" value="move">
                    <input type="hidden" name="params[to]" value="southwest">
                    <input type="submit" title="Aller au sud-ouest" value="▼" style="margin-left:-0.5em;transform:rotate(30deg)">
                </form>
            </td>
            <td>
                <form method="post" action="#Outside">
                    <input type="hidden" name="api_name" value="zone">
                    <input type="hidden" name="action" value="move">
                    <input type="hidden" name="params[to]" value="southeast">
                    <input type="submit" title="Aller au sud-est" value="▼" style="margin-right:-0.5em;transform:rotate(-30deg)">
                </form>
            </td>
            <td></td>
        </tr>
    </table>';
}
