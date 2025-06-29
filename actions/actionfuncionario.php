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
$cnh = isset($_POST['cnh']) ? $_POST['cnh'] : null;
$data_contratacao = isset($_POST['data_contratacao']) ? $_POST['data_contratacao'] : null;
$ativo = isset($_POST['ativo']) ? $_POST['ativo'] : false;
$cargo = isset($_POST['cargo']) ? $_POST['cargo'] : null;
$cpf = isset($_POST['cpf']) ? $_POST['cpf'] : null;


if ($acao == 'adicionar') {
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO funcionarios (fun_nome, fun_email, fun_senha, fun_telefone, fun_cnh, fun_data_contratacao, fun_ativo, fun_cargo, fun_cpf) 
            VALUES (:nome, :email, :senha, :telefone, :cnh, :data_contratacao, :ativo, :cargo, cpf)";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha', $senha_hash); 
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':cnh', $cnh);
    $stmt->bindParam(':data_contratacao', $data_contratacao);
    $stmt->bindParam(':ativo', $ativo);
    $stmt->bindParam(':cargo', $cargo);
    $stmt->bindParam(':cpf', $cpf);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Funcionário adicionado com sucesso!';
        header('Location: ../admin/funcionarios/funcionarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao inserir funcionário!</div>";
        header('Location: ../admin/funcionarios/adicionar_funcionario.php');
        exit;
    }
}

if ($acao == 'editar') {
    if (!empty($senha)) {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE funcionarios SET fun_nome = :nome, fun_email = :email, fun_senha = :senha, fun_telefone = :telefone, 
                fun_cnh = :cnh, fun_ativo = :ativo, fun_cargo = :cargo fun_cpf = :cpf WHERE fun_codigo = :id";
    } else {
        $sql = "UPDATE funcionarios SET fun_nome = :nome, fun_email = :email, fun_telefone = :telefone, 
                fun_cnh = :cnh, fun_ativo = :ativo, fun_cargo = :cargo, fun_cpf = :cpf WHERE fun_codigo = :id";
    }
    
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    if (!empty($senha)) {
        $stmt->bindParam(':senha', $senha_hash);
    }
    $stmt->bindParam(':telefone', $telefone);
    $stmt->bindParam(':cnh', $cnh);
    $stmt->bindParam(':ativo', $ativo);
    $stmt->bindParam(':cargo', $cargo);
    $stmt->bindParam(':cpf', $cpf);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Funcionário editado com sucesso!';
        header('Location: ../admin/funcionarios/funcionarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao editar funcionário!</div>";
        header('Location: ../admin/funcionarios/editar_funcionario.php?id=' . $id);
        exit;
    }
}

if ($acao == 'excluir') {
    $sql = 'DELETE FROM motocicletas WHERE fun_codigo = :id';
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    
    $sql = "DELETE FROM funcionarios WHERE fun_codigo = :id";
    $stmt = $conexao->prepare($sql);
    $stmt->bindParam(':id', $id);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Funcionário excluído com sucesso!';
        header('Location: ../admin/funcionarios/funcionarios.php');
        exit;
    } else {
        echo "<div class='alert alert-danger' role='alert'>Erro ao excluir funcionário!</div>";
        header('Location: ../admin/funcionarios/funcionarios.php');
        exit;
    }
}

if ($acao == 'ativar_desativar') {
    $codigo = $_POST['fun_codigo'];
    $ativo = ($_POST['ativo'] === '1');


    $sql = "UPDATE funcionarios SET fun_ativo = :ativo WHERE fun_codigo = :codigo";
    $stmt = $conexao->prepare($sql);
    $stmt->bindValue(':ativo', $ativo, PDO::PARAM_BOOL); 
    $stmt->bindValue(':codigo', $codigo, PDO::PARAM_INT);
    $retorno = $stmt->execute();

    if ($retorno) {
        $_SESSION['mensagem'] = 'Status do funcionário atualizado!';
    } else {
        $_SESSION['mensagem'] = 'Erro ao atualizar status!';
    }

    header('Location: ../admin/funcionarios/funcionarios.php');
    exit;
} 
?>
