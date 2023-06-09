<?php
session_start(); 
// Démarre une session pour utiliser les variables de session

if (!empty($_POST) && !empty($_POST['mail']) && !empty($_POST['password'])) 
{
    require_once 'include/db.php'; 
    // Inclut le fichier contenant la connexion à la base de données

    $req = $pdo->prepare("SELECT `user`.* FROM `user` WHERE `user`.`mail` = ?");
    $req->execute([$_POST['mail']]);
    $user = $req->fetch();
   

    if ($user) {
        $reqRoles = $pdo->prepare("SELECT `role`.`nom` AS 'role'
        FROM `user_has_role`
        LEFT JOIN `role` ON `user_has_role`.`role_id` = `role`.`id`
        WHERE `user_has_role`.`user_id` = ?");
        $reqRoles->execute([$user->id]);
        $roles = $reqRoles->fetchAll(PDO::FETCH_COLUMN);

        if (password_verify($_POST['password'], $user->mp)) { 
            // Vérifie si le mot de passe fourni correspond au mot de passe haché stocké en base de données
           
            
            if(in_array('ADMIN', $roles)) {
                 // Utilisateur a le rôle ADMIN
                 $_SESSION['admin'] = $user;
                $_SESSION['flash']['success'] = 'Vous êtes maintenant connecté en tant qu\'administrateur';
                  header('Location: vueProfil/admin.php'); 
                  exit();
            }elseif(in_array('USER', $roles)) {
                 // Utilisateur a le rôle USER
                 $_SESSION['auth'] = $user;
                 $_SESSION['flash']['success'] = 'Vous êtes maintenant connecté en tant qu\'utilisateur';
                header('Location: vueProfil/profil.php'); 
                exit();
                // $_SESSION['flash']['danger'] = "vous n'avez pas les droits d'accés";
                // header("Refresh:0"); 
            }else { 
            // Utilisateur sans rôle approprié
            $_SESSION['flash']['danger'] = 'Vous n\'avez pas les droits d\'accès'; 
            header('Refresh:0');
            exit();
            }
        }else{
            $_SESSION['flash']['danger'] = 'Identifiant ou mot de passe incorrecte';
    }
}

}




