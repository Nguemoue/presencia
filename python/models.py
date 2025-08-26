# models.py
from sqlalchemy import Column, Integer, String, LargeBinary
from db import Base

class Person(Base):
    __tablename__ = "persons"

    id = Column(Integer, primary_key=True, index=True)
    name = Column(String(255), unique=True, nullable=False)
    face_encoding = Column(LargeBinary, nullable=False)
