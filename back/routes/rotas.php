
<?php

require_once __DIR__ . "/../controllers/UsuarioController.php";
require_once __DIR__ . "/../controllers/PublicacaoController.php";
require_once __DIR__ . "/../controllers/CurtidaController.php";

$rota = $_GET["rota"] ?? "";
$metodo = $_SERVER["REQUEST_METHOD"];


// USUARIO
if ($rota === "usuario") {

    switch ($metodo) {

        case "POST":
            createUsuario($con);
            break;

        case "GET":
            readUsuario($con);
            break;

        case "PUT":
            updateUsuario($con);
            break;

        case "DELETE":
            deleteUsuario($con);
            break;

        default:
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido!"]);
    }

}


// LOGIN
elseif ($rota === "login") {

    if ($metodo === "POST") {

        loginUsuario($con);

    } else {

        http_response_code(405);
        echo json_encode(["erro" => "Método não permitido!"]);

    }

}


// PUBLICAÇÃO
elseif ($rota === "publicacao") {

    switch ($metodo) {

        case "POST":
            createPublicacao($con);
            break;

        case "GET":
            readPublicacao($con);
            break;

        case "PUT":
            updatePublicacao($con);
            break;

        case "DELETE":
            deletePublicacao($con);
            break;

        default:
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido!"]);
    }

}


// CURTIDA
elseif ($rota === "curtida") {

    switch ($metodo) {

        case "POST":
            createCurtida($con);
            break;

        case "GET":
            readCurtida($con);
            break;

        case "DELETE":
            deleteCurtida($con);
            break;

        default:
            http_response_code(405);
            echo json_encode(["erro" => "Método não permitido!"]);
    }

}


// ROTA NÃO ENCONTRADA
else {

    http_response_code(404);

    echo json_encode([
        "erro" => "Rota não encontrada!"
    ]);

}
