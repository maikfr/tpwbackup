# Description

This project provides two simple commands to download and store offline-backups from teampassword.com. Included are:

* encrypted backup (json) file
* encrypted private key (for decryption of the backup json file)

This project is NOT a part of the official teampassword.com project and comes without any warranty or similar things... Please see the LICENSE file for more information. 
If something breaks or these commands become incompatible (for example if the offical teampassword.com API changes), please feel free to fix and send a pull request.

# Setup

Dependencies: php7, php7.0-xml
 
```
$ php ./composer.phar install
```

# Usage

```
$ php ./bin/console teampassword:backup --help

Usage:
  teampassword:backup [options] [--] <username> <backup-directory>

Arguments:
  username                 The teampassword.com username for login.
  backup-directory         The directory where to save teampassword.com backups and private-key.

Options:
      --password=PASSWORD  The teampassword.com password for login.
  -h, --help               Display this help message
  -q, --quiet              Do not output any message
  -V, --version            Display this application version
      --ansi               Force ANSI output
      --no-ansi            Disable ANSI output
  -n, --no-interaction     Do not ask any interactive question
  -v|vv|vvv, --verbose     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Create a backup from teampassword.com and store it (including the private key) in the filesystem.

$ php ./bin/console teampassword:backup:decrypt --help

Usage:
  teampassword:backup:decrypt [options] [--] <backup-file> <private-key>

Arguments:
  backup-file              The teampassword.com backup file.
  private-key              The encrypted private-key file.

Options:
      --password=PASSWORD  The password for the encrypted private-key.
  -h, --help               Display this help message
  -q, --quiet              Do not output any message
  -V, --version            Display this application version
      --ansi               Force ANSI output
      --no-ansi            Disable ANSI output
  -n, --no-interaction     Do not ask any interactive question
  -v|vv|vvv, --verbose     Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Help:
  Decrypt an teampassword.com backup.

```

# LICENSE

see LICENSE file