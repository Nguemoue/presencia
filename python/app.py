import pickle
from flask import Flask, request, jsonify   # <-- CORRIGÉ
import face_recognition
from sqlalchemy.orm import Session
from db import SessionLocal, engine, Base
from models import Person

# Créer les tables si elles n'existent pas
Base.metadata.create_all(bind=engine)

app = Flask(__name__)

@app.route("/register", methods=["POST"])
def register():
    if "file" not in request.files or "name" not in request.form:
        return jsonify({"error": "File and name required"}), 400

    file = request.files["file"]
    name = request.form["name"]

    # Encoder l'image
    img = face_recognition.load_image_file(file)
    encodings = face_recognition.face_encodings(img)

    if len(encodings) == 0:
        return jsonify({"error": "No face detected"}), 400

    encoding = encodings[0]
    serialized_encoding = pickle.dumps(encoding)

    db: Session = SessionLocal()
    person = Person(name=name, face_encoding=serialized_encoding)
    db.add(person)
    db.commit()
    db.refresh(person)
    db.close()

    return jsonify({"success": True, "id": person.id}), 201


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
    persons = db.query(Person).all()

    for person in persons:
        stored_encoding = pickle.loads(person.face_encoding)
        results = face_recognition.compare_faces([stored_encoding], uploaded_encoding)

        if results[0]:
            db.close()
            return jsonify({"match": True, "person": person.name}), 200

    db.close()
    return jsonify({"match": False}), 200


if __name__ == "__main__":
    app.run(debug=True)
