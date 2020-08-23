import os
from sqlalchemy import create_engine
from sqlalchemy import Column, Integer, String, Float

from sqlalchemy.ext.declarative import declarative_base
Base = declarative_base()

class MTR(Base):
    __tablename__ = 'data'
    id = Column(Integer, primary_key=True)
    host = Column(String)
    ip = Column(String)
    asn = Column(String)
    loss = Column(String)
    last = Column(String)
    avg = Column(String)
    best = Column(String)
    wrst = Column(String)
    stdev = Column(String)

    def __repr__(self):
        return "<MRT(host=%s, loss=%s)>" %(self.host, self.loss)


if __name__ == '__main__':
    dbname = 'mtr.db'
    if os.path.exists(dbname):
        os.unlink(dbname)
    engine = create_engine('sqlite:///' + dbname)
    Base.metadata.create_all(engine)
