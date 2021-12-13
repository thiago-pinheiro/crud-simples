
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Agenda de contatos</title>
        <link href="estilo.css" rel="stylesheet">
    </head>
    <body>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $id = isset($_POST['id']) && $_POST['id'] != null ? $_POST['id'] : '';
        $nome =
            isset($_POST['nome']) && $_POST['nome'] != null
                ? $_POST['nome']
                : '';
        $email =
            isset($_POST['email']) && $_POST['email'] != null
                ? $_POST['email']
                : '';
        $celular =
            isset($_POST['celular']) && $_POST['celular'] != null
                ? $_POST['celular']
                : null;
    } elseif (!isset($id)) {
        // Se não se não foi setado nenhum valor para variável $id
        $id = isset($_GET['id']) && $_GET['id'] != null ? $_GET['id'] : '';
        $nome = null;
        $email = null;
        $celular = null;
    }

    try {
        $conexao = new PDO('mysql:host=localhost; dbname=crud', 'root', '');
        $conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conexao->exec('set names utf8');
    } catch (PDOException $erro) {
        echo 'Erro na conexão:' . $erro->getMessage();
    }

    //Condição do INSERT + UPDATE
    if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'save' && $nome != '') {
        try {
            if ($id != '') {
                $stmt = $conexao->prepare(
                    'UPDATE contatos SET nome=?, email=?, celular=? WHERE id = ?'
                );
                $stmt->bindParam(4, $id);
            } else {
                $stmt = $conexao->prepare(
                    'INSERT INTO contatos (nome, email, celular) VALUES (?, ?, ?)'
                );
            }
            $stmt->bindParam(1, $nome);
            $stmt->bindParam(2, $email);
            $stmt->bindParam(3, $celular);

            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    echo 'Dados cadastrados com sucesso!';
                    $id = null;
                    $nome = null;
                    $email = null;
                    $celular = null;
                } else {
                    echo 'Erro ao tentar efetivar cadastro';
                }
            } else {
                throw new PDOException(
                    'Erro: Não foi possível executar a declaração sql'
                );
            }
        } catch (PDOException $erro) {
            echo 'Erro: ' . $erro->getMessage();
        }
    }

    //Condição do READ
    if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'upd' && $id != '') {
        try {
            $stmt = $conexao->prepare('SELECT * FROM contatos WHERE id = ?');
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                $rs = $stmt->fetch(PDO::FETCH_OBJ);
                $id = $rs->id;
                $nome = $rs->nome;
                $email = $rs->email;
                $celular = $rs->celular;
            } else {
                throw new PDOException(
                    'Erro: Não foi possível executar a declaração sql'
                );
            }
        } catch (PDOException $erro) {
            echo 'Erro: ' . $erro->getMessage();
        }
    }

    //Condição do DELETE
    if (isset($_REQUEST['act']) && $_REQUEST['act'] == 'del' && $id != '') {
        try {
            $stmt = $conexao->prepare('DELETE FROM contatos WHERE id = ?');
            $stmt->bindParam(1, $id, PDO::PARAM_INT);
            if ($stmt->execute()) {
                echo 'Registo foi excluído com êxito';
                $id = null;
            } else {
                throw new PDOException(
                    'Erro: Não foi possível executar a declaração sql'
                );
            }
        } catch (PDOException $erro) {
            echo 'Erro: ' . $erro->getMessage();
        }
    }
    ?>
    <h1>Agenda de contatos</h1>
<form action="?act=save" method="POST" name="form" >
            <input type="hidden" name="id"  <?php if (
                (isset($id) && $id != null) ||
                $id != ''
            ) {
                echo "value=\"{$id}\"";
            } ?> />

            <h4>Nome:</h4>
            <input type="text" name="nome" <?php if (
                (isset($nome) && $nome != null) ||
                $nome != ''
            ) {
                echo "value=\"{$nome}\"";
            } ?> />

            <h4>E-mail:</h4>
            <input type="text" name="email" <?php if (
                (isset($email) && $email != null) ||
                $email != ''
            ) {
                echo "value=\"{$email}\"";
            } ?> />

            <h4>Celular:</h4>
            <input type="text" name="celular" <?php if (
                (isset($celular) && $celular != null) ||
                $celular != ''
            ) {
                echo "value=\"{$celular}\"";
            } ?> />

            <div class="containerButton">
            <input type="submit" value="salvar" class="button"/>
            <input type="reset" value="cancelar" class="button"/>
            </div>
          
        </form>

        <table>
    <tr>
        <th>Nome</th>
        <th>E-mail</th>
        <th>Celular</th>
        <th>Ações</th>
    </tr>

    <?php try {
        $stmt = $conexao->prepare('SELECT * FROM contatos');

        if ($stmt->execute()) {
            while ($rs = $stmt->fetch(PDO::FETCH_OBJ)) {
                echo '<tr>';
                echo '<td>' .
                    $rs->nome .
                    '</td><td>' .
                    $rs->email .
                    '</td><td>' .
                    $rs->celular .
                    "</td><td><center><a href=\"?act=upd&id=" .
                    $rs->id .
                    "\">[Alterar]</a>" .
                    '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
                    "<a href=\"?act=del&id=" .
                    $rs->id .
                    "\">[Excluir]</a></center></td>";
                echo '</tr>';
            }
        } else {
            echo 'Erro: Não foi possível recuperar os dados do banco de dados';
        }
    } catch (PDOException $erro) {
        echo 'Erro: ' . $erro->getMessage();
    } ?>
</table>
    </body>
</html>