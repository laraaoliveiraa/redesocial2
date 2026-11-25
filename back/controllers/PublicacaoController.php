
<?php

require_once __DIR__ . "/../models/PublicacaoModel.php";

// RECEBER E VALIDAR DADOS
function receberDadosPublicacao() {

    $dados = json_decode(file_get_contents("php://input"), true);

    if (!is_array($dados)) {
        http_response_code(400);
        echo json_encode(["erro" => "Envie os dados corretamente!"]);
        return null;
    }

    if (
        !isset($dados["id_usuario"]) ||
        filter_var($dados["id_usuario"], FILTER_VALIDATE_INT) === false ||
        (int)$dados["id_usuario"] < 1
    ) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe um usuário válido!"]);
        return null;
    }

    // LEGENDA OPCIONAL
    $legenda = $dados["legenda"] ?? "";

    if (!is_string($legenda)) {
        http_response_code(400);
        echo json_encode(["erro" => "Legenda inválida!"]);
        return null;
    }

    // IMAGEM OPCIONAL
    $imagem = $dados["imagem_url"] ?? "";

    if (!is_string($imagem)) {
        http_response_code(400);
        echo json_encode(["erro" => "Link de imagem inválido!"]);
        return null;
    }

    $legenda = trim($legenda);
    $imagem = trim($imagem);

    // PRECISA TER TEXTO OU IMAGEM
    if ($legenda === "" && $imagem === "") {
        http_response_code(400);
        echo json_encode([
            "erro" => "Escreva uma legenda ou informe uma imagem!"
        ]);
        return null;
    }

    // VALIDAR LINK DA IMAGEM
    if ($imagem !== "" && !filter_var($imagem, FILTER_VALIDATE_URL)) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe um link de imagem válido!"]);
        return null;
    }

    return [
        "id_usuario" => (int)$dados["id_usuario"],
        "legenda" => $legenda,
        "imagem_url" => $imagem
    ];
}

// RECEBER ID
function receberIdPublicacao() {

    $id = filter_var(
        $_GET["id"] ?? null,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]
    );

    if ($id === false || $id === null) {
        http_response_code(400);
        echo json_encode(["erro" => "Informe um ID válido!"]);
        return null;
    }

    return $id;
}

// CRIAR PUBLICAÇÃO
function createPublicacao($con) {

    $dados = receberDadosPublicacao();

    if ($dados === null) {
        return;
    }

    try {
        $id = inserirPublicacao($con, $dados);

        http_response_code(201);

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Publicação criada com sucesso!",
            "id" => $id
        ]);

    } catch (PDOException $e) {
        http_response_code(400);
        echo json_encode([
            "erro" => "Não foi possível criar a publicação."
        ]);
    }
}

// LISTAR PUBLICAÇÕES
function readPublicacao($con) {
    echo json_encode(listarPublicacoes($con));
}

// ATUALIZAR PUBLICAÇÃO
function updatePublicacao($con) {

    $id = receberIdPublicacao();

    if ($id === null) {
        return;
    }

    $dados = receberDadosPublicacao();

    if ($dados === null) {
        return;
    }

    atualizarPublicacao($con, $id, $dados);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Publicação atualizada com sucesso!"
    ]);
}

// DELETAR PUBLICAÇÃO
function deletePublicacao($con) {

    $id = receberIdPublicacao();

    if ($id === null) {
        return;
    }

    removerPublicacao($con, $id);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Publicação removida com sucesso!"
    ]);
}
