# DoctrineEncryptBundle

Bundle allows to create doctrine entities with fields that will be protected with 
help of some encryption algorithm in database and it will be clearly for developer, because bundle is uses doctrine life cycle events.

### Installation

```bash
composer require sfcod/doctrine-encrypt
```

### Configuration

```yaml
sfcod_doctrine_encrypt:
  secret_key: '%env(ENC_KEY)%' # secret key (string), if is not set, APP_SECRET .env variable will be used
  secret_iv: '%env(ENC_IV)%' # secret initialization vector (string), if is not set, APP_SECRET .env variable will be used
  encryptor_class: ~ # you can provide here custom encryptor wich implements SfCod\DoctrineEncrypt\Encryptors\EncryptorInterface
```

### What does it do exactly

It gives you the opportunity to add the @Encrypt annotation above each string property:

```php
use SfCod\DoctrineEncrypt\Configuration\Encrypted;
...

/**
 * @Encrypt
 */
protected $username;
```

The bundle uses doctrine his life cycle events to encrypt the data when inserted into the database and decrypt the data when loaded into your entity manager.
It is only able to encrypt only string values at the moment.

### Advantages and disadvantaged of an encrypted database:

#### Advantages
- Information is stored safely
- Not worrying about saving backups at other locations
- Unreadable for employees managing the database

#### Disadvantages
- Can't use ORDER BY on encrypted data
- In SELECT WHERE statements the where values also have to be encrypted
- When you lose your key you lose your data (Make a backup of the key on a safe location)

### Commands

To make your life a little easier we created some commands that you can use for encrypting and decrypting your current database.

#### 1) Get status

You can use the comment `doctrine:encrypt:status` to get the current database and encryption information.

```
$ php app/console doctrine:encrypt:status
```

This command will return the amount of entities and the amount of properties with the @Encrypted tag for each entity.
The result will look like this:

```
DoctrineEncrypt\Entity\User has 3 properties which are encrypted.
DoctrineEncrypt\Entity\UserDetail has 13 properties which are encrypted.

2 entities found which are containing 16 encrypted properties.
```

#### 2) Encrypt current database

You can use the comment `doctrine:encrypt:database [encryptor]` to encrypt the current database.

* Optional parameter [encryptor]
    * An encryptor provided by the bundle or your own encryption class.
    * Default: Your encryptor set in the configuration file or the default encryption class when not set in the configuration file

```
$ php bin/console doctrine:encrypt:database
```

or you can provide an encryptor (optional).

```
$ php bin/console doctrine:encrypt:database App\Encryptors\Encryptor
```

This command will return the amount of values encrypted in the database.

```
Encryption finished values encrypted: 203 values.
```


#### 3) Decrypt current database

You can use the comment `doctrine:decrypt:database [encryptor]` to decrypt the current database.

* Optional parameter [encryptor]
    * An encryptor provided by the bundle or your own encryption class.
    * Default: Your encryptor set in the configuration file or the default encryption class when not set in the configuration file

```
$ php bin/console doctrine:encrypt:database
```

or you can provide an encryptor (optional).

```
$ php bin/console doctrine:encrypt:database App\Encryptors\Encryptor
```

This command will return the amount of entities and the amount of values decrypted in the database.

```
Decryption finished entities found: 26, decrypted 195 values.
```