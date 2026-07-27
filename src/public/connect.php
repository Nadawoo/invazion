<?php
require_once '../core/controller/autoload.php';
safely_require('/core/model/Server.php');
safely_require('/core/ZombLib.php');

$server = new Server();
$official_server_root = $server->official_server_root();
$html       = new HtmlPage();
$api        = new ZombLib($official_server_root.'/api');
$user_id    = null;

echo $html->page_header();
?>

<?php
if ($api->user_seems_connected() === true) {
    
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
}
else {
    ?>
    
    <form id="connectionForm" method="post" class="popup z-depth-2">

        <h2>Connexion</h2>
        
        <input type="hidden" name="action" value="connect">

        <label for="email">Mon e-mail</label>
        <input type="email"
               name="email"
               id="email"
               placeholder="johndoe@gmail.com"
               autocomplete="username"
               aria-describedby="username_constraints" required autofocus />

        <br>
        <br>

        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password"
               autocomplete="current-password" aria-describedby="password_constraints" />
        <div id="password_constraints" class="aside">
            Si vous n'avez pas défini de mot passe lors de la création du compte,
            laissez ce champ vide.
        </div>

        <input type="submit" value="Me connecter" class="redbutton" />

        <p class="center">
            <a href="register.php">Créer un compte</a>
        </p>

        <p style="text-align:right">
            <a href="index">Retour</a>
        </p>

    </form>

    <?php
}

echo $html->page_footer();
