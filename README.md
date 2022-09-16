# Création de l'application Symfony
```shell
symfony new --webapp une_question
```
# Initilisation des fichiers de configuration Platform.sh
https://symfony.com/doc/current/cloud/getting-started.html#configuring-platform-sh-for-a-project
```shell
symfony project:init
```

composer require platformsh/symfonyflex-bridge
composer require php-amqplib/php-amqplib
composer require symfony/amqp-messenger
composer require amphp/http-client
npm install require
npm install amqplib

Pour voir l'UI :
ssh $(platform ssh --pipe) -L 15672:rabbitmq.internal:15672
et ouvrir http://localhost:15672