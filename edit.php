<?php
/**
 * Page to edit the configuration of the game (list of items...)
 */

require_once 'core/controller/autoload.php';
safely_require('core/view/elements/html_options.php');
safely_require('core/controller/official_server_root.php');
safely_require('ZombLib.php');


/**
 * Liste de tous les objets du jeu pour menu déroulant <select>
 * 
 * @param array  $items       Liste complète des objets, telle que reoutnrée par l'API
 * @param string $select_name Nom du menu pour l'attribut du formulaire HTML
 * @return string HTML 
 */
function html_select_items($items, $select_name)
{
    
    $select_items = '<option value=""></option>';
    
    foreach ($items as $item_id=>$caracs) {

        $select_items .= '<option value="'.$item_id.'">'.$caracs['name'].'</option>';
    }
    
    return '<select name="'.$select_name.'">'.$select_items.'</select>';
}


// Catégories d'objets pour calculer les chances de découverte en fouille
function html_select_type()
{
    
    // Must match with the list of item types in the database (ENUM field)
    $items_types = [
        'ress'          => 'Ressources courantes (planches...)',
        'ress_rare'     => 'Ressources rares (scie...)',
        'weapon'        => 'Armes courantes (cutter...)',
        'weapon_rare'   => 'Armes rares (super lance-piles...)',
        'boost'         => 'Boost ordinaires (eau, nourriture)',
        'boost_rare'    => 'Boost rares (drogue, alcool, café)',
        'healing'       => 'Soins (bandage...)',
        'defense'       => 'Objets de défense',
        'animal'        => 'Animaux',
        'decoration'    => 'Décoration',
        'misc'          => 'Divers (extensions de sac...)',
        ];

    $select_items = '<option value=""></option>';
    
    foreach ($items_types as $alias=>$name) {

        $select_items .= '<option value="'.$alias.'">'.$name.'</option>';
    }
    
    return '<select name="item_type">'.$select_items.'</select>';
}


// Type of rules for eating a boost, e.g.: only 1 food per day
// Must match with the list of boost types in the database (ENUM field)
$boost_types = ['food'      => 'Nourriture',
                'water'     => 'Eau',                                
                'alcohol'   => 'Alcool',
                'drug'      => 'Drogue',
                'coffee'    => 'Café',
                'dice'      => 'Dés'
                ];


$api        = new ZombLib(official_server_root().'/api');
$html       = new HtmlPage();
$htmlitem   = new HtmlConfigItems();
$items      = $api->call_api('configs', 'get')['datas']['items'];
$table_body = '';


$table_header = '<tr>
            <th>id</th>
            <th>Objet</th>
            <th>Créé par</th>
            <th>% de découverte</th>
            <th>Encombrant</th>
            <th>Boost</th>
            <th>Arme</th>
            <th>Assemblable à partir de</th>
        </tr>';

foreach ($items as $id=>$caracs) {
    
    $table_body .= '<tr>
            <td>#'.$id.'</td>
            <td>'.$caracs['name'].'</td>
            <td>user'.$caracs['user_id'].'</td>
            <td style="text-align:right">'.$caracs['finding_rate'].'%</td>
            <td>'.$htmlitem->heaviness($caracs).'</td>
            <td>'.$htmlitem->boost($caracs).'</td>
            <td>'.$htmlitem->weapon($caracs).'</td>
            <td>'.$htmlitem->craft($caracs).'</td>
        </tr>';
}


echo $html->page_header();
?>


<div id="editConfig">

<h2>Créer un nouvel objet</h2>

<p style="width:70%;margin:auto;font-size:0.9em;text-align:center;font-style:italic;">
    Note : les cases non cochables sont des options qui ne sont pas encore  
    totalement opérationnelles dans le jeu.
    Venez faire pression sur <a href="https://discord.gg/2GRPTyM">le Discord</a>
    pour accélérer leur mise en place :)
</p>
    
<form method="POST" onsubmit="createItem(); return false;">
    
    <fieldset>
        <legend>Nom &amp; description</legend>
        <label for="item_name">Nom de l'objet :</label><br>
        <input id="item_name" name="item_characs[name]" type="text" placeholder="Tranche de pain" style="width:22em">
        <br>
        <label for="descr_ambiance">Description d'ambiance (<em>roleplay</em>) :</label><br>
        <textarea cols="35" rows="4" id="descr_ambiance" name="item_characs[descr_ambiance]" type="text" 
                  placeholder="Ce morceau de pain moisi n'est guère appétissant, mais faute de mieux..."></textarea>
        <br>
        <label for="descr_purpose">Description de l'utilité :</label><br>
        <textarea cols="35" rows="4" id="descr_purpose" name="item_characs[descr_purpose]" type="text" 
                  placeholder="Consommer cet objet vous donnera 6 points d'action."></textarea>
    </fieldset>
    
    <fieldset>
        <legend>Où trouver l'objet</legend>
        
        <div>
            <input type="checkbox" name="is_findable" id="is_findable" onchange="toggle('block_findable')">
            <label for="is_findable">Peut être trouvé en fouillant le désert</label> 
        </div>
        <div style="margin-left:2em" id="block_findable">
            Catégorie de fréquence : [fonctionnalité à venir]
            <!--
            <label>Catégorie de fréquence : <?php echo html_select_type() ?></label><br>
            <div style="color:grey" class="aside">
                Chaque catégorie d'objet a plus ou moins de chances d'être trouvée en fouilles. 
                Ex : "soins" est plus rare que "ressource".
            </div>
            -->
            <div style="margin-bottom:0.3em;font-size:0.8em">
                <a onclick="toggle('block_findable_advanced')">► Réglages avancés...</a>
            </div>
            <div id="block_findable_advanced">
                [fonctionnalité à venir]
                <!--
                <label for="finding_rate">Chances de trouver l'objet à l'intérieur de sa catégorie :</label>
                <input id="finding_rate" name="item_characs[finding_rate]" type="number" min="0" max="100"  step="10" value="100">%<br>
                <div style="color:grey" class="aside"><span style="font-style:normal">&#9888;&#65039;</span>
                    Comprendre comment agit ce pourcentage :<br>
                    • Quel que soit le pourcentage, l'objet n'a <strong>aucune chance</strong> d'être trouvé 
                    si le tirage au sort de la fouille ne sélectionne pas la <strong>catégorie de fréquence</strong>
                    à laquelle appartient l'objet.<br>
                    • Si sa catégorie est tirée au sort, une chance de <strong>100%</strong> signifie 
                    que l'objet a <strong>autant</strong> de chances d'être trouvé que les autres objets 
                    de la même catégorie. 
                    Ne réduisez ce taux que si l'objet doit être plus rare que les autres.
                </div>
                -->
            </div>
        </div>
        
        <div>
            <input type="checkbox" name="item_characs[is_craftable]" id="is_craftable" onchange="toggle('block_compo')" disabled>
            <label for="is_craftable">Peut être fabriqué en assemblant d'autres objets</label>
        </div>
        <div style="margin-left:2em" id="block_compo">
            <label>Composant : <?php echo html_select_items($items, 'craft_compo1') ?></label> <a>&#10060;</a><br>
            <label>Composant : <?php echo html_select_items($items, 'craft_compo2') ?></label> <a>&#10060;</a><br>
            <label>Composant : <?php echo html_select_items($items, 'craft_compo3') ?></label> <a>&#10060;</a><br>
            <label>Composant : <?php echo html_select_items($items, 'craft_compo4') ?></label> <a>&#10060;</a><br>
            <label>Composant : <?php echo html_select_items($items, 'craft_compo5') ?></label> <a>&#10060;</a><br>
            <a>	&#10133; Ajouter un composant...</a>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Utilité de l'objet</legend>
        <strong>Effets sur le joueur :</strong>
        <div>
            <input type="checkbox" name="is_boost" id="is_boost" onchange="toggle('block_apgain')">
            <label for="is_boost">Donne des points d'action après utilisation</label>
        </div>
        <div style="margin-left:2em" id="block_apgain">
            <label for="ap_gain">Nombre de points gagnés :</label>
            <input id="ap_gain" name="item_characs[ap_gain]" type="number" min="0" value="0">
            <br>
            <label for="boost_type">Règles de consommation de :</label>
            <select id="boost_type" name="item_characs[boost_type]">
                <?php echo html_options($boost_types) ?>
            </select>
        </div>
        
        <div>
            <input type="checkbox" name="item_characs[is_malus]" id="is_malus" onchange="toggle('block_malus')" disabled>
            <label for="is_malus">Dégrade la santé après utilisation</label>
        </div>
        <div style="margin-left:2em" id="block_malus">
            <input type="checkbox" id="malus_thirst" value="malus_thirst">
            <label for="malus_thirst">Donne soif</label><br>
            <input type="checkbox" id="malus_wound" value="malus_wound">
            <label for="malus_wound">Donne une blessure</label><br>            
            <input type="checkbox" id="malus_infection" value="malus_infection">
            <label for="malus_infection">Donne une infection</label><br>
            <input type="checkbox" id="malus_terror" value="malus_terror">
            <label for="malus_terror">Donne la terreur</label>
        </div>
        
        <div>
            <input type="checkbox" name="item_characs[is_healing]" id="is_healing" onchange="toggle('block_healing')" disabled>
            <label for="is_healing">Améliore la santé après utilisation</label>
        </div>
        <div style="margin-left:2em" id="block_healing">
            <input type="checkbox" id="heals_thirst" value="heals_thirst">
            <label for="heals_thirst">Enlève la soif</label><br>
            <input type="checkbox" id="heals_wound" value="heals_wound">
            <label for="heals_wound">Enlève la blessure</label><br>            
            <input type="checkbox" id="heals_infection" value="heals_infection">
            <label for="heals_infection">Enlève l'infection</label><br>
            <input type="checkbox" id="heals_terror" value="heals_terror">
            <label for="heals_terror">Enlève la terreur</label>
        </div>
                
        <div>
            <input type="checkbox" name="bag_extensions" id="bag_extensions" onchange="toggle('block_bag')" disabled>
            <label for="bag_extensions">Augmente la capacité du sac à dos</label>
        </div>
        <div style="margin-left:2em" id="block_bag">
            <label for="bag_extensions">Nombre de places supplémentaires :</label>
            <input id="bag_extensions" name="bag_extensions" type="number" min="1" value="1">
            <br>
            <label for="bag_unique_extension">Interdit de porter d'autres extensions :</label>
            <select id="bag_unique_extension" name="bag_unique_extension">
                <option value="1">oui</option>
                <option value="0">non</option>
            </select>
        </div>
        
        <br>
        <strong>Effets en tant qu'objet :</strong>
        <div>
            <input type="checkbox" name="item_characs[killing_rate]" value="100" id="is_weapon" onchange="toggle('block_weapon')">
            <label for="is_weapon">Est une arme contre les zombies</label>
        </div>
        <div style="margin-left:2em" id="block_weapon">
            <input type="radio" name="killing_rate" value="100" id="is_weapon_efficient" checked>
            <label for="is_weapon_efficient">L'objet tue 1 zombie à coup sûr</label><br>
            <input type="radio" name="killing_rate" value="custom" id="weapon_efficiency_custom" disabled>
            <label for="weapon_efficiency_custom">Personnaliser...</label><br>
            <div id="block_killing_rate" style="margin-left:2em">
                <label for="killing_rate">Chances de tuer un zombie :</label>
                <input id="killing_rate" name="killing_rate" type="number" min="0" value="100">%
                <br>
                <label for="max_killed">Nombre de tués max. par coup :</label>
                <input id="max_killed" name="max_killed" type="number" min="1" value="1">
            </div>
        </div>
        
        <input type="checkbox" name="item_characs[is_defense]" id="is_defense" disabled>
        <label for="is_defense">Est un objet de défense</label>
        <br>
        <input type="checkbox" name="item_characs[is_decoration]" id="is_decoration" disabled>
        <label for="is_decoration">Est un objet de décoration</label>
        <br>
        
        <div>
            <input type="checkbox" name="item_characs[is_dropper]" id="is_dropper" onchange="toggle('block_drop')" disabled>
            <label for="is_dropper">Fait apparaître un nouvel objet après utilisation</label>
        </div>
        <div style="margin-left:2em" id="block_drop">
            <em>Tirer au sort un objet parmi :</em><br>
            <?php echo 
            '• '. html_select_items($items, 'drop_item_id[1]')
            . ' (chances <input type="number" name="drop_rate[1]" min="0" max="100"  step="10" value="100">%)<br>' .
            '• '. html_select_items($items, 'drop_item_id[2]')
            . ' (chances <input type="number" name="drop_rate[2]" min="0" max="100"  step="10" value="100">%)<br>' .
            '• '. html_select_items($items, 'drop_item_id[3]')
            . ' (chances <input type="number" name="drop_rate[3]" min="0" max="100"  step="10" value="100">%)'  
            ?>
        </div>
    </fieldset>
    
    <fieldset>
        <legend>Conditions d'utilisation</legend>
        
        <div>
            <input type="checkbox" name="item_characs[is_loadable]" id="is_loadable" onchange="toggle('block_loads')" disabled>
            <label for="is_loadable">Doit être chargé pour être utilisable (munition, énergie...)</label>
        </div>
        <div style="margin-left:2em" id="block_loads">
            <label>Objet à utiliser comme recharge :
            <?php echo html_select_items($items, 'load_item_id') ?>
            </label>
            <br>
            <label for="loads">Nombre d'utilisations une fois chargé :</label>
            <input id="loads" name="loads" type="number" min="1" value="1">
        </div>
        
        <input type="checkbox" name="item_characs[heaviness]" value="1" id="is_heavy">
        <label for="is_heavy">Est un objet encombrant</label>

    </fieldset>
    
    <fieldset>
        <legend>État de l'objet après utilisation</legend>
        
        <input type="radio" name="item_characs[destruction_rate]" value="100" id="is_destroyed" checked onchange="hide('block_solidity_custom')"> 
        <label for="is_destroyed">L'objet disparaît (définitif)</label><br>
        <input type="radio" name="item_characs[destruction_rate]" value="is_broken" id="is_broken" onchange="hide('block_solidity_custom')" disabled>
        <label for="is_broken">L'objet est cassé (réparable)</label><br>
        <input type="radio" name="item_characs[destruction_rate]" value="0" id="is_intact" onchange="hide('block_solidity_custom')">
        <label for="is_intact">L'objet reste intact</label><br>
        <input type="radio" name="item_characs[destruction_rate]" value="custom" id="solidity_custom" onchange="toggle('block_solidity_custom')" disabled>
        <label for="solidity_custom">Personnaliser...</label><br>
        <div style="margin-left:2em" id="block_solidity_custom">
            <label for="break_rate">Cassé :</label>
            <input id="break_rate" name="item_characs[break_rate]" type="number" min="0" max="100" value="0">% | 
            <label for="destruction_rate">Détruit :</label>
            <input id="destruction_rate" name="destruction_rate" type="number" min="0" max="100" value="100">% | 
            <label for="intact_rate">Intact :</label>
            <input id="intact_rate" name="intact_rate" type="number" min="0" max="100" value="0">%<br>
            <em>Intact = 100% - (Risque de casse + Risque de destruction)</em>
        </div>
    </fieldset>
    
    
    <p id="error" style="color:red;font-weight:bold;text-align:center"></p>
    
    <p class="center"><input type="submit" value="Enregistrer" class="redbutton"></p>
    
</form>


<hr>


<h2>Liste des objets existants</h2>
<?php
echo '<table>'
        .$table_header
        .$table_body.
    '</table>';
?>

<!-- End of the page container #editConfig -->
</div>

<?php
echo $html->page_footer();
