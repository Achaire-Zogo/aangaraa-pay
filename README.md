# Aangaraa Pay Laravel Package

Un package Laravel pour intégrer facilement les paiements MTN Mobile Money et Orange Money au Cameroun via votre propre API.

## Installation

```bash
composer require zaz-aangaraa/pay
```

## Configuration

1. Publiez les fichiers de configuration et les migrations :

```bash
php artisan vendor:publish --tag=aangaraa-pay
```

2. Exécutez les migrations :

```bash
php artisan migrate
```

3. Ajoutez les variables d'environnement suivantes à votre fichier `.env` :

```env
# Aangaraa Pay Configuration
AANGARAA_PAY_API_URL=https://votre-api-url.com/api
AANGARAA_PAY_APP_KEY=votre_app_key
MTN_CALLBACK_URL=https://votre-site.com/callback/mtn
MTN_NOTIFY_URL=https://votre-site.com/notify/mtn
ORANGE_CALLBACK_URL=https://votre-site.com/callback/orange
ORANGE_NOTIFY_URL=https://votre-site.com/notify/orange
```

## Utilisation

### Initialiser un paiement

```php
use Aangaraa\Pay\Facades\AangaraaPay;

// Initialiser un paiement
$payment = AangaraaPay::initializePayment([
    'phone_number' => '237XXXXXXXXX',
    'amount' => 1000,
    'description' => 'Paiement pour service X',
    'app_key' => 'votre_app_key',
    'transaction_id' => 'unique_transaction_id',
    'operator' => 'MTN_Cameroon', // ou 'Orange_Cameroon'
    'currency' => 'XAF',
]);
```

### Vérifier le statut d'un paiement

```php
$status = AangaraaPay::checkPaymentStatus('unique_transaction_id');
```

### Retirer de l'argent

```php
$response = AangaraaPay::withdrawMoney([
    'phone_number' => '237XXXXXXXXX',
    'amount' => 500,
    'app_key' => 'votre_app_key',
    'operator' => 'MTN_Cameroon', // ou 'Orange_Cameroon'
]);
```

## Gestion des Webhooks

Le package gère automatiquement les webhooks de notification pour MTN et Orange Money. Assurez-vous que vos URLs de notification sont accessibles publiquement.

## Sécurité

Le package utilise votre `app_key` pour authentifier les requêtes. Assurez-vous de ne jamais partager votre clé et de la stocker de manière sécurisée.

## Support

Pour toute question ou problème, veuillez créer une issue sur le dépôt GitHub.
