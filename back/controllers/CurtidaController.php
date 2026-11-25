
<?php

require_once __DIR__ . "/../models/CurtidaModel.php";

// RECEBER E VALIDAR DADOS
function receberDadosCurtida() {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!is_array($dados)) {

        http_response_code(400);

        echo json_encode([
            "erro" => "Envie os dados corretamente!"
        ]);

        return null;
    }

    foreach (["id_usuario", "id_publicacao"] as $campo) {

        if (
            !isset($dados[$campo]) ||
            filter_var($dados[$campo], FILTER_VALIDATE_INT) === false ||
            (int)$dados[$campo] < 1
        ) {

            http_response_code(400);

            echo json_encode([
                "erro" => "O campo $campo deve conter um ID válido!"
            ]);

            return null;
        }

    }

    $dados["id_usuario"] = (int)$dados["id_usuario"];
    $dados["id_publicacao"] = (int)$dados["id_publicacao"];

    return $dados;
}


// RECEBER ID DA CURTIDA
function receberIdCurtida() {

    $id = filter_var(
        $_GET["id"] ?? null,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]
    );

    if ($id === false || $id === null) {

        http_response_code(400);

        echo json_encode([
            "erro" => "Informe um ID de curtida válido!"
        ]);

        return null;
    }

    return $id;
}


// CREATE - CURTIR PUBLICAÇÃO
function createCurtida($con) {

    $dados = receberDadosCurtida();

    if ($dados === null) {
        return;
    }

    try {

        // Verificar se já existe uma curtida
        $existente = buscarCurtida(
            $con,
            $dados["id_usuario"],
            $dados["id_publicacao"]
        );

        if ($existente) {

            echo json_encode([
                "sucesso" => true,
                "curtido" => true,
                "mensagem" => "Você já curtiu esta publicação!"
            ]);

            return;
        }

        // Registrar curtida
        $id = inserirCurtida($con, $dados);

        http_response_code(201);

        echo json_encode([
            "sucesso" => true,
            "curtido" => true,
            "mensagem" => "Publicação curtida com sucesso!",
            "id" => $id
        ]);

    } catch (PDOException $erro) {

        http_response_code(500);

        echo json_encode([
            "erro" => "Erro ao registrar curtida."
        ]);

    }

}


// READ - LISTAR CURTIDAS
function readCurtida($con) {

    try {

        $curtidas = listarCurtidas($con);

        echo json_encode($curtidas);

    } catch (PDOException $erro) {

        http_response_code(500);

        echo json_encode([
            "erro" => "Erro ao buscar curtidas."
        ]);

    }

}


// DELETE - REMOVER CURTIDA
function deleteCurtida($con) {

    $id = receberIdCurtida();

    if ($id === null) {
        return;
    }

    try {

        removerCurtida($con, $id);

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Curtida removida com sucesso!"
        ]);

    } catch (PDOException $erro) {

        http_response_code(500);

        echo json_encode([
            "erro" => "Erro ao remover curtida."
        ]);

    }

}
