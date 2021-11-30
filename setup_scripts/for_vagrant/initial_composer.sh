#!bin/bash
docker run --rm --name sample -u "0:0" -v $(pwd):/var/www/html  -w /var/www/html  -it laravelsail/php80-composer:latest bash -c "cp setup_scripts/for_vagrant/unzip /usr/local/bin && chmod +x /usr/local/bin/unzip && composer install --ignore-platform-reqs"