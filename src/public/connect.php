<?php
require_once '../core/controller/autoload.php';
safely_require('/core/model/Server.php');
safely_require('/core/ZombLib.php');

$server = new Server();
$official_server_root = $server->official_server_root();
$html = new HtmlPage();
$api  = new ZombLib($official_server_root.'/api');
?>

<?php
// If the user is not connected, go back to the main page
if($api->user_seems_connected() === false) {    
    header("Location:index");
    exit;
}

echo $html->page_header();

$user_id = $api->get_token_data('user_id');
?>

<form id="disconnectionForm" method="post">
    <p>Vous êtes connecté en tant que joueur n°&nbsp;<?php echo $user_id ?></p>
    <p><a href="index#Outside" class="bold">&gt;&gt;&nbsp;Continuer ma partie en cours</a></p>
    <p><a href="games" class="bold">&gt;&gt;&nbsp;Voir toutes les parties</a></p>
    <p><a href="edit" class="bold" title="Paramétrez les objets disponibles dans le jeu (bêta)">&gt;&gt;&nbsp;Créer des objets</a></p>
    <p class="center">
        <input type="hidden" name="action" value="disconnect">
        <input type="submit" value="Me déconnecter" />
    </p>
</form>

<?php    
echo $html->page_footer();
