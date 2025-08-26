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
    """
    Compare une photo avec le visage encodé d'un utilisateur donné.
    Requiert : user_id, file (image).
    """
    if "file" not in request.files or "user_id" not in request.form:
        return jsonify({"error": "File and user_id required"}), 400

    try:
        user_id = int(request.form["user_id"])
    except ValueError:
        return jsonify({"error": "user_id must be an integer"}), 400

    file = request.files["file"]
    img = face_recognition.load_image_file(file)
    encodings = face_recognition.face_encodings(img)

    if not encodings:
        return jsonify({"error": "No face detected"}), 400

    uploaded_encoding = encodings[0]

    db: Session = SessionLocal()
    try:
        user = db.query(User).filter(User.id == user_id).first()
        if not user:
            return jsonify({"error": "User not found"}), 404

        if not user.face_encoding:
            return jsonify({"error": "No face_encoding stored for this user"}), 400

        stored_encoding = pickle.loads(user.face_encoding)
        match = face_recognition.compare_faces([stored_encoding], uploaded_encoding)[0]

        if match:
            return jsonify({
                "match": True,
                "user": {
                    "id": user.id,
                    "nom": user.nom,
                    "prenom": user.prenom,
                    "email": user.email,
                    "type": user.type,
                    "matricule": user.matricule
                }
            }), 200
        return jsonify({"match": False}), 200
    finally:
        db.close()


if __name__ == "__main__":
    app.run(debug=True)
