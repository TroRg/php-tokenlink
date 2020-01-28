# tokenlink
Helps to generate tokens which protect links from hotcopy and allow to pass own non secured attributes in id field.

## Usage examples

### Create new token

```php
<?php

use trorg\tokenlink\{Identificator, Token};

$config = [
    'secret' => 'my_secret',
    'ttl' => 86400,
    'idFields' => ['user_id', 'browser_id', 'user_ip', 'time'],
];

$id = new Identificator([
    'user_id' => 1,
    'browser_id' => 2,
    'user_ip' => '8.8.8.8',
    'time' => time(),
]);

$token = new Token($id, $config);

$protectedUrl = sprintf('https://mysite.com/hotcopyprotection/%s/myfile.txt', $token);
```

### Load and verify token
```php
<?php
use trorg\tokenlink\{Identificator, Token};

$config = [
    'secret' => 'my_secret',
    'ttl' => 86400,
    'idFields' => ['user_id', 'browser_id', 'user_ip', 'time'],
];

$token = Token::load($_GET['token'], $config['idFields']);
if ($token->isValid()) {
    // grant access to file
}
throw new \Exception('Your access is expired!');
```
