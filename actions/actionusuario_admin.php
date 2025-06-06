<?php

include '../components/verifica_sessao_admin.php';
require '../bd/conexao.php';
$conexao = conexao::getInstance();

$acao = isset($_POST['acao']) ? $_POST['acao'] : null;
$id = isset($_POST['id']) ? $_POST['id'] : 0;
$nome = isset($_POST['nome']) ? $_POST['nome'] : null;
$email = isset($_POST['email']) ? $_POST['email'] : null;
$senha = isset($_POST['senha']) ? $_POST['senha'] : null;
$telefone = isset($_POST['telefone']) ? $_POST['telefone'] : null;
$ativo = isset($_POST['ativo']) ? $_POST['ativo'] : false;
$updated_at = date('Y-m-d H:i:s');
$created_at = date('Y-m-d H:i:s');


if (
    $acao === 'admin_editar' &&
    isset($_SESSION['logado099'], $_SESSION['tipo']) &&
    $_SESSION['logado099'] === true &&
    ($_SESSION['tipo'] === 'atendente')
) {
    if (!empty($senha)) {
        $hashedPassword = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET usu_nome = :nome, usu_email = :email, usu_senha = :senha, usu_telefone = :telefone, usu_ativo = :ativo WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':senha', $hashedPassword);
    } else {
        $sql = "UPDATE usuarios SET usu_nome = :nome, usu_email = :email, usu_telefone = :telefone, usu_ativo = :ativo WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
    }

    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':ativo', $ativo);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Usuário editado com sucesso!';
        header('Location: ../admin/usuarios/usuarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao editar usuário!</div>";
        header('Location: ../admin/usuarios/editar_usuario.php?id=' . $id);
        exit;
    }
}

if ($acao === 'banir') {
    $sql = "UPDATE usuarios SET usu_ativo = false WHERE usu_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();
    if ($retorno) {
        $_SESSION['mensagem'] = 'Usuário banido com sucesso!';
        header('Location: ../admin/usuarios/usuarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao banir usuário!</div>";
        header('Location: ../admin/usuarios/banir_usuario.php?id=' . $id);
        exit;
    }
}
if ($acao === 'desbanir') {
    $sql = "UPDATE usuarios SET usu_ativo = true WHERE usu_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();
    if ($retorno) {
        $_SESSION['mensagem'] = 'Usuário desbanido com sucesso!';
        header('Location: ../admin/usuarios/usuarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao desbanir usuário!</div>";
        header('Location: ../admin/usuarios/desbanir_usuario.php?id=' . $id);
        exit;
    }
}

if ($acao === 'excluir') {
    try {
        $sql = 'DELETE FROM avaliacoes WHERE via_codigo IN (
                    SELECT via_codigo FROM viagens WHERE usu_codigo = :id
                )';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = 'DELETE FROM viagens WHERE usu_codigo = :id';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = 'DELETE FROM avaliacoes WHERE usu_codigo = :id';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = 'DELETE FROM solicitacoes WHERE usu_codigo = :id';
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $sql = "DELETE FROM usuarios WHERE usu_codigo = :id";
        $stmt = $conexao->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $retorno = $stmt->execute();

        if ($retorno) {
            if ($_SESSION['tipo'] === 'usuario') {
                $_SESSION = array();
                session_destroy();

                header('Location: ../index.php');
                exit;
            } elseif ($_SESSION['tipo'] === 'atendente') {
                $_SESSION['mensagem'] = 'Conta excluída com sucesso!';
                header('Location: ../admin/usuarios/usuarios.php');
                exit;
            }
        } else {
            $_SESSION['mensagem'] = 'Erro ao excluir conta!';
            if ($_SESSION['tipo'] === 'atendente') {
                header('Location: ../admin/usuarios/usuarios.php');
            } else {
                header('Location: ../index.php');
            }
            exit;
        }
    } catch (PDOException $e) {
        $_SESSION['mensagem'] = 'Erro no banco de dados: ' . $e->getMessage();
        if ($_SESSION['tipo'] === 'atendente') {
            header('Location: ../admin/usuarios/usuarios.php');
        } else {
            header('Location: ../index.php');
        }
        exit;
    }
}