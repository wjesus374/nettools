import os
from sqlalchemy import create_engine
from sqlalchemy import Column, Integer, String, Float
from sqlalchemy.ext.declarative import declarative_base
Base = declarative_base()

class TRACEROUTE(Base):
    __tablename__ = 'data'
    id = Column(Integer, primary_key=True)
    dest = Column(String)
    host = Column(String)
    qnt_saltos = Column(Integer)
    tr = Column(Float)

    def __repr__(self):
        return "<TRACEROUTE(host=%s, tr=%s)>" %(self.host, self.tr)

if __name__ == '__main__':
    dbname = 'traceroute.db'
    if os.path.exists(dbname):
        os.unlink(dbname)
    engine = create_engine('sqlite:///' + dbname)
    Base.metadata.create_all(engine)
