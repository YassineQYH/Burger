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


                        <tr>
                            <td>Item 1</td>
                            <td>Description 1</td>
                            <td>Prix 1</td>
                            <td>Catégorie 1</td>
                            <td width=300>  <!-- btn-default => blanc / btn-primary => bleu / btn-danger => rouge -->
                                <a class="btn btn-default" href="view.php?id=1"><span class="glyphicon glyphicon-eye-open"></span> Voir</a>
                                <a class="btn btn-primary" href="update.php?id=1"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>
                                <a class="btn btn-danger" href="delete.php?id=1"><span class="glyphicon glyphicon-remove"></span> Supprimer</a>
                            </td>
                        </tr>

                        <tr>
                            <td>Item 2</td>
                            <td>Description 2</td>
                            <td>Prix 2</td>
                            <td>Catégorie 2</td>
                            <td width=300>
                                <a class="btn btn-default" href="view.php?id=2"><span class="glyphicon glyphicon-eye-open"></span> Voir</a>
                                <a class="btn btn-primary" href="update.php?id=2"><span class="glyphicon glyphicon-pencil"></span> Modifier</a>
                                <a class="btn btn-danger" href="delete.php?id=2"><span class="glyphicon glyphicon-remove"></span> Supprimer</a>
                            </td>
                        </tr>


                    </tbody>
                </table>
            </div>

        </div>


    </body>


</html>