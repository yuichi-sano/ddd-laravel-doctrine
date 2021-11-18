##!/usr/bin/env bash
#
#BASE_DIR=`dirname $0`
#
#cd $BASE_DIR
#
#./gradlew -i clean processResources flywayMigrate -Dflyway.locations=classpath:db/migration,classpath:develop -Dflyway.url=jdbc:postgresql://{url}:5432/{dbname} -Dflyway.user={user} -Dflyway.password={pass}
