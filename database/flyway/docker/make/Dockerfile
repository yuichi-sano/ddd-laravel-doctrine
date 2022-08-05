FROM postgres:10.13
RUN localedef -i ja_JP -c -f UTF-8 -A /usr/share/locale/locale.alias ja_JP.UTF-8
ENV LANG ja_JP.utf8
ENV PGDATA /var/lib/postgresql/data
ENV TDEHOME /opt/nec/tdeforpg10-fe
ENV PGHOME /usr/lib/postgresql/10
ENV PGDATABASE $POSTGRES_DB
ENV PGUSER $POSTGRES_USER
ENV PGMASTER postgres
ENV PGPASSWORD $POSTGRES_PASSWORD


RUN set -eux; \
	\
	apt-get -y update; \
    apt-get -y install wget \
            alien \
            expect \
            make \
            unzip \
            sudo \
            vim \
    ; \
    \
    mkdir /usr/lib64/; \
    ln -s /usr/lib/postgresql/10/lib/pgcrypto.so /usr/lib/x86_64-linux-gnu/libpgcrypto.so; \
    ln -s /usr/lib/postgresql/10/lib/pgcrypto.so /usr/lib64/libpgcrypto.so; \
    ln -s /usr/lib/x86_64-linux-gnu/libpq.so.5   /usr/lib/postgresql/10/lib/libpq.so.5
RUN echo "%${PGMASTER}    ALL=(ALL)   NOPASSWD:    ALL" >> /etc/sudoers.d/${PGMASTER}
RUN wget  https://github.com/nec-postgres/tdeforpg/releases/download/v1.2.1.0/tde_for_pg10-fe-1.2.1-0.el7.x86_64.rpm
RUN alien -d tde_for_pg10-fe-1.2.1-0.el7.x86_64.rpm
RUN dpkg  -i tde-for-pg10-fe_1.2.1-1_amd64.deb

RUN ln  -s $TDEHOME/SOURCES/data_encryption/10/data_encryption10.so.1.2.1.0 /usr/lib/x86_64-linux-gnu/data_encryption.so
RUN ln  -s $TDEHOME/SOURCES/data_encryption/10/data_encryption10.so.1.2.1.0 /usr/lib64/data_encryption.so

COPY ./pgtde/init_cipher_setup.sh /tmp
COPY ./pgtde/init_cipher_key_regist.sh /tmp
