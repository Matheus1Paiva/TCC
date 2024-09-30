import json
import cv2
import numpy as np
from face_recognition import face_locations, face_encodings, compare_faces, face_distance
import face_recognition

# Carregue as imagens de amostra e obtenha as encodings faciais
with open('faces.json') as f:
    faces_db = json.load(f)

known_face_names = [face['name'] for face in faces_db['faces']]
known_face_encodings = []

for face in faces_db['faces']:
    image = face_recognition.load_image_file(face['image_path'])
    face_encoding = face_encodings(image)[0]
    known_face_encodings.append(face_encoding)

# Inicialize a webcam
video_capture = cv2.VideoCapture(0)
video_capture.set(3, 1280)  # Largura
video_capture.set(4, 720)  # Altura

# Parâmetros de processamento
frame_process_interval = 60  # Processar a cada 60 quadros
frame_count = 0
free = False

# Define last_face_location before the loop
last_face_location = None
recognized = False  # Adiciona uma variável para rastrear o reconhecimento

while True:
    # Leia um quadro do vídeo
    ret, frame = video_capture.read()

    # Verifique se deve processar esse quadro
    frame_count += 1
    if frame_count % frame_process_interval == 0:
        # Encontre as localizações e encodings faciais no quadro
        face_locations_list = face_recognition.face_locations(frame)
        face_encodings_list = face_recognition.face_encodings(frame, face_locations_list)

        # Defina como não reconhecido por padrão
        recognized = False

        # Percorra cada face neste quadro de vídeo
        for (top, right, bottom, left), face_encoding in zip(face_locations_list, face_encodings_list):
            # Veja se o rosto corresponde ao(s) rosto(s) conhecido(s)
            name = "Desconhecido"

            matches = compare_faces(known_face_encodings, face_encoding)
            face_distances = face_distance(known_face_encodings, face_encoding)
            best_match_index = np.argmin(face_distances)
            if matches[best_match_index]:
                name = known_face_names[best_match_index]
                recognized = True  # Atualiza para True quando um rosto é reconhecido

            # Armazene a última localização do rosto detectado e seu nome
            last_face_location = (top, right, bottom, left)
            last_face_name = name

    # Desenhe uma caixa ao redor do rosto detectado
    if last_face_location is not None:
        top, right, bottom, left = last_face_location
        cv2.rectangle(frame, (left - 25, top - 25), (right + 25, bottom + 25), (0, 0, 255), 1)

    # Desenhe uma etiqueta com um nome abaixo da face
    if last_face_location is not None:
        top, right, bottom, left = last_face_location
        cv2.rectangle(frame, (left - 25, bottom + 25), (right + 25, bottom + 70), (0, 0, 255), cv2.FILLED)
        font = cv2.FONT_HERSHEY_DUPLEX
        cv2.putText(frame, last_face_name, (left, bottom + 60), font, 1.0, (255, 255, 255), 1)

    # Exiba a imagem resultante
    cv2.imshow('Video', frame)

    # Imprime "Liberado" apenas quando um rosto conhecido é detectado pela primeira vez
    if recognized and not free:
        print("Liberado")
        free = True
    elif not recognized and free:
        print("Bloqueado")
        free = False

    # Pressione 'q' no teclado para sair!
    if cv2.waitKey(1) & 0xFF == ord('q'):
        break

# Solte o identificador da webcam
video_capture.release()
cv2.destroyAllWindows()