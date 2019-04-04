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