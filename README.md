# PCsoft Tamkeen SDK

<a href="https://github.com/pcsoftgroup/tamkeen-php-sdk/actions"><img src="https://github.com/pcsoftgroup/tamkeen-php-sdk/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/pcsoftgroup/tamkeen-php-sdk"><img src="https://img.shields.io/packagist/dt/pcsoftgroup/tamkeen-php-sdk" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/pcsoftgroup/tamkeen-php-sdk"><img src="https://img.shields.io/packagist/v/pcsoftgroup/tamkeen-php-sdk" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/pcsoftgroup/tamkeen-php-sdk"><img src="https://img.shields.io/packagist/l/pcsoftgroup/tamkeen-php-sdk" alt="License"></a>

## Introduction

The [PCsoft Tamkeen](https://tamkeen.pcsoftgroup.com) SDK provides an expressive interface for interacting with Tamkeen's API and managing PCsoft Tamkeen servers.

## Official Documentation

### Installation

To install the SDK in your project you need to require the package via composer:

```bash
composer require pcsoftgroup/tamkeen-php-sdk
```

### Upgrading

When upgrading to a new major version of Tamkeen SDK, it's important that you carefully review [the upgrade guide](https://github.com/pcsoftgroup/tamkeen-php-sdk/blob/master/UPGRADE.md).

### Basic Usage

You can create an instance of the SDK like so:

```php
$tamkeen = new PCsoft\Tamkeen\Tamkeen(TOKEN_HERE);
```

On multiple actions supported by this SDK you may need to pass some parameters, for example when creating a new server:

```php
$server = $tamkeen->createServer([
    "provider"=> ServerProviders::DIGITAL_OCEAN,
    "credential_id"=> 1,
    "name"=> "test-via-api",
    "type"=> ServerTypes::APP,
    "size"=> "01",
    "database"=> "test123",
    "database_type" => InstallableServices::POSTGRES,
    "php_version"=> InstallableServices::PHP_71,
    "region"=> "ams2"
]);
```

These parameters will be used in the POST request sent to Tamkeen servers, you can find more information about the parameters needed for each action on
[Tamkeen's official API documentation](https://tamkeen.pcsoftgroup.com/api-documentation).

Notice that this request for example will only start the server creation process, your server might need a few minutes before it completes provisioning, you'll need to check the server's `$isReady` property to know if it's ready or not yet.

Some SDK methods however waits for the action to complete on Tamkeen's end, we do this by periodically contacting Tamkeen servers and checking if our action has completed, for example:

```php
$tamkeen->createSite(SERVER_ID, [SITE_PARAMETERS]);
```

This method will ping Tamkeen servers every 5 seconds and see if the newly created Site's status is `installed` and only return when it's so, in case the waiting exceeded 30 seconds a `PCsoft\Tamkeen\Exceptions\TimeoutException` will be thrown.

You can easily stop this behaviour be setting the `$wait` argument to false:

```php
$tamkeen->createSite(SERVER_ID, [SITE_PARAMETERS], false);
```

You can also set the desired timeout value:

```php
$tamkeen->setTimeout(120)->createSite(SERVER_ID, [SITE_PARAMETERS]);
```

### Authenticated User

```php
$tamkeen->user();
```

### Managing Servers

```php
$tamkeen->servers();
$tamkeen->server($serverId);
$tamkeen->createServer(array $data);
$tamkeen->updateServer($serverId, array $data);
$tamkeen->deleteServer($serverId);
$tamkeen->rebootServer($serverId);

// Server access
$tamkeen->revokeAccessToServer($serverId);
$tamkeen->reconnectToServer($serverId);
$tamkeen->reactivateToServer($serverId);
```

On a `Server` instance you may also call:

```php
$server->update(array $data);
$server->delete();
$server->reboot();
$server->revokeAccess();
$server->reconnect();
$server->reactivate();
$server->rebootMysql();
$server->stopMysql();
$server->rebootPostgres();
$server->stopPostgres();
$server->rebootNginx();
$server->stopNginx();
$server->installBlackfire(array $data);
$server->removeBlackfire();
$server->installPapertrail(array $data);
$server->removePapertrail();
$server->enableOPCache();
$server->disableOPCache();
$server->phpVersions();
$server->installPHP($version);
$server->updatePHP($version);
```

### Server SSH Keys

```php
$tamkeen->keys($serverId);
$tamkeen->sshKey($serverId, $keyId);
$tamkeen->createSSHKey($serverId, array $data, $wait = true);
$tamkeen->deleteSSHKey($serverId, $keyId);
```

On a `SSHKey` instance you may also call:

```php
$sshKey->delete();
```

### Server Scheduled Jobs

```php
$tamkeen->jobs($serverId);
$tamkeen->job($serverId, $jobId);
$tamkeen->createJob($serverId, array $data, $wait = true);
$tamkeen->deleteJob($serverId, $jobId);
```

On a `Job` instance you may also call:

```php
$job->delete();
```

### Server Events

```php
$tamkeen->events();
$tamkeen->events($serverId);
```

### Managing Services

```php
// MySQL
$tamkeen->rebootMysql($serverId);
$tamkeen->stopMysql($serverId);

// Postgres
$tamkeen->rebootPostgres($serverId);
$tamkeen->stopPostgres($serverId);

// Nginx
$tamkeen->rebootNginx($serverId);
$tamkeen->stopNginx($serverId);
$tamkeen->siteNginxFile($serverId, $siteId);
$tamkeen->updateSiteNginxFile($serverId, $siteId, $content);

// Blackfire
$tamkeen->installBlackfire($serverId, array $data);
$tamkeen->removeBlackfire($serverId);

// Papertrail
$tamkeen->installPapertrail($serverId, array $data);
$tamkeen->removePapertrail($serverId);

// OPCache
$tamkeen->enableOPCache($serverId);
$tamkeen->disableOPCache($serverId);
```

### Server Daemons

```php
$tamkeen->daemons($serverId);
$tamkeen->daemon($serverId, $daemonId);
$tamkeen->createDaemon($serverId, array $data, $wait = true);
$tamkeen->restartDaemon($serverId, $daemonId, $wait = true);
$tamkeen->deleteDaemon($serverId, $daemonId);
```

On a `Daemon` instance you may also call:

```php
$daemon->restart($wait = true);
$daemon->delete();
```

### Server Firewall Rules

```php
$tamkeen->firewallRules($serverId);
$tamkeen->firewallRule($serverId, $ruleId);
$tamkeen->createFirewallRule($serverId, array $data, $wait = true);
$tamkeen->deleteFirewallRule($serverId, $ruleId);
```

On a `FirewallRule` instance you may also call:

```php
$rule->delete();
```

### Managing Sites

```php
$tamkeen->sites($serverId);
$tamkeen->site($serverId, $siteId);
$tamkeen->createSite($serverId, array $data, $wait = true);
$tamkeen->updateSite($serverId, $siteId, array $data);
$tamkeen->refreshSiteToken($serverId, $siteId);
$tamkeen->deleteSite($serverId, $siteId);

// Add Site Aliases
$tamkeen->addSiteAliases($serverId, $siteId, array $aliases);

// Environment File
$tamkeen->siteEnvironmentFile($serverId, $siteId);
$tamkeen->updateSiteEnvironmentFile($serverId, $siteId, $content);

// Site Repositories and Deployments
$tamkeen->installGitRepositoryOnSite($serverId, $siteId, array $data, $wait = false);
$tamkeen->updateSiteGitRepository($serverId, $siteId, array $data);
$tamkeen->destroySiteGitRepository($serverId, $siteId, $wait = false);
$tamkeen->siteDeploymentScript($serverId, $siteId);
$tamkeen->updateSiteDeploymentScript($serverId, $siteId, $content);
$tamkeen->enableQuickDeploy($serverId, $siteId);
$tamkeen->disableQuickDeploy($serverId, $siteId);
$tamkeen->deploySite($serverId, $siteId, $wait = false);
$tamkeen->resetDeploymentState($serverId, $siteId);
$tamkeen->siteDeploymentLog($serverId, $siteId);
$tamkeen->deploymentHistory($serverId, $siteId);
$tamkeen->deploymentHistoryDeployment($serverId, $siteId, $deploymentId);
$tamkeen->deploymentHistoryOutput($serverId, $siteId, $deploymentId);

// PHP Version
$tamkeen->changeSitePHPVersion($serverId, $siteId, $version);

// Installing Wordpress
$tamkeen->installWordPress($serverId, $siteId, array $data);
$tamkeen->removeWordPress($serverId, $siteId);

// Installing phpMyAdmin
$tamkeen->installPhpMyAdmin($serverId, $siteId, array $data);
$tamkeen->removePhpMyAdmin($serverId, $siteId);

// Updating Node balancing Configuration
$tamkeen->updateNodeBalancingConfiguration($serverId, $siteId, array $data);
```

On a `Site` instance you may also call:

```php
$site->refreshToken();
$site->delete();
$site->installGitRepository(array $data, $wait = false);
$site->updateGitRepository(array $data);
$site->destroyGitRepository($wait = false);
$site->getDeploymentScript();
$site->updateDeploymentScript($content);
$site->enableQuickDeploy();
$site->disableQuickDeploy();
$site->deploySite($wait = false);
$site->resetDeploymentState();
$site->siteDeploymentLog();
$site->getDeploymentHistory();
$site->getDeploymentHistoryDeployment($deploymentId);
$site->getDeploymentHistoryOutput($deploymentId);
$site->installWordPress($data);
$site->removeWordPress();
$site->installPhpMyAdmin($data);
$site->removePhpMyAdmin();
$site->changePHPVersion($version);
```

### Site Workers

```php
$tamkeen->workers($serverId, $siteId);
$tamkeen->worker($serverId, $siteId, $workerId);
$tamkeen->createWorker($serverId, $siteId, array $data, $wait = true);
$tamkeen->deleteWorker($serverId, $siteId, $workerId);
$tamkeen->restartWorker($serverId, $siteId, $workerId, $wait = true);
```

On a `Worker` instance you may also call:

```php
$worker->delete();
$worker->restart($wait = true);
```

### Security Rules

```php
$tamkeen->securityRules($serverId, $siteId);
$tamkeen->securityRule($serverId, $siteId, $ruleId);
$tamkeen->createSecurityRule($serverId, $siteId, array $data);
$tamkeen->deleteSecurityRule($serverId, $siteId, $ruleId);
```

On a `SecurityRule` instance you may also call:

```php
$securityRule->delete();
```

### Site Webhooks

```php
$tamkeen->webhooks($serverId, $siteId);
$tamkeen->webhook($serverId, $siteId, $webhookId);
$tamkeen->createWebhook($serverId, $siteId, array $data);
$tamkeen->deleteWebhook($serverId, $siteId, $webhookId);
```

On a `Webhook` instance you may also call:

```php
$webhook->delete();
```

### Site Commands

```php
$tamkeen->executeSiteCommand($serverId, $siteId, array $data);
$tamkeen->listCommandHistory($serverId, $siteId);
$tamkeen->getSiteCommand($serverId, $siteId, $commandId);
```

### Site SSL Certificates

```php
$tamkeen->certificates($serverId, $siteId);
$tamkeen->certificate($serverId, $siteId, $certificateId);
$tamkeen->createCertificate($serverId, $siteId, array $data, $wait = true);
$tamkeen->deleteCertificate($serverId, $siteId, $certificateId);
$tamkeen->getCertificateSigningRequest($serverId, $siteId, $certificateId);
$tamkeen->installCertificate($serverId, $siteId, $certificateId, array $data, $wait = true);
$tamkeen->activateCertificate($serverId, $siteId, $certificateId, $wait = true);
$tamkeen->obtainLetsEncryptCertificate($serverId, $siteId, $data, $wait = true);
```

On a `Certificate` instance you may also call:

```php
$certificate->delete();
$certificate->getSigningRequest();
$certificate->install($wait = true);
$certificate->activate($wait = true);
```

### Nginx Templates

```php
$tamkeen->nginxTemplates($serverId);
$tamkeen->nginxDefaultTemplate($serverId);
$tamkeen->nginxTemplate($serverId, $templateId);
$tamkeen->createNginxTemplate($serverId, array $data);
$tamkeen->updateNginxTemplate($serverId, $templateId, array $data);
$tamkeen->deleteNginxTemplate($serverId, $templateId);
```

On a `NginxTemplate` instance you may also call:

```php
$nginxTemplate->update(array $data);
$nginxTemplate->delete();
```

### Managing Databases

```php
$tamkeen->databases($serverId);
$tamkeen->database($serverId, $databaseId);
$tamkeen->createDatabase($serverId, array $data, $wait = true);
$tamkeen->updateDatabase($serverId, $databaseId, array $data);
$tamkeen->deleteDatabase($serverId, $databaseId);

// Users
$tamkeen->databaseUsers($serverId);
$tamkeen->databaseUser($serverId, $userId);
$tamkeen->createDatabaseUser($serverId, array $data, $wait = true);
$tamkeen->updateDatabaseUser($serverId, $userId, array $data);
$tamkeen->deleteDatabaseUser($serverId, $userId);
```

On a `Database` instance you may also call:

```php
$database->update(array $data);
$database->delete();
```

On a `DatabaseUser` instance you may also call:

```php
$databaseUser->update(array $data);
$databaseUser->delete();
```

### Managing Recipes

```php
$tamkeen->recipes();
$tamkeen->recipe($recipeId);
$tamkeen->createRecipe(array $data);
$tamkeen->updateRecipe($recipeId, array $data);
$tamkeen->deleteRecipe($recipeId);
$tamkeen->runRecipe($recipeId, array $data);
```

On a `Recipe` instance you may also call:

```php
$recipe->update(array $data);
$recipe->delete();
$recipe->run(array $data);
```

### Managing Backups

```php
$tamkeen->backupConfigurations($serverId);
$tamkeen->createBackupConfiguration($serverId, array $data);
$tamkeen->updateBackupConfiguration($serverId, $backupConfigurationId, array $data);
$tamkeen->backupConfiguration($serverId, $backupConfigurationId);
$tamkeen->deleteBackupConfiguration($serverId, $backupConfigurationId);
$tamkeen->restoreBackup($serverId, $backupConfigurationId, $backupId);
$tamkeen->deleteBackup($serverId, $backupConfigurationId, $backupId);
```

On a `BackupConfiguration` instance you may also call:

```php
$extendedConfig = $backupConfig->get(); // Load the databases also
$backupConfig->update(array $data);
$backupConfig->delete();
$backupConfig->restoreBackup($backupId);
$backupConfig->deleteBackup($backupId);
```

On a `Backup` instance you may also call:

```php
$backupConfig->delete();
$backupConfig->restore();
```

## Contributing

Thank you for considering contributing to Tamkeen SDK! You can read the contribution guide [here](.github/CONTRIBUTING.md).

## Code of Conduct

In order to ensure that the PCsoft community is welcoming to all, please review and abide by the [Code of Conduct](https://pcsoftgroup.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

Please review [our security policy](https://github.com/pcsoftgroup/tamkeen-php-sdk/security/policy) on how to report security vulnerabilities.

## License

PCsoft Tamkeen SDK is open-sourced software licensed under the [MIT license](LICENSE.md).
