from flask import Flask, request, jsonify
import face_recognition
import base64
import numpy as np
from io import BytesIO
from PIL import Image
import os

REFERENCE = "uploads/"
app = Flask(__name__)

@app.route('/face_recognition', methods=['POST'])
def recognize():
    try:
        # Vérification des champs requis
        d = request.get_json()
        if not d or 'image' not in d or 'user_id' not in d:
            return jsonify(success=False, error="Missing 'image' or 'user_id'"), 400

        # Décodage de l'image base64
        try:
            img_b64 = d['image'].split(',')[1]
            img = Image.open(BytesIO(base64.b64decode(img_b64)))
            img = np.array(img)
        except Exception as e:
            return jsonify(success=False, error="Invalid image format"), 400

        # Chargement de la photo de référence
        user_id = d['user_id']
        ref_path = os.path.join(REFERENCE , f"user_{user_id}.jpg")
        if not os.path.exists(ref_path):
            return jsonify(success=False, error="Reference image not found"), 404

        known = face_recognition.load_image_file(ref_path)
        known_encodings = face_recognition.face_encodings(known)
        if not known_encodings:
            return jsonify(success=False, error="No face found in reference image"), 422

        input_encodings = face_recognition.face_encodings(img)
        if not input_encodings:
            return jsonify(success=False, error="No face found in input image"), 422

        # Comparaison des visages
        match = face_recognition.compare_faces(known_encodings, input_encodings[0])
        return jsonify(success=match[0])

    except Exception as e:
        return jsonify(success=False, error=str(e)), 500

if __name__ == '__main__':
    app.run(debug=False, host='127.0.0.1', port=5000)