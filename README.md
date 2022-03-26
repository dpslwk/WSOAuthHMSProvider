# WSOAuthHMSProvider

This extension adds a [HMS](http://github.com/NottingHack/hms2/tree/main/app/HMS) 
`AuthProvider` to WSOAuth.

This requires that PluggableAuth and WSOAuth are also installed


```
$wgGroupPermissions['*']['autocreateaccount'] = true;

wfLoadExtension( 'PluggableAuth' );
wfLoadExtension( 'WSOAuth' );
wfLoadExtension( 'WSOAuthHMSProvider' );

$wgPluggableAuth_EnableAutoLogin = true; // ??
$wgPluggableAuth_EnableLocalLogin = false; // ??

$wgPluggableAuth_ButtonLabelMessage = "wsoauth-hmsprovider-pluggable-auth-button-label-message";
$wgOAuthUri = 'https://<HMS domain>/';
$wgOAuthClientId = '<The client ID (key) you received from HMS, php artisan passport:client>';
$wgOAuthClientSecret = '<The secret you received from HMS>';
$wgOAuthRedirectUri = 'https://<wiki domain>/index.php/Special:PluggableAuthLogin';
$wgOAuthAuthProvider = 'hms';
$wgOAuthAutoPopulateGroups = []; // ?? 
$wgOAuthMigrateUsersByUsername = false; // ??
```

