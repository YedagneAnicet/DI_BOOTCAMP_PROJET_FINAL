<?php

require_once __DIR__ . '/../vendor/autoload.php';

require_once __DIR__ . '/../src/Models/Db.php';

use App\Models\DB;
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



//FIN TABLE UTILISATEURS

$app->post('api/utilisateurs/inscription', function (Request $request, Response $response, array $args) {
    // Récupérer les données du formulaire d'inscription
    $data = $request->getParsedBody();
    $nom = $data['nom'];
    $prenoms = $data['prenoms'];
    $adresse = $data['adresse'];
    $ville = $data['ville'];
    $telephone = $data['telephone'];
    $email = $data['email'];
    $mot_de_passe = password_hash($data['mot_de_passe'], PASSWORD_DEFAULT); // hachage du mot de passe

    // Enregistrer l'utilisateur dans la base de données
    $sql = "INSERT INTO utilisateurs (nom_utilisateur, prenoms_utilisateur, adresse_utilisateur,ville_utilisateur,telephone_utilisateur, email_utilisateur, mot_de_passe) VALUES (:nom, :prenoms, :adresse, :ville, :telephone, :email, :mdp)";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nom', $name);
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





//FIN DE L'API





$app->run();