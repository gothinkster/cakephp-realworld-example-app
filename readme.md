# ![CakePHP RealWorld Example App](logo.png)

> ### Example CakePHP codebase containing real world examples (CRUD, auth, advanced patterns and more) that adheres to the [RealWorld](https://github.com/gothinkster/realworld-example-apps) spec and API.

This repo is functionality complete — PRs and issues welcome!

----------

# Getting started

## Installation

Please check the official cakephp installation guide for server requirements before you start. [Official Documentation](https://book.cakephp.org/3.0/en/installation.html)

Clone the repository

    git clone git@github.com:CakeDC/cakephp-realworld-example-app.git

Switch to the repo folder

    cd cakephp-realworld-example-app

Install all the dependencies using composer

    composer install

Copy the example app config file and make the required configuration changes in the config/app.php file

    cp config/app.php.default config/app.php

Run the database migrations (**Set the database connection in app.php**)

    bin/cake migrations migrate
	
## API Specification

This application adheres to the api specifications set by the [Thinkster](https://github.com/gothinkster) team. This helps mix and match any backend with any other frontend without conflicts.

> [Full API Spec](https://github.com/gothinkster/realworld/tree/master/api)

For more information on how to this works with other frontends/backends, head over to the [RealWorld](https://github.com/gothinkster/realworld) repo.


# How it works

> Describe the general architecture of your app here

# Getting started

> npm install, npm start, etc.

