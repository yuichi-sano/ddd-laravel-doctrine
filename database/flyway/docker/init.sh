#!/bin/bash

set -e

export PGDATA=/var/lib/postgresql/data
export TDEHOME=/opt/nec/tdeforpg10-fe
export PGHOME=/usr/lib/postgresql/10
export PGDATABASE=${POSTGRES_DB:sample}
export PGUSER=${POSTGRES_USER:sample}
export PGMASTER=postgres
export PGPASSWORD=${POSTGRES_PASSWORD:weak-password}
export PGTESTDB=${POSTGRES_DB}test
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$POSTGRES_DB" <<-EOSQL
    CREATE DATABASE $PGTESTDB ;
    GRANT ALL PRIVILEGES ON DATABASE $PGTESTDB  TO $PGUSER;
    CREATE extension pgcrypto;
    CREATE ROLE dbowner;
    ALTER ROLE dbowner WITH SUPERUSER INHERIT NOCREATEROLE NOCREATEDB LOGIN NOREPLICATION NOBYPASSRLS PASSWORD 'md5e47e25b4813927f628a76f734604eb5e';
EOSQL
psql -v ON_ERROR_STOP=1 --username "$POSTGRES_USER" --dbname "$PGTESTDB" <<-EOSQL
    CREATE extension pgcrypto;
EOSQL

sudo mv /tmp/init_cipher_setup.sh $TDEHOME/SOURCES/bin
sudo mv /tmp/init_cipher_key_regist.sh $TDEHOME/SOURCES/bin
cd $TDEHOME/SOURCES
expect -c "
set timeout 20
spawn sudo sh bin/init_cipher_setup.sh $PGHOME
expect \"select menu \[1 - 2\]\"
send -- \"1\n\"
expect \"Please enter database server port to connect :\"
send -- \"$PGPORT\n\"
expect \"Please enter database user name to connect :\"
send -- \"$PGUSER\n\"
expect \"Please enter password for authentication :\"
send -- \"$PGPASSWORD\n\"
expect \"Please enter database name to connect :\"
send -- \"$PGDATABASE\n\"
expect \"WARN: Transparent data encryption function has already been activated\"
send -- \x03
expect \"Please input \[Yes\/No\]\"
send -- \"Yes\n\"
expect eof
"

expect -c "
set timeout 20
spawn sudo sh bin/init_cipher_key_regist.sh $PGHOME
expect \"Please enter database server port to connect :\"
send -- \"$PGPORT\n\"
expect \"Please enter database user name to connect :\"
send -- \"$PGUSER\n\"
expect \"Please enter password for authentication :\"
send -- \"$PGPASSWORD\n\"
expect \"Please enter database name to connect :\"
send -- \"$PGDATABASE\n\"
expect \"Please enter the new cipher key :\"
send -- \"$PGTDE_CIPHER\n\"
expect \"Please retype the new cipher key :\"
send -- \"$PGTDE_CIPHER\n\"
expect  \"Please enter the algorithm for new cipher key :\"
send -- \"$PGTDE_ALGO\n\"
expect  \"Are you sure to register new cipher key(y/n) :\"
send -- \"y\n\"
expect \"WARN: Transparent data encryption function has already been activated\"
send -- \x03
expect \"Please input \[Yes\/No\]\"
send -- \"Yes\n\"
expect eof
"

expect -c "
set timeout 20
spawn sudo sh bin/init_cipher_setup.sh $PGHOME
expect \"select menu \[1 - 2\]\"
send -- \"1\n\"
expect \"Please enter database server port to connect :\"
send -- \"$PGPORT\n\"
expect \"Please enter database user name to connect :\"
send -- \"$PGUSER\n\"
expect \"Please enter password for authentication :\"
send -- \"$PGPASSWORD\n\"
expect \"Please enter database name to connect :\"
send -- \"$PGTESTDB\n\"
expect \"WARN: Transparent data encryption function has already been activated\"
send -- \x03
expect \"Please input \[Yes\/No\]\"
send -- \"Yes\n\"
expect eof
"

expect -c "
set timeout 20
spawn sudo sh bin/init_cipher_key_regist.sh $PGHOME
expect \"Please enter database server port to connect :\"
send -- \"$PGPORT\n\"
expect \"Please enter database user name to connect :\"
send -- \"$PGUSER\n\"
expect \"Please enter password for authentication :\"
send -- \"$PGPASSWORD\n\"
expect \"Please enter database name to connect :\"
send -- \"$PGTESTDB\n\"
expect \"Please enter the new cipher key :\"
send -- \"$PGTDE_CIPHER\n\"
expect \"Please retype the new cipher key :\"
send -- \"$PGTDE_CIPHER\n\"
expect  \"Please enter the algorithm for new cipher key :\"
send -- \"$PGTDE_ALGO\n\"
expect  \"Are you sure to register new cipher key(y/n) :\"
send -- \"y\n\"
expect \"WARN: Transparent data encryption function has already been activated\"
send -- \x03
expect \"Please input \[Yes\/No\]\"
send -- \"Yes\n\"
expect eof
"
