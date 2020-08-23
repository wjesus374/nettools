import subprocess
import sqlalchemy as db
from sqlalchemy.orm import Session
from create_db_traceroute import Base, TRACEROUTE

engine = db.create_engine('sqlite:///traceroute.db')
connection = engine.connect()
Base.metadata.bind = engine
session = Session(engine)

hosts= ['www.pudim.com.br']
awk = ['awk','{print $2,$3}']

for host in hosts:
    p1 = subprocess.Popen(['traceroute', '-n', '-q', '1', host], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    p2 = subprocess.Popen(awk, stdin=p1.stdout, stdout=subprocess.PIPE)
    stdout, stderr = p2.communicate()
    qnt_saltos = len(stdout.splitlines())-1
    x = 1
    for line in stdout.splitlines():
        if x != 1:
            line = line.decode("utf-8")
            line = line.split(" ")
            #print(line)
            if isinstance(line[1], str):
                tr=0.0
            else:
                tr=line[1]
            data = TRACEROUTE(tr=tr, qnt_saltos=qnt_saltos, dest=host, host=line[0])
            session.add(data)
            session.commit()
            #session.flush()
        x += 1
