import subprocess
import sqlalchemy as db
from sqlalchemy.orm import Session
from create_db_mtr import Base, MTR

engine = db.create_engine('sqlite:///mtr.db')
connection = engine.connect()
Base.metadata.bind = engine
session = Session(engine)

awk = ['awk','NR>1 {print $2,$3,$4,$5,$6,$7,$8,$9,$10}']
hosts= ['www.pudim.com.br']

for host in hosts:
    p1 = subprocess.Popen(['mtr', '-w', '--ipinfo', '0', '-c5', host], stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    p2 = subprocess.Popen(awk, stdin=p1.stdout, stdout=subprocess.PIPE)
    stdout, stderr = p2.communicate()
    x = 1
    for line in stdout.splitlines():
        if x != 1:
            line = line.decode("utf-8")
            line = line.split(" ")
            #print(line.split(" "))
            loss_data = line[2].replace("%","")
            data = MTR(host=host, asn=line[0], ip=line[1], loss=loss_data, 
                       last=line[4], avg=line[5], best=line[6],
                       wrst=line[7], stdev=line[8])
            session.add(data)
            session.commit()
            #session.flush()
        x += 1
