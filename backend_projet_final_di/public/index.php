<?php

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

$app->post('/api/pharmacies_add', function (Request $request, Response $response, array $args) {

    $data = $request->getParsedBody();
    
    $nom = $data['nom_pharmacie'];
    $adresse = $data['adresse_pharmacie'];
    $ville = $data['ville_pharmacie'];
    $telephone = $data['telephone_pharmacie'];
    $email = $data['email_pharmacie'];
    $role = $data['role'];
    $garde = $data['de_garde'];

    // Vérification des paramètres obligatoires
    if (!isset($nom) || !isset($adresse) || !isset($ville) || !isset($telephone) || !isset($email)) {
        $error = array(
            "message" => "Tous les champs obligatoires doivent être fournis"
        );
        $response->getBody()->write(json_encode($error));
        return $response
            ->withHeader('content-type', 'application/json')
            ->withStatus(400);
    }

    if(!isset($role)){
        $role = 0;
    }

    if(!isset($garde)){
        $garde = 0;
    }

    $sql = "INSERT INTO pharmacies (nom_pharmacie, adresse_pharmacie, ville_pharmacie, telephone_pharmacie, email_pharmacie, role, de_garde) VALUES (:nom_pharmacie, :adresse_pharmacie, :ville_pharmacie, :telephone_pharmacie, :email_pharmacie, :role, :de_garde)";

    try {
        $db = new DB();
        $conn = $db->connect();
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':nom_pharmacie', $nom);
        $stmt->bindParam(':adresse_pharmacie', $adresse);
        $stmt->bindParam(':ville_pharmacie', $ville);
        $stmt->bindParam(':telephone_pharmacie', $telephone);
        $stmt->bindParam(':email_pharmacie', $email);
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

$app->get('/api/pharmacies_garde', function (Request $request, Response $response, array $args) {

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









//FIN TABLE PHARMACIE





//FIN DE L'API





$app->run();