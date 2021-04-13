<?php
/**
 * Gets the number of days passed since the beginning of the current game
 * 
 * @param string $game_start_date The date the new city was launched 
 *                                (e.g. "2020-11-09"). If is null, the function
 *                                will return NULL.
 * @return int
 */
function get_game_day($game_start_date) {
    
    $result = null;
    
    if ($game_start_date !== null) {
        $last_death = new DateTime($game_start_date);
        $curr_date  = new DateTime(date('Y-m-d'));
        $result = $last_death->diff($curr_date)->days;
    }
    
    return $result;
}
