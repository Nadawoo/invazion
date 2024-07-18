<?php
// TODO: remove this code after migrating to PHP >= v.7.3. The function array_key_first()
// exists in those PHP versions.
// Source of this code: https://www.php.net/manual/en/function.array-key-first.php#refsect1-function.array-key-first-notes
if (!function_exists('array_key_first')) {
    
    function array_key_first(array $arr) {
        foreach($arr as $key=>$unused) {
            return $key;
        }
        return null;
    }
}
