import pickle
from flask import Flask, request, jsonify
import face_recognition
from sqlalchemy.orm import Session
from db import SessionLocal, engine, Base
from models import User

# Créer les tables si elles n'existent pas
Base.metadata.create_all(bind=engine)

app = Flask(__name__)

@app.route("/register", methods=["POST"])
def register_face():
    """
    Enregistre l'encodage facial d'un utilisateur existant via son user_id.
    Requiert : user_id, file (image contenant un seul visage).
    """
    if "file" not in request.files or "user_id" not in request.form:
        return jsonify({"error": "File and user_id required"}), 400

    file = request.files["file"]
    user_id = request.form["user_id"]

    # Charger et encoder l'image
    img = face_recognition.load_image_file(file)
    encodings = face_recognition.face_encodings(img)

    if not encodings:
        return jsonify({"error": "No face detected"}), 400

    serialized_encoding = pickle.dumps(encodings[0])

    db: Session = SessionLocal()
    try:
        user = db.query(User).filter(User.id == user_id).first()
        if not user:
            return jsonify({"error": "User not found"}), 404

        user.face_encoding = serialized_encoding
        db.commit()
        return jsonify({"success": True, "id": user.id}), 201
    except Exception as e:
        db.rollback()
        return jsonify({"error": str(e)}), 500
    finally:
        db.close()

@app.route("/recognize", methods=["POST"])
def recognize():
    if "file" not in request.files:
        return jsonify({"error": "No file uploaded"}), 400

    file = request.files["file"]

    img = face_recognition.load_image_file(file)
    encodings = face_recognition.face_encodings(img)

    if len(encodings) == 0:
        return jsonify({"error": "No face detected"}), 400

    uploaded_encoding = encodings[0]

    db: Session = SessionLocal()
    #filter where face encoding is not null
    users = db.query(User).filter(User.face_encoding != None ).all()

    for person in users:
        stored_encoding = pickle.loads(person.face_encoding)
        results = face_recognition.compare_faces([stored_encoding], uploaded_encoding)

        if results[0]:
            db.close()
            return jsonify({"match": True, "person": person.id}), 200

    db.close()
    return jsonify({"match": False}), 200


if __name__ == "__main__":
    app.run(debug=True)
