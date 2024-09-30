<?php
$servername = "localhost:3306";
$username = "matheuspaiva_DataBase";
$password = "098321";
$database = "matheuspaiva_TCC";

// Cria conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $database);

// Verifica se a conexão falhou
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o ID foi passado pelo formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];

    // Executa a query de exclusão
    $sql = "DELETE FROM contatos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao deletar contato: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "ID do contato não fornecido.";
}

$conn->close();
?>
