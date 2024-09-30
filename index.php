<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$servername = "localhost:3306";
	$username = "matheuspaiva_DataBase";
	$password = "098321";
	$database = "matheuspaiva_TCC";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Falha na conexão com o banco de dados: " . $conn->connect_error);
    }

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $cpf = preg_replace('/\D/', '', $_POST['cpf']); // Remove caracteres não numéricos
    $dob = $_POST['dob'];

    // Verifique se o CPF tem 11 dígitos
    if (strlen($cpf) != 11) {
        die("CPF deve ter exatamente 11 dígitos.");
    }

    $photo = file_get_contents($_FILES['photo']['tmp_name']);

    if (!empty($name) && !empty($phone) && !empty($cpf) && !empty($dob)) {
        $sql = "INSERT INTO contatos (nome, telefone, foto, `Data`, CPF) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        
        if ($stmt === false) {
            die("Erro ao preparar a consulta: " . $conn->error);
        }

        $stmt->bind_param("sssss", $name, $phone, $photo, $dob, $cpf);

        if ($stmt->execute()) {
            header("Location: index.php");
            exit();
        } else {
            echo "<div class='error'>Erro ao adicionar o contato: " . $stmt->error . "</div>";
        }

        $stmt->close();
    } else {
        echo "<div class='error'>Por favor, preencha todos os campos.</div>";
    }

    $conn->close();
}
?>






<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Alunos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(72deg, #b11b1b, #0505a8);
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

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="file"] {
            width: calc(100% - 22px);
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
			margin-left: 155px;
        }

        button:hover {
            background-color: #0056b3;
        }

        .error {
            color: red;
            margin-top: 10px;
        }

        h2 {
            margin-top: 20px;
            font-size: 1.5em;
            text-align: center;
			color: black;
        }

        ul {
            list-style-type: none;
            padding: 0;
            max-width: 800px;
            margin: 0 auto;
        }

        li {
            background-color: rgba(255, 255, 255, 0.9);
            border: 1px solid #ddd;
            padding: 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            transition: background-color 0.3s ease;
        }

        li:hover {
            background-color: rgba(240, 240, 240, 0.9);
        }

        img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .contact-details {
            display: flex;
            flex-direction: column;
        }

        .contact-details span {
            margin-bottom: 5px;
            font-size: 1em;
        }

        #imagePreview {
            text-align: center;
            margin-bottom: 15px;
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

    </style>
</head>
<body>
    <header>
        <h1>Cadastro de alunos</h1>
    </header>

    <form class="cadastro" method="POST" enctype="multipart/form-data">
        <label for="name">Nome:</label>
        <input type="text" name="name" id="name" required>

        <label for="phone">Telefone:</label>
        <input type="text" name="phone" id="phone" required>

        <label for="cpf">CPF:</label>
        <input type="text" name="cpf" id="cpf" maxlength="14" required>


        <label for="dob">Data de Nascimento:</label>
        <input type="date" name="dob" id="dob" required>


        <label for="photo">Foto:</label>
        <input type="file" name="photo" accept="image/*" id="photoInput">

        <div id="imagePreview"></div>

        <button type="submit">Finalizar Cadastro</button>
    </form>


    <h2>Alunos:</h2>
        <ul>
            <?php require_once './list_contacts.php'; ?>
        </ul>


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
