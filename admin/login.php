<?php
    require 'database.php';
    session_start();    // La page doit commencer avec session_start car je vais jouer avec les sessions.

    $email = $password = $error ="";


    if(!empty($_POST))
    {   
        $email = checkInput($_POST['email']); 
        $password = checkInput($_POST['password']); 

    // Vérifier si l'email et le mdp correspondent à une ligne dans ma BDD
    $db = Database::connect();
    $statement = $db->prepare("SELECT * FROM users WHERE email = ? and password = ?");
    $statement->execute(array($email,$password));
    Database::disconnect();

    if($statement->fetch()) // Si il ya une ligne, il va rentrer dans ce if // ça veut dire que mon utiliser peux se connecter puisqu'il se trouve dans ma BDD
    {   // Donc la je dois rajouter quelque chose dans ma SG session
        $_SESSION['email'] = $email;    // Je vais enregistrer session email = email, peut importe ce qu'il y a d'écrit dedans, c'est juste pour dire qu'on a inscrit quelque chose dans la session.
        header("Location: index.php");  // Maintenant, je veux rediriger l'utilisateur vers la page index.php de admin
    }
    else
    {
        $error = "Votre email ou mot de passe est incorrect.";
    }
    }

    function checkInput($data)
    {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data; 
    }
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Burger Code</title>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
        <link href='http://fonts.googleapis.com/css?family=Holtwood+One+SC' rel='stylesheet' type='text/css'>   <!-- C'est une font de Google-Font -->
        <link rel="stylesheet" href="../css/style.css">
    </head>

    <body>  

        <h1 class="text-logo">
            <span class="glyphicon glyphicon-cutlery"></span>
            Burger Code
            <span class="glyphicon glyphicon-cutlery"></span>
        </h1>
        <div class="container admin">

            <div class="row">

                <h1><strong>Login</h1>
                <br>
                <form class="form" action="login.php" method="post">  

                    <div class="form-group">
                        <label for="email">Adresse e-mail:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="E-mail" value="<?php echo $email; ?>">
                    </div> 

                    <div class="form-group">
                        <label for="password">Mot de passe:</label>
                        <input type="text" class="form-control" id="password" name="password" placeholder="Mot de passe" value="<?php echo $password; ?>">
                    </div> 

                    <span class="help-inline"><?php echo $error; ?></span>
                    

                    <div class="form-actions"> 
                        <button type="submit" class="btn btn-primary"> Se connecter</button>
                    </div>
                </form>
            </div> <!-- Fermeture de row -->

        </div><!-- Fermeture de  container admin -->


    </body>


</html>