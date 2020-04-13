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
        <link rel="stylesheet" href="css/style.css">
    </head>

    <body>
        <div class="container site">

            <h1 class="text-logo"><!-- TITRE -->
                <span class="glyphicon glyphicon-cutlery"></span><!-- Logo de fourchette et du couteau -->
                Burger Code
                <span class="glyphicon glyphicon-cutlery"></span>
            </h1>

            <?php 
            require 'admin/database.php';
            echo '<nav>
                    <ul class="nav nav-pills">';    /* class="nav nav-pills" =>Permet de donner le style/effet de notre nav */
            $db = Database::connect();
            $statement = $db->query('SELECT * FROM categories');
            $categories = $statement->fetchAll();
            foreach($categories as $category)
            {
                if($category['id'] == '1')
                    echo '<li role="presentation" class="active"><a href="#'. $category['id'] .'" data-toggle="tab">'. $category['name'] .'</a></li>';  /* Son rôle c'est de présenter la table et il a l'id 1 // ACTIVE => ça sera l'onglet par défaut qui sera ouvert quand on rafraichis la page // data-toggle=tab => C'est les Onglet */
                else
                    echo '<li role="presentation"><a href="#'. $category['id'] .'" data-toggle="tab">'. $category['name'] .'</a></li>';

            }
            echo    '</ul>
                </nav>';

            echo '<div class="tab-content">';  /* contenues de cet onglet  */
            foreach($categories as $category)
            {
                if($category['id'] == '1')
                    echo '<div class="tab-pane active" id="'. $category['id'] .'">';  /* Pour dire que c'est cette partie la / le 1er onglet qui sera ouvert(active) et on lui donne l'id 1 */
                else
                    echo '<div class="tab-pane" id="'. $category['id'] .'">';

                echo '<div class="row">';  /* C'est une ligne  */

                $statement = $db->prepare('SELECT * FROM items WHERE items.category = ?');
                $statement->execute(array($category['id']));

                    while($item = $statement->fetch())  /* Pour avoir chacune des lignes des items j'utilise la fonction fetch() */
                    {   /* thumbnail pour donner un effet autour des éléments. */
                        /* caption = Pour dans la thumbnail, mettre tout les éléments en dessous de l'image.  */
                        /* btn-order => ça c'est nous qui allons décider de lui donner un nom et après comme ça on pourra le changer dans le CSS  */
                        /*  Mettre l'icone du caddie  */
                        echo '<div class ="col-sm-6 col-md-4">
                                <div class="thumbnail">   
                                <img src="images/'. $item['image'] . '" alt="...">
                                    <div class="price">' .number_format($item['price'],2,'.',''). '€</div> 
                                    <div class="caption">   
                                        <h4>' . $item['name'] . '</h4>
                                        <p>' . $item['description'] . '</p>
                                        <a href="#" class="btn btn-order" role="button">
                                            <span class="glyphicon glyphicon-shopping-cart"></span>
                                            Commander
                                        </a>
                                    </div>
                                </div>
                            </div>';
                    }
                    echo    '</div>    
                        </div>';    /* Les 2 fermeture de div correspondent au div ligne 45 et 49 */
            }
            Database::disconnect();

            

            ?>
      
                    
            </div><!-- Fermeture de "tab-content" -->

        </div><!-- Fermeture de class="container site" -->

    </body>

</html>