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
        
        <h2>Connexion</h2>
        
        <input type="hidden" name="action" value="connect">
        
        <label for="email">Mon e-mail</label>
        <input type="email" name="email" id="email" placeholder="johndoe@gmail.com" value="'.$email.'"
               autocomplete="username" aria-describedby="username_constraints" required autofocus />
        <!--
        <div id="username_constraints" class="aside" style="margin-bottom:1.5em">
            L\'adresse e-mail que vous avez indiquée lorsque vous avez créé votre compte.
        </div>
        -->
        <br>
        <br>
        
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password"
               autocomplete="current-password" aria-describedby="password_constraints" />
        <div id="password_constraints" class="aside">
            Si vous n\'avez pas défini de mot passe lors de la création du compte,
            laissez ce champ vide.
        </div>
            
        <div id="buttonsBlock">
            <a href="register.php" style="font-variant:small-caps">Créer un compte</a>
            <input type="submit" value="Me connecter" class="redbutton" />
        </div>
        
        <p style="text-align:right"><a href="index">Annuler</a></p>
        
    </form>';
}
