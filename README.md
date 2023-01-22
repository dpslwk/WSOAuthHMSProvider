# WSOAuthHMSProvider

This extension adds a [HMS](http://github.com/NottingHack/hms2/tree/main/app/HMS) 
`AuthProvider` to WSOAuth.

This requires that PluggableAuth and WSOAuth are also installed

HMS generate passport client`php artisan passport:client`  
User: leave blank  
Redirect: `https://<wiki domain>/index.php/Special:PluggableAuthLogin`

```
$wgGroupPermissions['*']['autocreateaccount'] = true;

$wgPluggableAuth_EnableAutoLogin = false;
$wgPluggableAuth_EnableLocalLogin = true;

$wgPluggableAuth_Config['hms'] = [
    'plugin' => 'WSOAuth',
    'data' => [
        'type' => 'hms',
        'uri' => 'https://hmsdev.test/',
        'clientId' => '9',
        'clientSecret' => 'ctYIiYVX1oIVrOSXroPn2jRIkxCb4FsMEVpjoVYb',
        'redirectUri' => 'http://wiki.test/index.php/Special:PluggableAuthLogin',
    ],
    'buttonLabelMessage' => 'wsoauth-hmsprovider-pluggable-auth-button-label-message'
];
$wgOAuthAutoPopulateGroups = [];
$wgOAuthMigrateUsersByUsername = false;
```
