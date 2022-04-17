<?php
/**
 * Options d'un menu déroulant <select> en HTML
 * 
 * @param array $options Liste des options, de forme [clé=>Nom, clé=>Nom...]
 * @return string HTML
 */
function html_options($options)
{
    $result = '';

    foreach ($options as $key=>$val) {

        $result .= '<option value="'.$key.'">'.$val.'</option>';
    }
    
    return $result;
}
