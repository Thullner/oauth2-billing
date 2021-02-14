<?php

require __DIR__ . './vendor/autoload.php';


use League\OAuth2\Client\Provider\Billing;

$provider = new Billing([
    'clientId'          => 'plugin',
    'clientSecret'      => 'secret',
    'redirectUri'       => 'https://oauth2-billing.test/example.php',
]);

//$user = $provider->getResourceOwner(new \League\OAuth2\Client\Token\AccessToken(['access_token' => 'test']));
//var_dump($user->toArray());
//die();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_GET['code'])) {


    // If we don't have an authorization code then get one
    $authUrl = $provider->getAuthorizationUrl();


    $_SESSION['oauth2state'] = $provider->getState();

    header('Location: ' . $authUrl);
    exit;
// Check given state against previously stored one to mitigate CSRF attack

} elseif (empty($_GET['state']) || ($_GET['state'] !== $_SESSION['oauth2state'])) {
    unset($_SESSION['oauth2state']);
    exit('Invalid state');
}
else {

    // Try to get an access token (using the authorization code grant)
    $token = $provider->getAccessToken('authorization_code', [
        'code' => $_GET['code'],
    ]);

    // Optional: Now you have a token you can look up a users profile data
    try {

        var_dump($token);
        // We got an access token, let's now get the user's details
        $user = $provider->getResourceOwner($token);

        // Use these details to create a new profile
        var_dump($user->toArray());

    } catch (Exception $e) {

        // Failed to get user details
        exit('Oh dear...');
    }
}
