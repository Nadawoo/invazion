<?php
/**
 * Check whether the citizen is currently in a game (= has joined a map and
 * has no death waiting for a validation)
 * 
 * @param int $citizen_map_id The ID of the citizen's map
 * @param string $unvalidated_death_cause The cause of death waiting for a validation,
 *                                        as returned by the "me" API
 * @return bool
 */
function is_in_game($citizen_map_id, $unvalidated_death_cause) {
    
    return ($citizen_map_id !== null and $unvalidated_death_cause === null);
}
