from sqlalchemy import Column, Integer, String, Enum, ForeignKey, DateTime, LargeBinary, func
from sqlalchemy.orm import relationship
from db import Base

class User(Base):
    __tablename__ = "users"

    id = Column(Integer, primary_key=True, autoincrement=True)
    nom = Column(String(100), nullable=False)
    prenom = Column(String(100), nullable=False)
    email = Column(String(150), unique=True, nullable=False)
    mot_de_passe = Column(String(255), nullable=False)
    type = Column(Enum("etudiant", "personnel", "admin"), nullable=False)
    photo_reference = Column(String(255), nullable=True)
    face_encoding = Column(LargeBinary, nullable=False)
    matricule = Column(String(255), nullable=True)
    created_at = Column(DateTime, server_default=func.now())
