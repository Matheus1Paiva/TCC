import json
from flask import Flask, render_template, request, jsonify
import os
import base64

app = Flask(__name__)

# Crie uma pasta para salvar as imagens, se não existir
UPLOAD_FOLDER = 'faces'
os.makedirs(UPLOAD_FOLDER, exist_ok=True)

@app.route('/')
def Cadastro_Face():
    return render_template('Cadastro_Face.html')

@app.route('/upload', methods=['POST'])
def upload_image():
    try:
        data = request.get_json()  # Recebe o JSON do frontend
        image_data = data['image'].split(',')[1]  # Ignora o cabeçalho da base64
        image_name = data['name']  # Nome da imagem inserido pelo usuário
        
        # Decodifica a imagem da base64 para bytes
        image_bytes = base64.b64decode(image_data)
        
        # Define o caminho completo para salvar a imagem com o nome fornecido
        image_path = os.path.join(UPLOAD_FOLDER, f'{image_name}.jpg')
        
        # Salva a imagem no diretório
        with open(image_path, 'wb') as f:
            f.write(image_bytes)
        
        # Adicione a face ao banco de dados
        with open('./Reconhecimento/faces.json') as f:
            faces_db = json.load(f)

        # Inclua o "../" no início do caminho da imagem
        new_face = {
            "name": image_name,
            "image_path": f'../{image_path}'
        }

        faces_db['faces'].append(new_face)

        with open('./Reconhecimento/faces.json', 'w') as f:
            json.dump(faces_db, f, indent=4)
        
        return f'Imagem "{image_name}.jpg" salva com sucesso!', 200
    except Exception as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(debug=True)
