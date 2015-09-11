Deployment Notes
========================

When deploying to the AWS server, if there are any composer related changes, such as autoloading new components you will have to run 'composer update' on the server (preferably as part of an automated deployment process). The vendor directly amongst others aren't under version control, however will be installed as dependancies on initial deployment to a server through running 'composer install'.

Case sensitivity appears to be different between environments, on a local OSX environmen when requiring or autoloading classes, case sensitivity wasn't an issue. However on AWS this is strict and must be adhered to.