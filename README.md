# XenForo [bd] API OAuth2-Server support for the PHP League's OAuth 2.0 Client
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

This package provides XenForo [bd] API OAuth 2.0 support for the PHP League's [OAuth 2.0 Client](https://github.com/thephpleague/oauth2-client).

## Installation

To install, use composer:

```
composer require sas1024/oauth2-xenforo-bdapi
```

## Usage

Usage is the same as The League's OAuth client, using `\Sas1024\OAuth2\Client\Provider\XenForoBdApi` as the provider.

### Authorization Code Flow

```php

use Sas1024\OAuth2\Client\Provider\XenForoBdApi;

require_once('./vendor/autoload.php');
session_start();

$provider = new XenForoBdApi([
    'clientId'          => '{xenforo-bdapi-client-id}',
    'clientSecret'      => '{xenforo-bdapi-client-secret}',
    'redirectUri'       => 'https://example.com/callback-url',
    'baseUrl'           => 'https://example.com',
]);

if (!isset($_GET['code'])) {

    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();
    $_SESSION['oauth2state'] = $provider->getState();
    header('Location: '.$authUrl);
    exit;

// Check given state against previously stored one to mitigate CSRF attack
} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {

    unset($_SESSION['oauth2state']);
    exit('Invalid state');

} else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code']
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        printf('Hello %s!', $user->getUsername());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }

    // Use this to interact with an API on the users behalf
    echo $token->getToken();
}

```

## Testing

``` bash
$ ./vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](https://github.com/sas1024/oauth2-xenforo-bdapi/blob/master/LICENSE) for more information.