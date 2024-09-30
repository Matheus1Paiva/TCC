<?php
$servername = "localhost:3306";
$username = "matheuspaiva_DataBase";
$password = "098321";
$database = "matheuspaiva_TCC";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

$sql = "SELECT id, nome, telefone, foto, Data, CPF FROM contatos"; // Inclui o campo 'id' para referência
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<img src='data:image/jpeg;base64," . base64_encode($row['foto']) . "' alt='Foto'>";
        echo "<div class='contact-details'>";
        echo "<span><strong>Nome:</strong> " . htmlspecialchars($row['nome']) . "</span>";
        echo "<span><strong>Telefone:</strong> " . htmlspecialchars($row['telefone']) . "</span>";
        echo "<span><strong>Data:</strong> " . htmlspecialchars($row['Data']) . "</span>";
        echo "<span><strong>CPF:</strong> " . htmlspecialchars($row['CPF']) . "</span>";
        echo "</div>";

        // Botão para Editar
        echo "<form action='editar.php' method='GET' style='display:inline-block;'>";
        echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
        echo "<button type='submit'>Editar</button>";
        echo "</form>";

        // Botão para Deletar
        echo "<form action='deletar.php' method='POST' style='display:inline-block;'>";
        echo "<input type='hidden' name='id' value='" . htmlspecialchars($row['id']) . "'>";
        echo "<button type='submit' onclick='return confirm(\"Tem certeza que deseja deletar este contato?\")'>Deletar</button>";
        echo "</form>";

        echo "</li>";
    }
} else {
    echo "<li>Nenhum contato encontrado.</li>";
}

$conn->close();
?>
