<?php

Header('Access-Control-Allow-Origin : *');
Header('Access-Control-Allow-Headers : *');
Header('Access-Control-Allow-Methods : GET, POST, PUT, DELETE, PATCH, OPTIONS');


require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../src/Models/Db.php';

use App\Models\DB;
use Firebase\JWT\JWT;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathMiddleware;
use Slim\Factory\AppFactory;


$app = AppFactory::create();
$app->addBodyParsingMiddleware();

$app->addRoutingMiddleware();
$app->add(new BasePathMiddleware($app));
$app->addErrorMiddleware(true, true, true);


// DEBUT DE MON API 

//ACTION SUR LA TABLE UTILISATEURS


$app->post('/api/utilisateurs/inscription', function (Request $request, Response $response, array $args) {
    // Récupérer les données du formulaire d'inscription
    $data = $request->getParsedBody();
    $nom = $data["nom"];
    $prenoms = $data["prenoms"];
    $adresse = $data["adresse"];
    $ville = $data["ville"];
    $telephone = $data["telephone"];
    $email = $data["email"];
    $mot_de_passe = password_hash($data['mdp'], PASSWORD_DEFAULT);


    // Enregistrer l'utilisateur dans la base de données
    $sql = "INSERT INTO utilisateurs (nom_utilisateur, prenoms_utilisateur, adresse_utilisateur,ville_utilisateur,telephone_utilisateur, email_utilisateur, mot_de_passe) VALUES (:nom, :prenoms, :adresse, :ville, :telephone, :email, :mdp)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom', $nom);
        $stmt->bindParam(':prenoms', $prenoms);
        $stmt->bindParam(':adresse', $adresse);
        $stmt->bindParam(':ville', $ville);
        $stmt->bindParam(':telephone', $telephone);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':mdp', $mot_de_passe);

        $result = $stmt->execute();

        $db = null; //pour deconnecter la bd

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});



$app->post('/api/utilisateurs/connexion', function (Request $request, Response $response, array $args) {
    // Récupérer les informations d'identification de l'utilisateur
    $data = $request->getParsedBody();
    $email = $data["email"];
    $mot_de_passe = $data["mdp"];

    // Vérifier les informations d'identification de l'utilisateur
    $sql = "SELECT * FROM utilisateurs WHERE email_utilisateur=:email LIMIT 1";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':email', $email);

        $stmt->execute();

        $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);

        $db = null; //pour deconnecter la bd

        // Vérifier si l'utilisateur existe et si le mot de passe est correct
        if (!$utilisateur || !password_verify($mot_de_passe, $utilisateur['mot_de_passe'])) {
            throw new Exception('Adresse e-mail ou mot de passe incorrect.');
        }

        // Générer un jeton d'authentification
        $jeton_payload = array(
            'id_utilisateur' => $utilisateur['id_utilisateur'],
            'nom' => $utilisateur['nom_utilisateur'],
            'email' => $utilisateur['email_utilisateur']
        );

        $secret_key = bin2hex(random_bytes(16)); // génère une clé secrète de 32 caractères hexadécimaux (16 octets)

        $jeton = JWT::encode($jeton_payload, $secret_key, 'HS256');

        $response_data = array(
            'jeton' => $jeton
        );
        $response->getBody()->write(json_encode($response_data));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);

    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    } catch (Exception $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(401);
    }
});


$app->post('/api/utilisateurs/deconnexion', function (Request $request, Response $response, array $args) {
    // Récupérer le jeton d'authentification dans les en-têtes de la requête
    $jeton = $request->getHeader('Authorization')[0] ?? '';

    // Vérifier si le jeton est présent
    if (!$jeton) {
        $error = array(
            "message" => "Vous devez être connecté pour pouvoir vous déconnecter."
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(401);
    }

    try {
        // Décoder le jeton et récupérer l'ID de l'utilisateur
        $secret_key = bin2hex(random_bytes(16));
        $jeton_payload = JWT::decode($jeton, $secret_key, 'HS256');
        $id_utilisateur = $jeton_payload->id_utilisateur;

        // Supprimer le jeton d'authentification stocké côté client
        setcookie('jeton', '', time() - 3600, '/');

        $response_data = array(
            'message' => 'Vous avez été déconnecté avec succès.'
        );
        $response->getBody()->write(json_encode($response_data));
        return $response

            ->withHeader('content-type', 'application/json')
            ->withStatus(200);

    } catch (Exception $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(401);
    }
});

//FIN TABLE UTILISATEURS



//ACTION SUR LA TABLE PHARMACIE 


//ajouter une nouvelle pharmacie 

$app->post('/api/pharmacies/add', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();

    $nom = $data['nom_pharmacie'];
    $adresse = $data['adresse_pharmacie'];
    $ville = $data['ville_pharmacie'];
    $telephone = $data['telephone_pharmacie'];
    $role = $data['role'];
    $garde = $data['de_garde'];

    // Vérification des paramètres obligatoires
    if (!isset($nom) || !isset($adresse) || !isset($ville) || !isset($telephone)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    if (!isset($role)) {
        $role = 0;
    }

    if (!isset($garde)) {
        $garde = 0;
    }

    $sql = "INSERT INTO pharmacies (nom_pharmacie, adresse_pharmacie, ville_pharmacie, telephone_pharmacie, role, de_garde) VALUES (:nom_pharmacie, :adresse_pharmacie, :ville_pharmacie, :telephone_pharmacie,:role, :de_garde)";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom_pharmacie', $nom);
        $stmt->bindParam(':adresse_pharmacie', $adresse);
        $stmt->bindParam(':ville_pharmacie', $ville);
        $stmt->bindParam(':telephone_pharmacie', $telephone);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':de_garde', $garde);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_pharmacie" => $lastInsertId,
            "message" => "Pharmacie ajoutée avec succès"
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//selectionner la liste de toute les pharmacie

$app->get('/api/pharmacies/all', function (Request $request, Response $response, array $args) {

    $sql = "SELECT * FROM pharmacies";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $pharmacies = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($pharmacies));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//selection une pharmacie avec son id 

$app->get('/api/pharmacies/{id}', function (Request $request, Response $response, array $args) {

    $pharmacyId = $request->getAttribute('id');

    $sql = "SELECT * FROM pharmacies WHERE id_pharmacie = :id_pharmacie";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_pharmacie', $pharmacyId);
        $stmt->execute();
        $pharmacy = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($pharmacy) {
            $response->getBody()->write(json_encode($pharmacy));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La pharmacie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//selectionner la liste de tous les pharmacie de garde 

$app->get('/api/pharmacies/all/garde', function (Request $request, Response $response, array $args) {

    $sql = "SELECT * FROM pharmacies WHERE de_garde = 1";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $pharmacies = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($pharmacies));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

//mettre en ajour une pharmacie 

$app->put('/api/pharmacies/update/{id}', function (Request $request, Response $response, array $args) {
    $pharmacyId = $request->getAttribute('id');

    $data = $request->getParsedBody();

    $nom = $data['nom_pharmacie'];
    $adresse = $data['adresse_pharmacie'];
    $ville = $data['ville_pharmacie'];
    $telephone = $data['telephone_pharmacie'];
    $role = $data['role'];
    $garde = $data['de_garde'];

    $sql = "UPDATE pharmacies SET nom_pharmacie = :nom_pharmacie, adresse_pharmacie = :adresse_pharmacie, ville_pharmacie = :ville_pharmacie, telephone_pharmacie = :telephone_pharmacie, role = :role, de_garde = :de_garde WHERE id_pharmacie = :id_pharmacie";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom_pharmacie', $nom);
        $stmt->bindParam(':adresse_pharmacie', $adresse);
        $stmt->bindParam(':ville_pharmacie', $ville);
        $stmt->bindParam(':telephone_pharmacie', $telephone);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':de_garde', $garde);
        $stmt->bindParam(':id_pharmacie', $pharmacyId);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "La pharmacie a été mise à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La pharmacie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//supprimer une pharmacie 

$app->delete('/api/pharmacies/delete/{id}', function (Request $request, Response $response, array $args) {
    $pharmacyId = $args['id'];
    $sql = "DELETE FROM pharmacies WHERE id_pharmacie = :id_pharmacie";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_pharmacie', $pharmacyId);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "La pharmacie a été supprimée avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La pharmacie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//FIN TABLE PHARMACIE



//ACTION SUR LA TABLE CATEGORIE 

//ajouter une nouvelle categorie

$app->post('/api/categories/add', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();

    $nom = $data['nom_categorie'];


    // Vérification des paramètres obligatoires
    if (!isset($nom)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response->withHeader('Access-Control-Allow-Origin', '*')
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }


    $sql = "INSERT INTO categories (nom_categorie) VALUES (:nom_categorie)";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom_categorie', $nom);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_categorie" => $lastInsertId,
            "message" => "Categorie ajoutée avec succès"
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

//selectionner tous les categories 
$app->get('/api/categories/all', function (Request $request, Response $response, array $args) {

    $sql = "SELECT * FROM categories";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($categories));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//selectionner une categories par son id 
$app->get('/api/categories/{id}', function (Request $request, Response $response, array $args) {

    $categorieId = $request->getAttribute('id');

    $sql = "SELECT * FROM categories WHERE id_categorie = :id_categorie";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_categorie', $categorieId);
        $stmt->execute();
        $categories = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($categories) {
            $response->getBody()->write(json_encode($categories));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La categorie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//mettre en ajour une categorie 

$app->put('/api/categories/update/{id}', function (Request $request, Response $response, array $args) {
    $categorieId = $request->getAttribute('id');

    $data = $request->getParsedBody();

    $nom = $data['nom_categorie'];


    $sql = "UPDATE categories SET nom_categorie = :nom_categorie WHERE id_categorie = :id_categorie";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom_categorie', $nom);
        $stmt->bindParam(':id_categorie', $categorieId);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "La categorie a été mise à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La categorie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//supprimer une categorie 

$app->delete('/api/categories/delete/{id}', function (Request $request, Response $response, array $args) {
    $categorieId = $args['id'];
    $sql = "DELETE FROM categories WHERE id_categorie = :id_categorie";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_categorie', $categorieId);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "La categorie a été supprimée avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La categorie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

//récupérer les produits par catégorie

$app->get('/api/categories/{id_categorie}/produits', function (Request $request, Response $response, array $args) {
    $id_categorie = $args['id_categorie'];
    $sql = "SELECT * FROM produits WHERE id_categorie = :id_categorie";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_categorie', $id_categorie);
        $stmt->execute();
        $produits = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        if ($produits) {
            $response->getBody()->write(json_encode($produits));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Aucun produit trouvé pour cette catégorie"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

//FIN TABLE CATEGORIE



// ACTION SUR LA TABLE PRODUIT

//ajouter une nouvelle produit 

$app->post('/api/produits/add', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();

    $nom = $data['nom_produit'];
    $description = $data['description_produit'];
    $image = $data['image_produit'];
    $prix = $data['prix_produit'];
    $quantite = $data['quantite_produit'];
    $categorie = $data['nom_categorie'];

    // Vérification des paramètres obligatoires
    if (!isset($nom) || !isset($description) || !isset($image) || !isset($prix) || !isset($quantite)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }


    $sql = "INSERT INTO produits (nom_produit, description_produit, image_produit, prix_produit, quantite_stock, id_pharmacie, id_categorie)
    VALUES (:nom_produit, :description_produit, :image_produit, :prix_produit, :quantite_stock, (SELECT id_pharmacie FROM pharmacies WHERE role = 1), (SELECT id_categorie FROM categories WHERE nom_categorie = :nom_categorie))";


    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom_produit', $nom);
        $stmt->bindParam(':description_produit', $description);
        $stmt->bindParam(':image_produit', $image);
        $stmt->bindParam(':prix_produit', $prix);
        $stmt->bindParam(':quantite_stock', $quantite);
        $stmt->bindParam(':nom_categorie', $categorie);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_produit" => $lastInsertId,
            "message" => "produit ajoutée avec succès"
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

//selectionner tous les produits
$app->get('/api/produits/all', function (Request $request, Response $response, array $args) {

    $sql = "SELECT * FROM produits";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->query($sql);
        $produits = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;

        $response->getBody()->write(json_encode($produits));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(200);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

//selection une produit avec son id 

$app->get('/api/produits/{id}', function (Request $request, Response $response, array $args) {

    $produitId = $request->getAttribute('id');

    $sql = "SELECT * FROM produits WHERE id_produit = :id_produit";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_produit', $produitId);
        $stmt->execute();
        $produit = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($produit) {
            $response->getBody()->write(json_encode($produit));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La pharmacie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// mettre a jour un produits
$app->put('/api/produits/update/{id}', function (Request $request, Response $response, array $args) {
    $produitId = $request->getAttribute('id');
    $data = $request->getParsedBody();

    $nom = $data['nom_produit'];
    $description = $data['description_produit'];
    $image = $data['image_produit'];
    $prix = $data['prix_produit'];
    $quantite = $data['quantite_produit'];
    $categorie = $data['nom_categorie'];


    $sql = "UPDATE produits SET nom_produit =:nom_produit, description_produit =:description_produit , image_produit= :image_produit, prix_produit=:prix_produit, quantite_stock=:quantite_stock, id_pharmacie =(SELECT id_pharmacie FROM pharmacies WHERE role = 1), id_categorie =(SELECT id_categorie FROM categories WHERE nom_categorie = :nom_categorie) WHERE id_produit = :id_produit";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom_produit', $nom);
        $stmt->bindParam(':description_produit', $description);
        $stmt->bindParam(':image_produit', $image);
        $stmt->bindParam(':prix_produit', $prix);
        $stmt->bindParam(':quantite_stock', $quantite);
        $stmt->bindParam(':nom_categorie', $categorie);
        $stmt->bindParam(':id_produit', $produitId);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "La pharmacie a été mise à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La pharmacie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


// supprimer un produit

$app->delete('/api/produits/delete/{id}', function (Request $request, Response $response, array $args) {
    $produitId = $args['id'];
    $sql = "DELETE FROM produits WHERE id_produit = :id_produit";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_produit', $produitId);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Le produit a été supprimée avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "Le produit est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});
// FIN TABLE PRODUITS



// ACTION SUR LA TABLE ARTICLE



//ajouter un nouvel article 

$app->post('/api/articles/add', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();

    $titre = $data['titre_article'];
    $description = $data['description_article'];
    $image = $data['image_article'];
    $date = $data['date_creation'];
    $nombre_like = $data['nombre_like'];

    // Vérification des paramètres obligatoires
    if (!isset($titre) || !isset($description) || !isset($image) || !isset($date) || !isset($nombre_like)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }


    $sql = "INSERT INTO articles_blog (titre_artcile, description_description, image_article, date_creation, nombre_like, id_pharmacie)
    VALUES (:titre_article, :description_article, :image_article, :date_creation, :nombre_like, (SELECT id_pharmacie FROM pharmacies WHERE role = 1))";


    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':titre_article', $titre);
        $stmt->bindParam(':description_article', $description);
        $stmt->bindParam(':image_article', $image);
        $stmt->bindParam(':date_creation', $date);
        $stmt->bindParam(':nombre_like,', $nombre_like);

        $stmt->execute();
        $lastInsertId = $conn->lastInsertId();
        $db = null;

        $responseBody = array(
            "id_article" => $lastInsertId,
            "message" => "article ajoutée avec succès"
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


//selection un article avec son id 

$app->get('/api/articles/{id}', function (Request $request, Response $response, array $args) {

    $articleId = $request->getAttribute('id');

    $sql = "SELECT * FROM articles_blog WHERE id_article = :id_article";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_article', $articleId);
        $stmt->execute();
        $article = $stmt->fetch(PDO::FETCH_OBJ);
        $db = null;

        if ($article) {
            $response->getBody()->write(json_encode($article));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "La pharmacie est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});



// mettre a jour un article

$app->put('/api/article/update/{id}', function (Request $request, Response $response, array $args) {
    $articleId = $request->getAttribute('id');
    $data = $request->getParsedBody();

    $titre = $data['titre_article'];
    $description = $data['description_article'];
    $image = $data['image_article'];
    $date = $data['date_creation'];
    $nombre_like = $data['nombre_like'];

    $sql = "UPDATE articles_blog SET titre_artcile =:titre_article, description_article =:description_article , image_article= :image_article, date_creation=:date_creation, nombre_like=:nombre_like, id_pharmacie =(SELECT id_pharmacie FROM pharmacies WHERE role = 1)";


    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':titre_article', $titre);
        $stmt->bindParam(':description_article', $description);
        $stmt->bindParam(':image_article', $image);
        $stmt->bindParam(':date_creation', $date);
        $stmt->bindParam(':nombre_like,', $nombre_like);
        $stmt->bindParam(':id_article', $articleId);

        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "L'article a été mise à jour avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "L'article est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});


// supprimer un produit

$app->delete('/api/articles/delete/{id}', function (Request $request, Response $response, array $args) {
    $articleId = $args['id'];
    $sql = "DELETE FROM article_blog WHERE id_article = :id_article";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id_article', $articleId);
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        $db = null;

        if ($rowCount > 0) {
            $response->getBody()->write(json_encode(array("message" => "Le produit a été supprimée avec succès.")));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(200);
        } else {
            $error = array(
                "message" => "L'article est introuvable"
            );
            $response->getBody()->write(json_encode($error));
            return $response
                ->withHeader('content-type', 'application/json')
                ->withStatus(404);
        }
    } catch (PDOException $e) {
        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});

// FIN TABLE ARTICLE 



// ACTION SUR LA TABLE COMMANDE

// pour enregistrer une commande 

$app->post('/api/commandes_add', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();

    $produits = $data['produits']; // tableau d'objets contenant id_produit et quantite_produit
    $prix_total = $data['prix_total'];
    $id_utilisateur = $data['id_utilisateur'];

    // Vérification des paramètres obligatoires
    if (!isset($produits) || !isset($prix_total) || !isset($id_utilisateur)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    $statut_commande = "En cours";

    $sql_commande = "INSERT INTO commandes (statut_commande, prix_commande, id_utilisateur)
    VALUES (:statut_commande, :prix_commande, :id_utilisateur)";

    $sql_commande_produit = "INSERT INTO commande_produit (id_commande, id_produit, quantite_produit)
    VALUES (:id_commande, :id_produit, :quantite_produit)";

    try {
        $db = new DB();
        $conn = $db->connect();
        $conn->beginTransaction();

        // Enregistrement de la commande
        $stmt_commande = $conn->prepare($sql_commande);

        $stmt_commande->bindParam(':statut_commande', $statut_commande);
        $stmt_commande->bindParam(':prix_commande', $prix_total);
        $stmt_commande->bindParam(':id_utilisateur', $id_utilisateur);

        $stmt_commande->execute();
        $lastInsertId = $conn->lastInsertId();

        // Enregistrement des produits de la commande
        $stmt_commande_produit = $conn->prepare($sql_commande_produit);

        foreach ($produits as $produit) {
            $id_produit = $produit['id_produit'];
            $quantite_produit = $produit['quantite_produit'];

            $stmt_commande_produit->bindParam(':id_commande', $lastInsertId);
            $stmt_commande_produit->bindParam(':id_produit', $id_produit);
            $stmt_commande_produit->bindParam(':quantite_produit', $quantite_produit);

            $stmt_commande_produit->execute();
        }

        $conn->commit();
        $db = null;

        $responseBody = array(
            "id_commande" => $lastInsertId,
            "message" => "Commande enregistrée avec succès"
        );

        $response->getBody()->write(json_encode($responseBody));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(201);
    } catch (PDOException $e) {
        $conn->rollBack();

        $error = array(
            "message" => $e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(500);
    }
});



// FIN TABLE COMMANDE



//FIN DE L'API

$app->run();