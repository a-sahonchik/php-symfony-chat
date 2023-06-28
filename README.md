# php-symfony-chat

Chat application that allows users to exchange text messages with possibility to attach pictures.

User roles are:
- `Guest` - unauthorized user, can only read messages.
- `User` - can read and write messages, also can edit and delete own messages.
- `Moderaotr` - like `User`, but can delete messages of any users, also can block users in chat.
- `Admin` - like `Moderator`, but also can access admin panel, where he able to create, edit, block and delete users and delete messages.

## how to install

```bash
git clone https://github.com/a-sahonchik/php-symfony-chat.git

cd php-symfony-chat

docker-compose build --no-cache
```

## how to run
```bash
docker-compose up
```

App is served at http://localhost/

Test users are created automatically, their credentials are (`username`:`password`):
- `user`:`user`
- `moder`:`moder`
- `admin`:`admin`

Admin user can access admin panel at http://localhost/admin/ or by clicking `Admin panel` menu item (visible only for authorized admins).

## useful console commands

It's possible to create users via console commands (inside `php` container).


```bash
bin/console app:create:<role> <username> <password>
```

Possible `<role>` is one of: `user`, `moderator`, `admin`.

For example, command `bin/console app:create:moderator xxx yyy` will create user with role `moderator` and `xxx`:`yyy` credentials.