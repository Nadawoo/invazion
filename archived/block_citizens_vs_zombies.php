<?php
/**
 * Génère en HTML l'encadré du rapport de force entre les citoyens et les zombies
 * (« Contrôle de la zone »)
 * 
 * @param int $nbr_citizens Le nombre de citoyens dans la zone
 * @param int $nbr_zombies  Le nombre de zombies dans la zone
 * @param int $total_citizens_pts   Nombre total de points de contrôle 
 *                                  de tous les citoyens dans la zone
 * @param int $total_zombies_pts   Nombre total de points de contrôle 
 *                                 de tous les zombies dans la zone
 * 
 * @return string HTML
 */
function block_citizens_vs_zombies($nbr_citizens, $nbr_zombies, $total_citizens_pts, $total_zombies_pts)
{

   $control_summary = '';

   if ($nbr_zombies === 0) {

       $control_summary = '<span style="color:darkgreen">'
           . 'Il n\'y a aucun zombie ici. Vous&nbsp;pouvez vous&nbsp;déplacer librement.'
           . '</span>';
   }
   elseif ($total_zombies_pts <= $total_citizens_pts) {

       $control_summary = '<span style="color:red">'
           . 'Il&nbsp;y&nbsp;a des&nbsp;zombies ici&nbsp;! Quitter la&nbsp;zone '
           . 'vous&nbsp;coûtera 1&nbsp;point&nbsp;d\'action.'
           . '</span>';
   }

   return 
     '<p>' . $control_summary . '</p>'
   . '<div style="display:flex;flex-direction:row;align-items:center;justify-content:center">'
   .   '<div class="green" style="text-align:center;cursor:help" '
           . 'title="Il y a '.plural($nbr_citizens, 'humain') . " sur cette case\n" 
           . '=> '.$total_citizens_pts.' points de contrôle pour les humains">'
           . '<span style="font-variant:small-caps">humains</span><br>'
           . '<strong style="font-size:2em">'.$total_citizens_pts.'</strong><br>'
           . 'points de contrôle<br>'
           . '<span class="small">('.plural($nbr_citizens, 'joueur').' x 5 pdc)</span>'
   .   '</div>'
   .   '<div style="text-align:center;margin:0 5%">'
   .       '&lt;vs&gt;'
   .   '</div>'
   .   '<div class="red" style="text-align:center;cursor:help" '
           . 'title="Il y a '.plural($nbr_zombies, 'zombie') . " sur cette case\n" 
           . '=> '.$total_zombies_pts.' points de contrôle pour les zombies">'
           . '<span style="font-variant:small-caps">zombies</span><br>'
           . '<strong style="font-size:2em">'.$total_zombies_pts.'</strong><br>'
           . 'points de contrôle<br>'
           . '<span class="small">('.plural($nbr_zombies, 'zombie').' x 1 pdc)</span>'
   .   '</div> '
   . '</div>';
}
