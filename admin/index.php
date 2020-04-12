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

    <body>  <!-- Création de l’Admin + Admin – Liste des items -->

        <h1 class="text-logo"><!-- TITRE -->
            <span class="glyphicon glyphicon-cutlery"></span><!-- Logo de fourchette et du couteau -->
            Burger Code
            <span class="glyphicon glyphicon-cutlery"></span>
        </h1>
        <div class="container admin">
            <div class="row">
                <h1><strong>Liste des items <a href="insert.php" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-plus"> Ajouter</a></h1>

                <table class="table table-striped table-bordered"> <!-- table => Le tableau // table-striped => C'est ce qui fait qu'il y a une ligne grise et une ligne blanche à chaque fois. --> 
                    <thead><!-- Cette table est composé du header = C'est la 1ere ligne, là où on met le nom des colonnes  -->
                        <tr><!-- tr => Une ligne // C'est comme row-->
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Prix</th>
                            <th>Catégorie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody><!-- Cette table est aussi composé d'un corp. -->


                        <!-- <tr>       PARTIE STATIQUE
                            <td>Item 1</td>
                            <td>Description 1</td>
                            <td>Prix 1</td>
                            <td>Catégorie 1</td> -->
                            <!-- <td width=300> -->  <!-- btn-default => blanc / btn-primary => bleu / btn-danger => rouge -->
                            <!-- <a class="btn btn-default" href="view.php?id=1"><span class="glyphicon glyphicon-eye-open"></span> Voir</a>
                                <a class="btn btn-primary" href="update.php?id=1"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>
                                <a class="btn btn-danger" href="delete.php?id=1"><span class="glyphicon glyphicon-remove"></span> Supprimer</a>
                            </td>
                        </tr> -->

                        <?php   
                        /* PARTIE DYNAMIQUE */
                            // On require le fichier database.php pour pouvoir utiliser son contenu
                            require 'database.php'; /* La différence entre require et include c'est que si le fichier n'existe pas, il arrête tout. */

                            /* Sur le fichier database.php on avait une fonction statique connect() qui nous retournait la connection à la BDD, Donc on va utiliser ça, comment ?  */
                            /* On va créer une variable db qui sera égale à la fonction connect() de la classe database. Comme c'est c'est une fonction statique, j'y accède en donnant le nom de la class et en mettant 2x deux point :: et on peut l'utiliser ici car elle est public. */
                            $db = Database::connect(); // Donc là, ça nous à retourner la connection dans la variable $db

                            // Maintenant, on va récupérer les résultats de notre requête car dans cette page, je veux donner la liste des items. CAD La commande SQL que je veux faire dans ma BDD c'est de sélectionner des informations. je veux donc faire un SELECT.
                            // On va créer une autre variable $statement et on va lui dire $db->query et mettre notre requête SQL la dedans. 
                            $statement = $db->query('SELECT items.id, items.name, items.description, items.price, categories.name AS category
                                                    FROM items LEFT JOIN categories ON items.category = categories.id
                                                    ORDER BY items.id DESC'); // Pour la requête SQL on a besoin des champs : name / description / price / category (pas l'id mais le nom) qu'on va devoir relier au name qui se trouve dans la table categories donc on va le relier au champ id/ et l'id de l'item.

                            // Quand j'ai récupérer toute les lignes et les colonnes dont j'ai besoin pour ma page index.php 
                            // Et maintenant, je veux les afficher. Comment ? Avec un while.
                            while($item = $statement->fetch()) // fetch() => récupère moi juste une ligne
                            {   // Comme je fais une boucle avec cette condition : ($item = $statement->fetch(), à chaque fois il va repasser et récupérer la ligne d'après.
                                echo '<tr>';
                                echo '<td>' . $item['name'] . '</td>';  // Je concatène avec une information qui vient de la BDD. Ici, on a dit que la ligne s'appele $item, je vais mettre la variable que j'ai envie d'afficher, la variable c'est le champ nom que j'ai mis dans ma BDD
                                echo '<td>' . $item['description'] . '</td>'; // Ici, le champ s'appelait description.
                                echo '<td>' . $item['price'] . '</td>';
                                echo '<td>' . $item['category'] . '</td>';
                                echo '<td width=300>';  /* btn-default => blanc / btn-primary => bleu / btn-danger => rouge */
                                echo '<a class="btn btn-default" href="view.php?id=' . $item['id'] . '"><span class="glyphicon glyphicon-eye-open"></span> Voir</a>'; // La, je vais lui donner des id car, je veux voir un item donc je veux updater donc ce qui m'importe c'est la ligne spécifique, je la désigne avec l'id
                                echo '  ';
                                echo '<a class="btn btn-primary" href="update.php?id=' . $item['id'] . '"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>';
                                echo '  ';
                                echo '<a class="btn btn-danger" href="delete.php?id=' . $item['id'] . '"><span class="glyphicon glyphicon-remove"></span> Supprimer</a>';
                                echo '</td>';
                                echo '</tr>';
                            }
                        ?>





                    </tbody>
                </table>
            </div>

        </div>


    </body>


</html>