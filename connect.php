<?php
require_once 'controller/autoload.php';
safely_require('controller/official_server_root.php');
safely_require('view/HtmlPage.php');
safely_require('view/connect.php');
safely_require('ZombLib.php');

$html       = new HtmlPage();
$api        = new ZombLib(official_server_root().'/api');
$html_error = '';
$user_id    = NULL;

$action     = filter_input(INPUT_POST, 'action',     FILTER_SANITIZE_STRING);
$email      = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));


// Actions possibles
if ($action === 'connect') {
    
    // The connection form is now handled by javascript
    
}
elseif ($action === 'disconnect') {
    
    $json = $api->disconnect_user();
    
    $html_error = ($json['metas']['error_code'] === 'success')
                    ? 'Vous avez été déconnecté avec succès.'
                    : 'La déconnexion a échoué pour une raison inconnue. Veuillez réessayer...';
} 


echo $html->page_header();
?>

<h1>Me connecter</h1>

<p class="center" style="color:red;font-weight:bold"><?php echo $html_error ?></p>
<p id="error" class="center" style="color:orangered;font-weight:bold"></p>

<?php
if ($api->user_seems_connected() === TRUE) {
    
    $user_id = $api->get_token_data('user_id');
    ?>
    
    <form method="post">
        <input type="hidden" name="action" value="disconnect">
        <p>Vous êtes connecté en tant que joueur n°&nbsp;<?php echo $user_id ?></p>
        <p><a href="index" class="bold">&gt;&gt;&nbsp;Retourner au jeu</a></p>
        <p class="center"><input type="submit" value="Me déconnecter" /></p>
    </form>
    
    <?php
}
else { 
    
    echo connect($email);
    
}

echo $html->page_footer();
