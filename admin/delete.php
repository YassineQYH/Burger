<?php
    require 'database.php';

//  Si la SG GET n'est pas vide, à la place id  
//  Si je t'ai passé en GET un id alors tu vas me le mettre dans une variable qui s'appel id

if(!empty($_GET['id']))
{
    $id = checkInput($_GET['id']);  // Donc la au 1er passage j'a irécupéré l'id et je vais le stocker dans mon formulaire en mettant value="<?php echo $id; et tout ça, ça ne se voit pas, invisible à l'utilisateur.
}

    // Comment on le récupère dans la méthode POST ?
    // 2eme passage : 
    if(!empty($_POST))  // Si la SG POST n'est pas vide             // S'il a appuyé sur oui je suis dans le cas du POST
    {   // Allons récupérer notre id
        $id = checkInput($_POST['id']);  // Alors, ça veut dire que j'ai posté mon formulaire/ Que j'ai appuyé sur le bouton oui/ Le bouton oui il a le type submite, ce qui veut dire que c'est lui qui soumet le formulaire.

        // Une fois que la personne à appuyer sur oui, on va aller le supprimer en faisant une cmd SQL.
        $db = Database::connect();
        $statement = $db->prepare("DELETE FROM items WHERE id = ?");    // La, pas besoin de récupérer quoi que ce soit, c'est juste un ordre qui va dire : supprimer moi de la table items quoi ? l'id qui est égale à ce qu'on a donné. / Ce qu'on a donné, on a fait une petite variante cette fois, on l'a fait avec input type-hidden, on l'a pas récupéré avec le GET, on l'a récupéré avec le post quand j'ai soumis mon formulaire.
        $statement->execute(array($id));
        Database::disconnect();
        header("Location: index.php"); // Dans le cas ou tout s'est bien passé, je retourne dans la page index.php
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

                <h1><strong>Supprimer un item</h1>
                <br>

                <form class="form" role="form" action="delete.php" method="post">  
                    <!-- Au 2eme passage, on va vraiment supprimer l'id mais comment se rappeler de l'id ? Plusieurs possibilité : On aurait tout simplement pu faire comme dab et le passer dans l'url en disant ?id=" et mettre la valeur de l'id || Mais histoire de voir une méthode différente qui est aussi souvent utilisé on va changer || On va le mettre dans notre formulaire dans un input de type hidden => veut dire caché/c'est quelque chose qui ne va pas se voir à l'écran. Cet input, je vais lui donner un nom pour ensuite pouvoir le récupérer avec ma méthode POST et la valeur de cet input(la pour le coup je vais l'ouvrir dés la 1ere fois) on met un tag php echo $id;?> // D'où je l'ai récupéré cet id ? Il est venu dans l'url -->
                    <input type="hidden" name="id" value="<?php echo $id; ?>"></input><!-- je vais le stocker dans mon formulaire en mettant value=" echo $id; et tout ça, ça ne se voit pas, invisible à l'utilisateur.|| Pourquoi faire ça ? Car au 2eme passage on va supprimer cet enregistrement de la BDD avec la cmd SQL DELETE, pas au 1er passage mais au 2eme passage.-->
                    <!-- Au 2eme passage, je ne le récupère pas avec le GET(j'aurais pu mais je vais pas faire comme ça) mais avec le POST car dans le formulaire de type hidden qui à le nom id on lui à donner la valeur du $id donc c'est comme ça qu'on va le récupérer pour le 2eme passage.-->
                    <p class="alert alert-warning">Êtes-vous sur de vouloir supprimer ?</p> <!-- alert-warning => orange  -->

                    <div class="form-actions"> 
                        <button type="submit" class="btn btn-warning"> Oui</button>
                        <a class="btn btn-default" href="index.php"> Non</a> 
                    </div>
                </form>
            </div> <!-- Fermeture de row -->

        </div><!-- Fermeture de  container admin -->


    </body>


</html>