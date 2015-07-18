# Le Consortium Horizon - web apps

Le consortium horizon propose un ensemble d'applications web dont les sources sont centralisées dans ce repository :

- CMS : [Wordpress](https://github.com/WordPress/WordPress)
- Forum : [Vanilla](https://github.com/vanilla/vanilla)
- Wiki : [Mediawiki](https://github.com/wikimedia/mediawiki)


## Prérequis

Sont nécessaires les éléments suivants :

- PHP 5.x
- MySQL 5.x

### Mac

```
# Install Homebrew
$ ruby -e "$(curl -fsSL https://raw.githubusercontent.com/Homebrew/install/master/install)"
$ brew update && brew upgrade

# Install PHP-FPM
$ brew tap homebrew/dupes
$ brew tap homebrew/php
$ brew install --without-apache --with-fpm --with-mysql php56

# Setup auto-start
$ mkdir -p ~/Library/LaunchAgents
$ ln -sfv /usr/local/opt/php56/homebrew.mxcl.php56.plist ~/Library/LaunchAgents/
$ launchctl load -w ~/Library/LaunchAgents/homebrew.mxcl.php56.plist

# Install MySQL
$ brew install mysql
$ ln -sfv /usr/local/opt/mysql/*.plist ~/Library/LaunchAgents
$ launchctl load ~/Library/LaunchAgents/homebrew.mxcl.mysql.plist

# Install Caddyserver
$ brew install caddy
```

## Installation

```
$ bower install
```

## Développement

```
# Run the local server
$ caddy
```
