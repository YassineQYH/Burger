<?php
session_start();
if(!isset($_SESSION['email']))
{
    header("Location: login.php");
}

    require 'database.php';

    // J'écris toute les variables sur les erreurs qui vont nous donner les msg d'erreurs
    // Et toute les variables qui vont nous donner les valeurs.
    // La ce que je fais c'est pour la partie ou je repasse une 2eme fois
    // La partie où je passe la premiere fois je les initialise à rien du tout, elles sont vide.
    $nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image ="";

    //  Nos variables on les a envoyé la 1ere fois qu'on est arrivé sur insert.php, on les a rempli et après on les a renvoyer une 2eme fois au insert.php en soumettant notre formulaire avec la méthode post 
    //  On récupère des variables avec la méthode post en posant des questions sur la SG post 

    //  Donc on va lui demander si la SG post est vide.
    if(!empty($_POST))
    {   //  Si le POST n'est pas vide, ça veut dire que c'est pas mon 1er passage mais que c'est mon 2eme passage.
        //  Je dois remplir toutes mes variables ($name, $description, $price, $category, $image)
        $name = checkInput($_POST['name']); // Je la rempli avec le contenu de la SG POST qui est un array et je veux l'élément name, c'est celui qui avait le nom name dans notre formulaire. | Pour la sécurité, les informations qui viennent de l'extérieur on va ré utiliser notre fonction checkInput
        $description = checkInput($_POST['description']);
        $price = checkInput($_POST['price']);
        $category = checkInput($_POST['category']); // Rappel : category on a pris la valeur, c'est l'id qu'on veut.
        $image = checkInput($_FILES['image']['name']); // Rappel : on avait donner un input de type file. On récupère un input de type files avec la SG $_FILES. C'est pour ça qu'on avait mis enctype sur notre formulaire. Et la, ce qu'on doit chercher, c'est plusieurs choses car dans ce $_FILES c'est un array de array. Donc déjà, je vais aller chercher la variable image et je veux son nom dans cette variable (je veux le nom du fichier). Sur une image j'ai besoin de plusieurs choses, puisque je veux l'uploader cette image, je veux la mettre dans un dossier, j'ai besoin de pleins d'autres choses. Donc pour l'instant ce que je vais mettre dans la variable image c'est juste son nom. 

        // Par ex : j'ai aussi besoin de son chemin.
        $imagePath = '../images/' . basename($image);   // Je veux savoir dans quel dossier je veux le mettre (Je veux même savoir tout le chemin de l'image, tout son path) => ../images , et je veux les mettre dans quoi ? | basename() => fonction le nom de base du nom de l'image qu'on à stocker dans $image | ça, ça va me donner le nom de l'image.

        // Je veux savoir l'extension de l'image.
        $imageExtension = pathinfo($imagePath, PATHINFO_EXTENSION);  // Je la récupère avec la fonction pathinfo() et je lui donne le chemin complet de notre image, donc $imagePath et une constante qui est PATHINFO_EXTENSION | au final, ça va me donner l'extension de l'image(png, jpg, git, etc).

        // maintenant, je vais créer des autres fonctions qui vont me permettre de savoir si au final j'ai rempli mon formulaire avec succès.
        $isSuccess = true;  // Je l'initialise à true. Oui, ça été un succès et si on rencontre un problème ont dira non ce n'est plus un succès, on va la mettre à false et à la fin on fera l'action qu'on doit faire si ça été un succès. 

        // On va regarder ce qu'il s'est passé avec l'upload donc on va créer une autre variable qu'on va appeler, isUploadSuccess
        $isUploadSuccess = false;   // Est-ce que l'upload lui-même à été un succès. (En ce qui concerne l'image.) Pour le coup, on va l'initialiser à false.

        //  Maintenant, on va commencer à poser nos questions sur nos variables.
        //  Est-ce que le nom est vide ?
        if(empty($name))    // Si le nom est vide on va mettre dans la variable $nameError un msg d'erreur.
        {
            $nameError = "Ce champ ne peut pas être vide";
            $isSuccess = false; // Vu qu'il y a une erreur sur le nom, l'ensemble du formulaire à un problème. Donc ce n'est pas toujours un succès.
        }
        if(empty($description))    
        {
            $descriptionError = "Ce champ ne peut pas être vide";
            $isSuccess = false;
        }
        if(empty($price))    
        {
            $priceError = "Ce champ ne peut pas être vide";
            $isSuccess = false;
        }
        // Pour catégorie c'est spécial, par défaut il séléctionne le 1er élément donc en théorie il va jamais être vide. Mais bon, on va le mettre aussi vu qu'on fait la même chose pour tout.
        if(empty($category))    
        {
            $categoryError = "Ce champ ne peut pas être vide";
            $isSuccess = false;
        }
        // L'image est spécial. Si l'image n'est pas vide, il se pose pleins de questions. Donc la on répond avec un else. Si l'image n'est pas vide, qu'est-ce qu'on va poser comme autre question ?
        if(empty($image))    
        {
            $imageError = "Ce champ ne peut pas être vide";
            $isSuccess = false;
        }
        else
        {
            $isUploadSuccess = true;
            if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif")
            {
                $imageError = "Les fichiers autorisé sont : .jpg, .jpeg, .png, .gif";
                $isUploadSuccess = false;
            }
            if(file_exists($imagePath)) // imagePath => c'est le chemin de notre image / ça revient à dire : si l'image existe déjà (le même nom de fichier).
            {
                $imageError = "Le fichier existe déjà";
                $isUploadSuccess = false;
            }
            if($_FILES["image"]["size"] > 500000)   // Limité la taille de l'image pour ne pas avoir des images énormes et ainsi éviter donc que le site prenne trop de temps à charger.
            {
                $imageError = "Le fichier ne doit pas depasser les 500kb";
                $isUploadSuccess = false;
            }
            if($isUploadSuccess)    // Si on a passé les étapes de vérification précédente, isUploadSucces égale donc à true puisqu'on l'avait initialisé à true.
            {       // Alors tu vas commencer à travailler. | !move_upload_file => c'est une fonction qui va : $_FILES["image"]["tmp_name"] => prendre mon fichier 
                if(!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath))  // Pour le moment, notre fichier se trouve dans une chose temporaire donc ici, on va le définir avec le tmp_name (temps_name) Donc je vais dire : tu vas prendre cette image temporaire et tu vas me la mettre dans le vrai chemin que j'ai défini.
                {   // En plus de ça, cette fonction va me revoyer : est-ce que j'ai réussi ou pas ? La valeur de la fonction va valoir ou true ou false. Donc si elle est true, génial, tu continues la suite(car elle à réussi). Si elle est false, (c'est pour ça que j'ai écrit not move_upload_file) Donc il va entrer dans ce if et la (ok, il peut y avoir plusieurs style d'erreur, mais la on va pas rentrer dans tout les détails de toutes les erreurs) on va lui écrire il y à eu une erreur lors de l'upload, pour lui dire que ça n'a pas marché. (Il peut y avoir plusieurs types d'erreur : par exemple : la configuration de votre PHP vous permet pas de faire des upload / que les images qu'on a mis limite une taille moins importante / etc) Là, on veut au moins prévenir la personne qu'il y a eu une erreur et qu'il ne considère pas ça comme : oui c'est passé.
                    $imageError = "Il y a eu une erreur lors de l'upload";  // S'il a passé toutes les étapes de vérifiaction précédente mais qu'il y a quand même eu une erreur, on affiche ce msg d'erreur.
                    $isUploadSuccess = false;
                }
            }
        }

        //   J'écris un if pour l'ensemble
        //  Maintenant, ce que je veux vérifier c'est qu'on a pas eu de problème sur toute les valeurs hors image qu'on avait checké avec le $isSuccess
        //  Si isSuccess est égale à true et que isUploadSuccess est égale à true 
        //  Alors, on rajoute notre élément dans notre BDD
        //      On se connecte à notre BDD 
        //      Ensuite, on crée un statement 
        //      Le statement va préparer déjà 
        //      Tu vas m'insérer dans la table items à la colonne name tu vas mettre un point d'intérrogation, à la colonne description pareil, etc 
        //      Ensuite, quand on va executer ce statement on va le remplir avec les valeurs des variables qu'on a récupérer dans notre formulaire ($name, $description, etc etc)
        //      Et on oubli pas de se déconnecter.
        //  Si tout s'est bien passé, je veux ensuite retourner vers la page index.php voir la liste et voir cet élément que je viens de rajouter en haut de la liste.
        if($isSuccess && $isUploadSuccess)
        {
            $db = Database::connect();
            $statement = $db->prepare("INSERT INTO items (name,description,price, category,image) value(?, ?, ?, ?, ?)");
            $statement->execute(array($name,$description,$price,$category,$image));
            Database::disconnect();
            header("Location: index.php");  // la fonction header veut dire change moi l'adresse et mets moi index.php
        }
    }

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

                <h1><strong>Ajouter un item</h1>
                <br>
                <form class="form" action="insert.php" method="post" enctype="multipart/form-data">   <!-- Le formulaire renvoie les valeurs vers lui même/vers la même page. | La 1ere fois qu'on va atteindre cette page, ça sera pour remplir nos valeurs et puis quand on va appuyer sur Submit il va encore rediriger vers cette page mais avec les valeurs qui seront stocké dans ma super global post | Une petite variente vu qu'on va uploader une image, on va ajouter enctype(encryption type) et la on doit lui mettre multipart/form-data-->

                    <div class="form-group">
                        <label for="name">Nom:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo $name; ?>"><!-- form-control => c'est du bootstrap pour lui donner un style | name="name" => ça c'est le nom qu'on doit lui donner pour le récupérer avec le post ensuite |placeholder => c'est le text qu'il y a écrit dessus quand il est vide | Au départ la value elle sera vide mais la 2eme fois quand je vais revenir, on va créer une variable name et on mettra la valeur de cette variable dans cet input  -->
                        <span class="help-inline"><?php echo $nameError; ?></span> <!-- J'ajoute un span avec les msg d'erreurs, je vais donner le nom help-inline, c'est nous qui allons la créer. -->
                    </div> 

                    <div class="form-group">
                        <label for="description">Description:</label>
                        <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php echo $description; ?>">
                        <span class="help-inline"><?php echo $descriptionError; ?></span>
                    </div> 

                    <div class="form-group">
                        <label for="price">Price: (en €)</label>
                        <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Prix" value="<?php echo $price; ?>"> <!-- step => Permet de créer 2 petite fleche pour augmenter et diminuer le nombre / Il défini à chaque clique de combien il va augmenter ou diminuer. ici on a choisis 0.01-->
                        <span class="help-inline"><?php echo $priceError; ?></span>
                    </div> 
                     
                    <div class="form-group">
                        <label for="category">Category:</label>
                        <select class="form-control" id="category" name="category">
                        <?php
                            $db = Database::connect();
                            foreach($db->query('SELECT * FROM categories') as $row) // Pour changer on va cette fois utiliser un foreach à la place d'un while | Il va faire la query et à chacun des enregistrement, au lieu d'aller avec des fetchs il va nous les mettre dans un row / Donc à chaque fois qu'il va faire une boucle, directement sur les résultats de la query il va nous les mettre à chaque fois dans le row
                            {   
                                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';    // La on a envie que le value soit égale au id (1 c'est le menu, 2 c'est le burger, etc)| Et la j'écris ce qu'il y a dedans, CAD son nom, row c'est ce qui sera visible. | Pourquoi faire ça ? car après quand je vais vouloir enregistrer, je veux faire un insert, ce que j'ai envie de récupérer c'est l'id, c'est la valeur, donc je le met dans value. Par contre j'ai envie que l'utilisateur voit le nom donc je le met à l'intérieur du tag.  
                            }
                            Database::disconnect();
                        ?>
                        </select>
                        <span class="help-inline"><?php echo $categoryError; ?></span>
                    </div> 

                    <div class="form-group">
                        <label for="image">Sélectionner une image:</label>
                        <input type="file" id="image" name="image"> <!-- input de type file => il va récupérer un fichier -->
                        <span class="help-inline"><?php echo $imageError; ?></span>
                    </div> 

                
                    <br>
                    <div class="form-actions"> <!-- Les actions de se formulaires -->
                        <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Ajouter</button><!-- C'est un bouton de type submit car j'ai envie que quand je clique sur le bouton ajouter il soumette mon formulaire à lui même (à insert.php) -->
                        <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a> 
                    </div>
                </form>
            </div> <!-- Fermeture de row -->

        </div><!-- Fermeture de  container admin -->


    </body>


</html>