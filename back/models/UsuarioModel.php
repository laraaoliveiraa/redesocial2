
<?php

// CREATE
function inserirUsuario($con, $dados) {

    $senhaHash = password_hash($dados["senha"], PASSWORD_DEFAULT);

    $stmt = $con->prepare(
        "INSERT INTO usuarios
        (nome, username, email, senha, data_cadastro)
        VALUES (?, ?, ?, ?, NOW())"
    );

    $stmt->execute([
        trim($dados["nome"]),
        trim($dados["username"]),
        trim($dados["email"]),
        $senhaHash
    ]);

    return $con->lastInsertId();
}


// READ
function listarUsuarios($con) {

    $stmt = $con->query(
        "SELECT id_usuario, nome, username, email, data_cadastro
         FROM usuarios
         ORDER BY id_usuario DESC"
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// BUSCAR USUÁRIO PELO E-MAIL E CONFERIR SENHA
function buscarUsuarioPorEmail($con, $email, $senha) {

    $stmt = $con->prepare(
        "SELECT * FROM usuarios WHERE email = ? LIMIT 1"
    );

    $stmt->execute([trim($email)]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        return false;
    }

    $senhaBanco = (string) $usuario["senha"];
    $senhaDigitada = (string) $senha;

    // Senhas protegidas com password_hash
    $senhaCorreta = password_verify($senhaDigitada, $senhaBanco);

    // Compatibilidade com contas antigas que tenham senha em texto puro
    if (!$senhaCorreta && hash_equals($senhaBanco, $senhaDigitada)) {

        $senhaCorreta = true;

        // Protege a senha antiga ao fazer login
        $novoHash = password_hash($senhaDigitada, PASSWORD_DEFAULT);

        $atualizar = $con->prepare(
            "UPDATE usuarios SET senha = ? WHERE id_usuario = ?"
        );

        $atualizar->execute([
            $novoHash,
            $usuario["id_usuario"]
        ]);
    }

    if (!$senhaCorreta) {
        return false;
    }

    return $usuario;
}


// UPDATE
function atualizarUsuario($con, $id, $dados) {

    $senhaHash = password_hash($dados["senha"], PASSWORD_DEFAULT);

    $stmt = $con->prepare(
        "UPDATE usuarios
         SET nome = ?, username = ?, email = ?, senha = ?
         WHERE id_usuario = ?"
    );

    $stmt->execute([
        trim($dados["nome"]),
        trim($dados["username"]),
        trim($dados["email"]),
        $senhaHash,
        $id
    ]);
}


// DELETE
function removerUsuario($con, $id) {

    $stmt = $con->prepare(
        "DELETE FROM usuarios WHERE id_usuario = ?"
    );

    $stmt->execute([$id]);
}
