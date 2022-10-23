# Arquigrafia

The Arquigrafia project intends to research how the individual and the collective
knowledge building processes relate to each other, by sharing subjectivities on interactive and communicative experiences based on an online collective gallery of Brazilian architecture digital images.

Considering the need for specific iconographic galleries, organized and open for free public access on the Internet, this project gathers a multidisciplinary research team to work on the design and construction of a social network on the Web 2.0 focused on Brazilian architecture digital images, whose operation dynamics will support the study of knowledge building processes.

So far, the project has focused on photographic images but, in a next step, drawings and
videos will be added. The collective network composed of digital image galleries will complement the current visual bases on Brazilian architecture and, later, may be extended to world architecture. The aim of this project is to stimulate architectural visual culture, promoting interaction between architecture students, teachers,
professional architects and, in a broader approach, among all those interested in architectural images.

Keywords: Architecture, Iconography, Web 2.0, Collaborative Systems, Collective intelligence.

## License

The Arquigrafia project is open-sourced software licensed under the [Creative Commons Attribution 3.0](http://creativecommons.org/licenses/by/3.0/deed.pt_BR).

## Prerequisites

- Composer
- PHP 5.6
- Mysql 5.5.3 (higher versions may throw a database exception when running migrations)

## Configuration Tutorial

- Get the dependencies and generate the application key:

```bash
composer install /
php artisan key:generate
```

- Create your mysql database and place the configuration at *app/config/local/database.php*. Some migrations use sql queries that references the database name as "arquigrafia", so you must keep this pattern or change those files.

- Run the migrations.

```bash
php artisan migrate
```

This project use Laravel Messenger for Laravel 4, and need to run migrate:

```bash
php artisan migrate --package=cmgmyr/messenger
```

- Since we're still using PHP 5.6, some JSON post requests might throw an deprecation warning. So, we need to set on PHP.ini:

```
always_populate_raw_post_data = -1
```

- Some features like user registration depends on smpt, so place the mail credentials at app/config/local/mail.php. Check for these [here](https://support.google.com/accounts/answer/185833?hl=en#app-passwords).


## Important comands

### Yarn commands
You can use [yarn](https://yarnpkg.com/en/) to call some comands.

- Starting PHP server

```
$ yarn php
```

- Starting JS server (auto-bundle)

```
$ yarn start
```

- Starting Jest test server

```
$ yarn test:watch
```

- Build JS files for production

```
$ yarn build
```
