<?php
session_start();
if(!isset($_SESSION['email']))
{
    header("Location: login.php");
}

    require 'database.php';

//  On récupère le id avec la méthode GET
//  Quand je clique sur modifier dans la page Admin il est écrit dans l'url
//  update.php?id=30
//  La, il s'agit du 1er passage (la 1ere fois que j'arrive à la page update.php).

if(!empty($_GET['id']))
{   // La on va aussi faire un checkInput de ($_GET['id']) pour le nettoyer, question de sécurité.
    $id = checkInput($_GET['id']); // Maintenant, on a l'id qu'on veut avoir dans la variable $id
}



    $nameError = $descriptionError = $priceError = $categoryError = $imageError = $name = $description = $price = $category = $image ="";


    //  La, ce code, il va être pour la 2eme fois que je passe sur la page update.php
    if(!empty($_POST))  // Si la méthode POST est engagé, ça veut dire que je viens d'appuyer sur le bouton modifier | Le update.php retourner sur le update.php mais il a rempli la SG global POST et la je vais récupérer mes informations.
    {   
        $name               = checkInput($_POST['name']);
        $description        = checkInput($_POST['description']);
        $price              = checkInput($_POST['price']);
        $category           = checkInput($_POST['category']); 
        $image              = checkInput($_FILES["image"]["name"]);
        $imagePath          = '../images/'. basename($image);
        $imageExtension     = pathinfo($imagePath,PATHINFO_EXTENSION);
        $isSuccess          = true;
        /* $isUploadSuccess = false; Je le supprime car si je ne suis pas aller chercher une nouvelle image mais il y a déjà une image dans ma database donc ce champ peut être vide, ce n'est pas une erreur car je n'ai pas voulu updater mon image tout simplement. Je peux vouloir updater mon prix mais pas mon image */  

        if(empty($name))    
        {
            $nameError = "Ce champ ne peut pas être vide";
            $isSuccess = false; 
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
        if(empty($category))    
        {
            $categoryError = "Ce champ ne peut pas être vide";
            $isSuccess = false;
        }
        if(empty($image))    // le input file est vide, ce qui signifie que l'image n'a pas ete update
        {   // Est-ce que l'image à été updater/modifier
            $isImageUpdated = false; // Si je suis ici, c'est que c'est nom puisque le input file image est vide donc l'image n'a pas été updater. J'ai donc l'intention de laisser la même image qui était dans la BDD.
        }
        else
        {
            // Ensuite, si je suis dans le else c'est que oui j'ai l'intention de changer mon image, donc isImageUpdated est vrai
            $isImageUpdated = true;
            $isUploadSuccess = true;
            if($imageExtension != "jpg" && $imageExtension != "png" && $imageExtension != "jpeg" && $imageExtension != "gif")
            {
                $imageError = "Les fichiers autorisé sont : .jpg, .jpeg, .png, .gif";
                $isUploadSuccess = false;
            }
            if(file_exists($imagePath)) 
            {
                $imageError = "Le fichier existe déjà";
                $isUploadSuccess = false;
            }
            if($_FILES["image"]["size"] > 500000)   
            {
                $imageError = "Le fichier ne doit pas depasser les 500kb";
                $isUploadSuccess = false;
            }
            if($isUploadSuccess)   
            {       
                if(!move_uploaded_file($_FILES["image"]["tmp_name"], $imagePath)) 
                {  
                    $imageError = "Il y a eu une erreur lors de l'upload"; 
                    $isUploadSuccess = false;
                }
            }
        }

        // Ici, je vais changer les question à la fin car le isUploadSuccess n'est pas forcément vrai car j'ai besoin de parler de isUploadSuccess que si je suis rentrer dans le else
        // Donc la, on a 2 scénarios,
        // Ou je suis dans le cas où tout mes paramètres sont bon, donc ça sera un isSuccess qui sera égale à true
        //  Ensuite, je vais poser une question, si ton image elle a été updater alors le isUploadSuccess doit aussi être égale à true.
        //  Par contre si l'image n'a pas été updater je me contente juste du isSuccess, je n'ai pas besoin de savoir le isUploadSuccess

        /* 1er cas de succès : avoir un succès sur tout les champs et mettre à jour notre image*//* 2eme cas de succès : avoir un succès sur tout les champs et ne pas mettre à jour notre image. */
                            /* 1er cas de succès  */                    /* 2Eme cas de succès */
        if (($isSuccess && $isImageUpdated && $isUploadSuccess) || ($isSuccess && !$isImageUpdated)) 
        {   // Donc ici, on est dans les 2 cas de succès qu'on peut avoir.
            $db = Database::connect();
            //  ça va un petit peu différencier à l'intérieur de la fonction car dans les 2 cas on ne va pas faire exactement la même chose.
            //  Ici, on insère une image mais dans le cas ou l'image n'a pas été uptader j'ai pas besoin d'inséré une image et surtout je vais pas insérer des valeurs, je vais les updater.

            //  Est-ce que je suis dans le cas où l'image à été updater 
            if($isImageUpdated)
            {
                $statement = $db->prepare("UPDATE items  set name = ?, description = ?, price = ?, category = ?, image = ? WHERE id = ?");
                $statement->execute(array($name,$description,$price,$category,$image,$id)); // Rappel : ces variables on les a récupérer de notre formulaire, elles sont venu avec la méthode POST plus haut. | Donc la on a updater avec les valeurs que l'utilisateur nous a donner, donc la je suis sur le 2eme passage du formulaire.
            }
            else    // J'ai envie d'updater mes valeurs mais pas l'image.
            {
                $statement = $db->prepare("UPDATE items set name = ?, description = ?, price = ?, category = ? WHERE id = ?"); // Donc la je supprime image = ?
                $statement->execute(array($name,$description,$price,$category, $id)); // Et ici je supprime $image
            }
            Database::disconnect();
            header("Location: index.php"); // Comme on est dans un cas de succès, on retourne à l'index.php, CAD à la liste de tout nos items. 
        }

        // Si je ne suis pas dans un cas de succès, j'ai des erreurs
        // Dans le cas où tu as voulu updater ton image donc isImageUpdate = true 
        // Et que ton upload s'est mal passé donc $isUploadSuccess n'est pas true donc je met ! not
        else if($isImageUpdated && !$isUploadSuccess) // Je suis dans le cas ou l'image à été update mais pas d'upload, il y a donc eu un problème avec l'image.
        {
            $db = Database::connect();
            $statement = $db->prepare("SELECT * FROM items WHERE id = ?");
            $statement->execute(array($id));
            $item = $statement->fetch(); 
            $image = $item['image'];    // Je réinitialise ma variable image avec ce qu'il y a dans ma BDD | Permet de ne pas laisser le nom du mauvais fichier dans Image: nom-du-mauvais-fichier | Supprimer ceci et essayer de modifier l'image avec un fichier autre que image dans modifier un item pour voir ce que ça fait.

            Database::disconnect();
        }
    }

    else    // Ici, je suis dans le 1er passage | Donc maintenant je dois remplir mon formulaire avec les valeurs actuel de cet item qui sont dans ma BDD | Ma méthode post elle est vide donc je rentre pas dans tout le if précédent et j'arrive à ce else. Dans ce else je vais me connecter à la database et je vais récupérer les informations de cette ligne qui est dans le BDD car WHERE ID est égale au id que j'ai donnée dans l'array de l'execute et je vais remplir mes variables name/description/price etc avec les valeurs de ma BDD et toute ces valeurs vont s'afficher dans notre page.
    {
        $db = Database::connect();

        // J'ai envie de récupérer toute les informations sur l'item du quel j'ai attrapé l'id avec le GET 
        $statement = $db->prepare("SELECT * FROM items WHERE id = ?");
        $statement->execute(array($id));    // Donc la, on a récupérer toutes nos valeurs
        // Et la on va tout simplement remplir
        // On va faire un fetch dans une variable qu'on va appeler item
        $item = $statement->fetch();    // On va attrapé cette ligne qu'on a fait avec la cmd SQL et on va le mettre dans le $item | Donc on récupère une ligne qui est un array

        // Vu qu'on a mis toute la ligne dans la varible $item
        // Ce que j'affiche dans mon formulaire.
        $name           = $item['name'];
        $description    = $item['description'];
        $price          = $item['price'];
        $category       = $item['category'];
        $image          = $item['image'];

        Database::disconnect();
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
                <div class="col-sm-6">
                    <h1><strong>Modifier un item</h1>
                    <br>   
                    <form class="form" action="<?php echo 'update.php?id=' . $id; ?>" method="post" enctype="multipart/form-data">   <!-- Le lien php vers le quel il va pointer quand je clique sur submit ça sera lui même(la même page que lui). CAD quand je suis sur update.php, je vais donner mes valeurs de mon formulaire avec la méthode post à la page update.php => 'update.php | Par contre, contrairement au insert qui insert juste des valeurs, le update est un peu particulier. J'ai besoin de savoir l'id. Il y a plusieurs méthode, vu que cet id je l'ai récupérer avec le GET au départ, pour la 1ere fois qu'on à ouvert le update.php / Maintenant je parle pour la 2eme fois, quand on va récupérer ces informations. On a plusieurs façon, où on peut le faire passer par un input dans le formulaire(un input qui ne se vera pas à l'écran[on verra ça dans la page du delete] Ou tout simplement, on peut répéter l'opération et le mettre dans l'url et le récupérer avec la métphode GET. C'est ce qu'on va faire ici.) | l'id on va supposer qu'on l'a mis dans une variable id -->

                        <div class="form-group">
                            <label for="name">Nom:</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Nom" value="<?php echo $name; ?>">
                            <span class="help-inline"><?php echo $nameError; ?></span> 
                        </div> 

                        <div class="form-group">
                            <label for="description">Description:</label>
                            <input type="text" class="form-control" id="description" name="description" placeholder="Description" value="<?php echo $description; ?>">
                            <span class="help-inline"><?php echo $descriptionError; ?></span>
                        </div> 

                        <div class="form-group">
                            <label for="price">Price: (en €)</label>
                            <input type="number" step="0.01" class="form-control" id="price" name="price" placeholder="Prix" value="<?php echo $price; ?>"> 
                            <span class="help-inline"><?php echo $priceError; ?></span>
                        </div> 
                        
                        <div class="form-group">
                            <label for="category">Category:</label>
                            <select class="form-control" id="category" name="category">
                            <?php
                                $db = Database::connect();
                                foreach($db->query('SELECT * FROM categories') as $row) 
                                {   
                                    // Dans le insert.php, notre catégorie elle s'ouvre avec le 1er de la liste qui est séléctionné. Ici, je veux ouvrir cette page avec ses paramètre actuel, donc je dois dire ici sa catégorie.
                                    // Si row id est égale à l'id de la catégorie
                                    if($row['id'] == $category) // On va supposer qu'on a la catégorie de notre item dans une variable category comme on a fait pour notre item.
                                    // Alors je fais un echo selected="selected"
                                    echo '<option selected="selected" value="' . $row['id'] . '">' . $row['name'] . '</option>';    
                                    else
                                    // Et dans le cas où je ne suis pas dans la catégorie de mon item on va le laisser comme il était avant sans le selected="selected". 
                                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                                }
                                Database::disconnect();
                            ?>
                            </select>
                            <span class="help-inline"><?php echo $categoryError; ?></span>
                        </div> 

                        <div class="form-group">
                            
                        <label>Image:</label><!-- Je veux savoir quelle est la valeur de l'image actuel avant que tu veuille la changer.  -->
                        <p><?php echo $image; ?></p><!-- Et ensuite, je vais mettre la valeur de cette image | Je suppose encore que la valeur actuel qu'il y a dans la BDD c'est la valeur de l'image qu'on a mis dans une variable $image -->
                            <label for="image">Sélectionner une image:</label>  <!-- Ici, ça ne va pas changer car il va me permettre de séléctionner/modifier l'image qu'on a déjà mis -->
                            <input type="file" id="image" name="image">
                            <span class="help-inline"><?php echo $imageError; ?></span>
                        </div> 

                    
                        <br>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-success"><span class="glyphicon glyphicon-pencil"></span> Modifier</button>
                            <a class="btn btn-primary" href="index.php"><span class="glyphicon glyphicon-arrow-left"></span> Retour</a> 
                        </div>
                    </form>
                </div>
                
                <!-- 2eme colonne pour l'image de droite // Je reprend le code de la page view // Ce n'est plus $item['image'] mais $image et pareil pour description et price-->
                <div class="col-sm-6 site">
                            <div class="thumbnail"><!-- thumbnail pour donner un effet autour des éléments. -->
                               <img src="<?php echo '../images/' . $image; ?>" alt="...">
                                <div class="price"><?php echo number_format((float)$price,2,'.','') . ' €'; ?></div>
                                <div class="caption"> <!-- caption = Pour dans la thumbnail, mettre tout les éléments en dessous de l'image. -->
                                    <h4><?php echo $name;?></h4>
                                    <p><?php echo $description;?></p>
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