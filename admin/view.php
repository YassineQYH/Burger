<?php
    require 'database.php'; // Pour me connecter à ma database


    // Je veux récuperer mon id qui est transmis dans l'url
    if(!empty($_GET['id'])) // S'il n'est pas vide, (la super global GET) C'est une SG dans laquelle les données qui sont transmises dans l'url vont être stocké dans cette SG
    {
        //S'il n'est pas vide, tu me le met dans une variable id
        $id = checkInput(($_GET['id'])); // Quand on récupère des variables depuis l'extérieur, on a envie de les checker pour pas donner donner aux hackers une porte d'entrée (pour la sécurité) dans nos informations.
    }

    //  On va maintenant se connecter à notre Database et on va stocker cette connection dans $db comme la fois précédente pour index.php de Admin
    //  On utilise encore une fois notre fonction statique de la classe Database qui est dans le require de la Database 
    $db = Database::connect();   // Dans $db maintenant on a la connection.
    // Je ne sais pas d'avance qu'elle va être l'id car je ne sais pas d'avance sur quel bouton pour voir l'item on va cliquer. Donc j'utilise la fonction prepare.
    $statement = $db->prepare('SELECT items.id, items.name, items.description, items.price, items.image, categories.name AS category
                                FROM items LEFT JOIN categories ON items.category = categories.id
                                WHERE items.id = ?'); // Where car je ne veux pas toute les lignes, je ne veux que la ligne où items.id est égale à $id mais vu que je ne le sait pas d'avance je met un point d'intérogation.
    // J'execute cette requête avec le $id avec la valeur que j'ai envie de lui donner, il n'y a qu'une valeur et c'est $id.
    $statement->execute(array($id));
    // Vu qu'il n'y a qu'une ligne, on va la stocker dans une variable $item. Pas besoin de faire de groupe vu qu'il n'y a qu'une ligne => fetch
    $item = $statement->fetch();
    // Et quand on a fini, on va déconnecter notre $database car ça ne sert à rien de laisser une connection ouverte.
    Database::disconnect();







    // Fonction checkInput (elle prend un paramètre, $data par exemple) qui vérifie plusieurs choses 
    function checkInput($data)
    {
        // Au cas où quelqu'un est mal intentionné, je lui enlève cette mauvaise intension. ça va pas marcher mais au moins, ça lui évitera de faire des problèmes sur mon site.
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data; // Je retourne le $data une fois que je l'ai nettoyé et que je suis sur qu'il n'y a plus de problème.
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

                <div class="col-sm-6">
                    <h1><strong>Voir un item</h1>
                    <br>
                    <form>
                        <div class="form-group">
                            <label>Nom:</label><?php echo ' ' . $item['name'];?>
                        </div> 
                        <div class="form-group">
                            <label>Description:</label><?php echo ' ' . $item['description'];?>
                        </div> 
                        <div class="form-group">
                            <label>Prix:</label><?php echo ' ' . number_format((float)$item['price'],2,'.','') . ' €'; ?>  <!-- Fonction number_format => pour mettre 2 chiffres après la virgule. Au cas ou le prix est pas un float je le transforme en float(avec des décimals) Elle à 4 arguments(valeur,combien de chiffre après la virgule,ce qui sépare les entier des décimals c'est un point,vide pour quand un chiffre est supérieur à 1000 des fois on peut mettre une virgule pour dire c'est 3,000 qui signifie 3000. Nous on va laisser vide) -->
                        </div> 
                        <div class="form-group">
                            <label>Categorie:</label><?php echo ' ' . $item['category'];?>
                        </div> 
                        <div class="form-group">
                            <label>Image:</label><?php echo ' ' . $item['image'];?>
                        </div>    
                    </form>
                    <br>
                    <div class="form-actions"> <!-- Les actions de se formulaires -->
                        <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a>
                    </div>

                    
                </div> <!-- Fermeture de col-sm-6 -->

                <div class="col-sm-6 site">
                            <div class="thumbnail"><!-- thumbnail pour donner un effet autour des éléments. -->
                               <img src="<?php echo '../images/' . $item['image'] ; ?>" alt="...">
                                <div class="price"><?php echo number_format((float)$item['price'],2,'.','') . ' €'; ?></div>
                                <div class="caption"> <!-- caption = Pour dans la thumbnail, mettre tout les éléments en dessous de l'image. -->
                                    <h4><?php echo $item['name'];?></h4>
                                    <p><?php echo $item['description'];?></p>
                                    <a href="#" class="btn btn-order" role="button"><!--btn-order => ça c'est nous qui allons décider de lui donner un nom et après comme ça on pourra le changer dans le CSS -->
                                        <span class="glyphicon glyphicon-shopping-cart"></span><!-- Mettre l'icone du caddie -->
                                        Commander
                                    </a>
                                </div>  <!-- Fermeture de caption -->
                            </div>  <!-- Fermeture de thumbnail -->
                        
                </div> <!-- Fermeture de col-sm-6 -->
           
            </div> <!-- Fermeture de row -->

        </div><!-- Fermeture de  container admin -->


    </body>


</html>