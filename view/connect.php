<?php
/**
 * HTML form to connect to our player account
 * 
 * @param string $email The email address of the user
 * @return string HTML
 */
function connect($email)
{
    
    return '<form method="post">
        
        <input type="hidden" name="action" value="connect">
        
        <p><strong>Mon email&nbsp;:</strong>
            <input type="email" name="email" value="'.$email.'" autofocus />
            <span class="aside">L\'adresse mail que vous avez indiquée lorsque vous avez créé votre compte</span>
        </p>
        
        <p><strong>Mot de passe&nbsp;:</strong>
            <input type="password" name="password" />
            <span class="aside">Si vous n\'avez pas défini de mot passe, laissez ce champ vide </span>
        </p>
        
        <p><input type="submit" value="Me connecter" /></p>
        <p><a href="index" class="bold">&gt;&gt;&nbsp;Retourner au jeu</a></p>
        
    </form>';
}
