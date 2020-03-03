<?php
require_once 'controller/autoload.php';
safely_require('view/BuildHtml.php');

$http_host   = filter_var($_SERVER['HTTP_HOST'],   FILTER_SANITIZE_URL);

$html = new BuildHtml();

echo $html->page_header() 
?>


<h2>Créer un compte InvaZion</h2>

<p>Pour des raisons de sécurité, vous devez créer votre compte sur le serveur central d'InvaZion :</p>

<p class="center" style="font-size:1.3em">
    <strong><a href="<?php echo 'https://invazion.nadazone.fr/register?redirect='.urlencode($http_host) ?>">► Créer mon compte ◄</a></strong>
</p>

<p>Vous pourrez ensuite revenir ici pour jouer.</p>

<p>Cela n'est nécessaire qu'au moment de créer votre compte. Pour vous connecter 
    ensuite, vous n'aurez pas besoin de passer par le serveur central.
</p>


<h4 style="margin-top:3em">Pourquoi ne pas m'inscrire directement ici ?</h4>
<p>La création de votre compte sur le serveur central renforce la sécurité de vos données.</p>


<?php echo $html->page_footer() ?>
