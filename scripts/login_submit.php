<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = isset($_POST['text_usuario']) ? trim(htmlspecialchars($_POST['text_usuario'])) : null;
    $senha = isset($_POST['text_senha']) ? trim(htmlspecialchars($_POST['text_senha'])) : null;

    // Verifica se os dados estão preenchidos
    if (!$usuario || !$senha) {
        header('Location: /?rota=login');
        exit;
    }
   
    $bd = new EasyPDO\EasyPDO(MYSQL_OPTIONS);

    $senhah = hash('sha256', $senha);

    $resultado = $bd->select("SELECT * FROM login WHERE login = :login AND senha = :senha", [
        ':login' => $usuario,
        ':senha' => $senhah
    ]);
   
    // Verifica se houve algum erro na consulta
    if (!$resultado) {
        session_start();
        $_SESSION['erro'] = 'Usuário ou senha inválidos';
        header('Location: index.php?rota=login');
        exit;
    }

    // Verifica se o usuário existe
    if (empty($resultado)) {
        session_start();
        $_SESSION['erro'] = 'Usuário ou senha inválidos';
        header('Location: index.php?rota=login');
        exit;
    }

    // Verifica se a senha está correta
    if (hash('sha256', trim($senha)) !== $resultado[0]['senha']) {
        session_start();
        $_SESSION['erro'] = 'Usuário ou senha inválidos';
        header('Location: index.php?rota=login');
        exit;
    }
    

    // Define a sessão do usuário
    session_start();
    $_SESSION['usuario'] = $resultado[0];

    // Redireciona para a página inicial
    header('Location: index.php?rota=home');
    exit;
} else {
    header('Location: /?rota=login');
    exit;
}
