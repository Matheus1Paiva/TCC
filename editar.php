<?php
$servername = "localhost:3306";
$username = "matheuspaiva_DataBase";
$password = "098321";
$database = "matheuspaiva_TCC";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}

// Verifica se o ID foi passado via GET para exibir o formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'];

    // Busca os dados atuais do contato
    $sql = "SELECT nome, telefone, Data, CPF, foto FROM contatos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        ?>
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Editar Aluno</title>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
            <style>
                body {
                    font-family: 'Arial', sans-serif;
                    margin: 0;
                    padding: 0;
                    background: linear-gradient(to right, #6a11cb, #2575fc);
                    color: #333;
                }

                header {
                    background-color: red;
                    color: white;
                    text-align: center;
                    padding: 20px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                h1 {
                    margin: 0;
                    font-size: 2em;
                }

                .cadastro {
                    background-color: rgba(255, 255, 255, 0.9);
                    padding: 20px;
                    margin: 20px auto;
                    max-width: 500px;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                label {
                    display: block;
                    margin-bottom: 8px;
                    font-weight: bold;
                }

                input[type="text"],
                input[type="file"],
                input[type="date"] {
                    width: calc(100%);
                    padding: 10px;
                    margin-bottom: 15px;
                    border: 1px solid #ccc;
                    border-radius: 5px;
                    box-sizing: border-box;
                }

                button {
                    background-color: #4a90e2;
                    color: white;
                    border: none;
                    padding: 12px 20px;
                    cursor: pointer;
                    border-radius: 8px;
                    font-size: 18px;
                    transition: background-color 0.3s, transform 0.2s;
                    margin-left: 170px;
                }

                button:hover {
                    background-color: #0056b3;
                }

                .error {
                    color: red;
                    margin-top: 10px;
                }

                #imagePreview {
                    text-align: center;
                    margin-bottom: 15px;
                }
            </style>
        </head>
        <body>
            <header>
                <h1>Editar Aluno</h1>
            </header>

            <form class="cadastro" action="editar.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">

                <label for="name">Nome:</label>
                <input type="text" name="name" id="name" value="<?php echo htmlspecialchars($row['nome']); ?>" required>

                <label for="phone">Telefone:</label>
                <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($row['telefone']); ?>" required>

                <label for="dob">Data de Nascimento:</label>
                <input type="date" name="dob" id="dob" value="<?php echo htmlspecialchars($row['Data']); ?>" required>

                <label for="cpf">CPF:</label>
                <input type="text" name="cpf" id="cpf" value="<?php echo htmlspecialchars($row['CPF']); ?>" maxlength="14" required>

                <label for="photo">Foto:</label>
                <input type="file" name="photo" accept="image/*" id="photoInput">
                
                <div id="imagePreview">
                    <?php if (!empty($row['foto'])): ?>
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row['foto']); ?>" alt="Foto" style="width: 100px; height: 100px; border-radius: 50%;">
                    <?php endif; ?>
                </div>

                <button type="submit">Atualizar Aluno</button>
            </form>

            <script>
            document.addEventListener("DOMContentLoaded", function() {
                const photoInput = document.getElementById("photoInput");
                const imagePreview = document.getElementById("imagePreview");

                photoInput.addEventListener("change", function() {
                    const file = this.files[0];

                    if (file) {
                        const reader = new FileReader();

                        reader.onload = function(e) {
                            const img = document.createElement("img");
                            img.src = e.target.result;
                            img.style.width = "100px";
                            img.style.height = "100px";
                            img.style.borderRadius = "50%";
                            imagePreview.innerHTML = "";
                            imagePreview.appendChild(img);
                        };

                        reader.readAsDataURL(file);
                    } else {
                        imagePreview.innerHTML = "";
                    }
                });
            });
            </script>
        </body>
        </html>
        <?php
    } else {
        echo "Aluno não encontrado.";
    }

    $stmt->close();
}

// Verifica se o formulário foi enviado via POST para atualizar os dados
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $dob = $_POST['dob'];
    $cpf = preg_replace('/\D/', '', $_POST['cpf']); // Remove caracteres não numéricos
    $photo = !empty($_FILES['photo']['tmp_name']) ? file_get_contents($_FILES['photo']['tmp_name']) : null;

    // Verifica se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        die("CPF deve ter exatamente 11 dígitos.");
    }

    // Query de atualização
    if ($photo) {
        $sql = "UPDATE contatos SET nome = ?, telefone = ?, Data = ?, CPF = ?, foto = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $name, $phone, $dob, $cpf, $photo, $id);
    } else {
        $sql = "UPDATE contatos SET nome = ?, telefone = ?, Data = ?, CPF = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $phone, $dob, $cpf, $id);
    }

    if ($stmt->execute()) {
        header("Location: index.php");
        exit();
    } else {
        echo "Erro ao atualizar aluno: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
