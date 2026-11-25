
<?php

// VERIFICAR SE O USUARIO JA CURTIU
function buscarCurtida($con, $id_usuario, $id_publicacao) {

    $stmt = $con->prepare(
        "SELECT id_curtida
         FROM curtidas
         WHERE id_usuario = ?
         AND id_publicacao = ?"
    );

    $stmt->execute([
        $id_usuario,
        $id_publicacao
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}


// CREATE - INSERIR CURTIDA
function inserirCurtida($con, $dados) {

    $stmt = $con->prepare(
        "INSERT INTO curtidas
        (id_usuario, id_publicacao)
        VALUES (?, ?)"
    );

    $stmt->execute([
        $dados["id_usuario"],
        $dados["id_publicacao"]
    ]);

    return $con->lastInsertId();
}


// READ - LISTAR CURTIDAS
function listarCurtidas($con) {

    $stmt = $con->query(
        "SELECT id_curtida, id_usuario, id_publicacao
         FROM curtidas
         ORDER BY id_curtida DESC"
    );

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


// DELETE - REMOVER CURTIDA
function removerCurtida($con, $id) {

    $stmt = $con->prepare(
        "DELETE FROM curtidas
         WHERE id_curtida = ?"
    );

    $stmt->execute([$id]);

    return $stmt->rowCount();
}
