<?php
require_once '../core/controller/autoload.php';
safely_require('/core/model/Server.php');

$server = new Server();
$official_server_root = $server->official_server_root();
$http_host = filter_var($_SERVER['HTTP_HOST'],   FILTER_SANITIZE_URL);
$registration_page = $official_server_root.'/register?redirect='.urlencode('http://'.$http_host.'/connect');

// Redirect directly to the registration page on the Azimutant's main server.
header("Location: ".$registration_page);
exit;


$html = new HtmlLayout();

echo $html->page_header() 
?>


<h2>Créer un compte Azimutant</h2>

<p>Pour des raisons de sécurité, vous devez créer votre compte sur le serveur central d'Azimutant :</p>

<p class="center" style="font-size:1.3em">
    <strong><a href="<?php echo $registration_page ?>">► Créer mon compte ◄</a></strong>
</p>

<p>Vous pourrez ensuite revenir ici pour jouer.</p>

<p>Cela n'est nécessaire qu'au moment de créer votre compte. Pour vous connecter 
    ensuite, vous n'aurez pas besoin de passer par le serveur central.
</p>


<h4 style="margin-top:3em">Pourquoi ne pas m'inscrire directement ici ?</h4>
<p>La création de votre compte sur le serveur central renforce la sécurité de vos données.</p>


<?php echo $html->page_footer() ?>
