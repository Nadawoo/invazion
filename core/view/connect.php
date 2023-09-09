<?php
/**
 * HTML form to connect to our player account
 * 
 * @param string $email The email address of the user
 * @return string HTML
 */
function connect($email)
{
    
    return '<form method="post" id="connectionForm" onsubmit="connectUser(); return false;">
        
        <h2>Me connecter</h2>
        
        <input type="hidden" name="action" value="connect">
        
        <fieldset>
            <legend>E-mail</legend>
            <input type="email" name="email" id="email" value="'.$email.'" autofocus />
        </fieldset>
        <div class="aside">L\'adresse e-mail que vous avez indiquée lorsque
                         vous avez créé votre compte.</div>
        
        
        <fieldset>
            <legend>Mot de passe</legend>
            <input type="password" name="password" id="password" />
        </fieldset>
        <div class="aside">Si vous n\'avez pas défini de mot passe, laissez ce champ vide.</div>
        
        <div id="buttonsBlock">
            <a href="register.php" style="font-variant:small-caps">Créer un compte</a>
            <input type="submit" value="Me connecter" />
        </div>
        
        <p style="text-align:right"><a href="index">Annuler</a></p>
        
    </form>';
}
