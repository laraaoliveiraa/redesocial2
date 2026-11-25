
<?php

require_once __DIR__ . "/../models/UsuarioModel.php";


// RECEBER E VALIDAR OS DADOS
function receberDadosUsuario() {

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (!is_array($dados)) {

        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "erro" => "Envie os dados corretamente!"
        ]);

        return null;
    }

    foreach (["nome", "username", "email", "senha"] as $campo) {

        if (
            !isset($dados[$campo]) ||
            !is_string($dados[$campo]) ||
            trim($dados[$campo]) === ""
        ) {

            http_response_code(400);

            echo json_encode([
                "sucesso" => false,
                "erro" => "O campo $campo é obrigatório!"
            ]);

            return null;
        }
    }

    if (!filter_var($dados["email"], FILTER_VALIDATE_EMAIL)) {

        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "erro" => "Informe um e-mail válido!"
        ]);

        return null;
    }

    if (strlen($dados["senha"]) < 6) {

        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "erro" => "A senha precisa ter pelo menos 6 caracteres!"
        ]);

        return null;
    }

    return $dados;
}


// RECEBER ID
function receberIdUsuario() {

    $id = filter_var(
        $_GET["id"] ?? null,
        FILTER_VALIDATE_INT,
        ["options" => ["min_range" => 1]]
    );

    if ($id === false || $id === null) {

        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "erro" => "Informe um ID de usuário válido!"
        ]);

        return null;
    }

    return $id;
}


// CREATE
function createUsuario($con) {

    $dados = receberDadosUsuario();

    if ($dados === null) {
        return;
    }

    try {

        $id = inserirUsuario($con, $dados);

        http_response_code(201);

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Conta criada com sucesso!",
            "id" => $id
        ]);

    } catch (PDOException $e) {

        http_response_code(409);

        echo json_encode([
            "sucesso" => false,
            "erro" => "Este e-mail ou nome de usuário já está cadastrado."
        ]);
    }
}


// READ
function readUsuario($con) {

    echo json_encode(listarUsuarios($con));
}


// UPDATE
function updateUsuario($con) {

    $id = receberIdUsuario();

    if ($id === null) {
        return;
    }

    $dados = receberDadosUsuario();

    if ($dados === null) {
        return;
    }

    try {

        atualizarUsuario($con, $id, $dados);

        echo json_encode([
            "sucesso" => true,
            "mensagem" => "Usuário atualizado com sucesso!"
        ]);

    } catch (PDOException $e) {

        http_response_code(409);

        echo json_encode([
            "sucesso" => false,
            "erro" => "Não foi possível atualizar. Verifique se o e-mail ou username já está em uso."
        ]);
    }
}


// DELETE
function deleteUsuario($con) {

    $id = receberIdUsuario();

    if ($id === null) {
        return;
    }

    removerUsuario($con, $id);

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Usuário removido com sucesso!"
    ]);
}


// LOGIN
function loginUsuario($con) {

    $dados = json_decode(
        file_get_contents("php://input"),
        true
    );

    if (
        !is_array($dados) ||
        empty($dados["email"]) ||
        empty($dados["senha"])
    ) {

        http_response_code(400);

        echo json_encode([
            "sucesso" => false,
            "erro" => "Informe o e-mail e a senha!"
        ]);

        return;
    }

    $usuario = buscarUsuarioPorEmail(
        $con,
        $dados["email"],
        $dados["senha"]
    );

    if (!$usuario) {

        http_response_code(401);

        echo json_encode([
            "sucesso" => false,
            "erro" => "E-mail ou senha incorretos."
        ]);

        return;
    }

    echo json_encode([
        "sucesso" => true,
        "mensagem" => "Login realizado com sucesso!",
        "usuario" => [
            "id_usuario" => $usuario["id_usuario"],
            "nome" => $usuario["nome"],
            "username" => $usuario["username"],
            "email" => $usuario["email"]
        ]
    ]);
}
