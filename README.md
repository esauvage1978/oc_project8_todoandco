# P8-ToDoAndCo

## Openclassrooms / Projet 8 : Améliorez une application existante de ToDo & Co
[![Codacy Badge](https://api.codacy.com/project/badge/Grade/cb926ba7026f4725b229c98f0b3ac5d0)](https://www.codacy.com/manual/esauvage1978/oc_project8_todoandco?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=esauvage1978/oc_project8_todoandco&amp;utm_campaign=Badge_Grade)

## Installation
1. Copy the GitHub's repository in local directory :
```
    git clone https://github.com/esauvage1978/oc_project8_todoandco.git
```
2. Modify the file `.env` .

3. Update the project with [Composer](https://getcomposer.org/download/) :
```
    composer install
```
4. Create the database :
```
    php bin/console doctrine:database:create
```
5. Create table :
```
    php bin/console make:migration
    php bin/console doctrine:migrations:migrate
```
6. (Optional) Create fixtures in dev environnement :
```
    php app/console doctrine:fixtures:load --env=dev 
```
