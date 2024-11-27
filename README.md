# Elecretanta

# Install PHP, Composer, and Laravel

If you don't already have PHP, Composer, and Laravel, use one of the following commands:

## MacOS

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.3)"
```

## Windows

```sh
# Run as administrator...
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.3'))
```

## Linux

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.3)"
```

## Install PHP packages and npm dependencies

```sh
composer i
```

```sh
npm i
```

## Update your .env
Copy the values from .env.example and update the appropriate values

```sh
DB_CONNECTION=
DB_HOST=
DB_PORT=
DB_DATABASE=
DB_USERNAME=
DB_PASSWORD=
```

## If your OS is Windows specific

C: > Users > yourUsernameHere > .config -> herd-lite -> bin -> php.ini, set the value for `variables_order` to `GPCS`

## Run the app locally

```sh
php artisan serve
```
