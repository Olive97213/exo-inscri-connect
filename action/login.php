<?php
session_start(); 
// Démarre une session pour utiliser les variables de session

if (!empty($_POST) && !empty($_POST['mail']) && !empty($_POST['password'])) 
{
    require_once 'include/db.php'; 
    // Inclut le fichier contenant la connexion à la base de données

    //$req = $pdo->prepare('SELECT * FROM user WHERE email = ?'); 
    
    $req = $pdo->prepare("SELECT `user`.*,`role`.`nom` AS 'role'
    FROM `user` 
        LEFT JOIN `user_has_role` ON `user_has_role`.`user_id` = `user`.`id` 
        LEFT JOIN `role` ON `user_has_role`.`role_id` = `role`.`id`
    WHERE `user`.`mail` = ?");
    // Prépare une requête pour récupérer les données utilisateur en fonction de l'email
    $req->execute([$_POST['mail']]); 
    // Exécute la requête en utilisant l'email fourni dans le formulaire
    $user = $req->fetch(); 
    // Récupère la première ligne de résultat

    if ($user) {
        if (password_verify($_POST['password'], $user->mp)) { 
            // Vérifie si le mot de passe fourni correspond au mot de passe haché stocké en base de données
            $_SESSION['auth'] = $user; 
            // Stocke les informations utilisateur dans la variable de session
            $_SESSION['flash']['success'] = "Vous êtes bien connecté"; 
            // Stocke un message de succès dans la variable de session
            if($user->role == "ADMIN") {
                header('Location: vueProfil/profil.php'); 
            }else {
                $_SESSION['flash']['danger'] = "vous n'avez pas les droits d'accés";
                header("Refresh:0"); 
            
            }
           
            // Redirige l'utilisateur vers la page de profil
            exit(); // Arrête l'exécution du script
        } else { 
            $_SESSION['flash']['danger'] = "Identifiant ou mot de passe incorrect"; 
            // Stocke un message d'erreur dans la variable de session
        }
    }
}
