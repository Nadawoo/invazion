<?php
require_once 'controller/autoload.php';
safely_require('controller/is_development_server.php');
safely_require('view/BuildHtml.php');
safely_require('ZombLib.php');

$html       = new BuildHtml();
$api        = new ZombLib(is_development_server() ? 'http://invazion.localhost/api' : '');
$html_error = '';
$user_id    = NULL;

$action     = filter_input(INPUT_POST, 'action',     FILTER_SANITIZE_STRING);
$password   = filter_input(INPUT_POST, 'password',   FILTER_SANITIZE_STRING);
$email      = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));


// Actions possibles
if ($action === 'connect') {
    
    if (empty($email)) {
        
        $html_error = 'Veuillez indiquer l\'adresse mail que vous aviez utilisée pour créer votre compte.<br/>'
                . 'Si vous n\'avez pas encore de compte, commencez par <a href="/register">en créer un</a>';
    }
    else {
        
        $json = $api->connect_user($email, $password);
        
        if ($json['metas']['error_code'] === 'success') {
            
            $html_error = 'Vous êtes maintenant connecté.';
            // Redirige automatiquement vers la page du jeu au bout d'1 seconde
            header('Refresh: 1; url=index.php'); 
        }
        else {
            
            $html_error = 'Vos identifiants n\'ont pas été reconnus. Vérifiez-les et réessayez...';
        }
    }
}
elseif ($action === 'disconnect') {
    
    $json = $api->disconnect_user();
    
    if ($json['metas']['error_code'] === 'success') {
        
        $html_error = 'Vous avez été déconnecté avec succès.';
    }
    else {
        
        $html_error = 'La déconnexion a échoué pour une raison inconnue. Veuillez réessayer...';
    }
} 


if ($api->user_seems_connected() === TRUE) {
    
    $user_id = $api->get_token_data('user_id');
} ?>


<?php echo $html->page_header() ?>

<h1>Me connecter</h1>

<p class="center" style="color:red;font-weight:bold"><?php echo $html_error ?></p>

<?php
if ($api->user_seems_connected() === TRUE) {
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
    ?>
    
    <form method="post">
        <input type="hidden" name="action" value="connect">
        <p><strong>Mon email&nbsp;:</strong>
            <input type="email" name="email" value="<?php echo $email ?>" autofocus />
            <span class="aside">L'adresse mail que vous avez indiquée lorsque vous avez créé votre compte</span>
        </p>
        
        <p><strong>Mot de passe&nbsp;:</strong>
            <input type="password" name="password" />
            <span class="aside">Si vous n'avez pas défini de mot passe, laissez ce champ vide </span>
        </p>
        
        <p><input type="submit" value="Me connecter" /></p>
        <p><a href="index" class="bold">&gt;&gt;&nbsp;Retourner au jeu</a></p>
    </form>
    
    <?php
} ?>

<?php echo $html->page_footer();
