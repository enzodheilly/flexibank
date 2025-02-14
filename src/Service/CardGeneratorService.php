<?php

namespace App\Service;

use DateTime;

class CardGeneratorService
{
    public function generateCardNumber(): string
    {
        // Exemple de génération de numéro de carte
        return '4111 1111 1111 ' . rand(1000, 9999);
    }

    public function generateCCV(): string
    {
        // Exemple de génération de CCV
        return str_pad(rand(0, 999), 3, '0', STR_PAD_LEFT);
    }

    public function generateExpirationDate(): DateTime
    {
        // Retourner un objet DateTime pour la date d'expiration
        return new DateTime('+3 years');
    }
}
