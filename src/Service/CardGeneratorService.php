<?php

namespace App\Service;

use DateTime;

class CardGeneratorService
{
    // IIN fictif pour la Caisse d'Épargne
    private const IIN = '401212'; 

    public function generateCardNumber(): string
    {
        // Générer les 9 chiffres du numéro de compte
        $accountNumber = str_pad(rand(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        
        // Concaténer l'IIN et le numéro de compte
        $cardNumberBase = self::IIN . $accountNumber;

        // Calculer le dernier chiffre (chiffre de contrôle) avec l'algorithme de Luhn
        $checksum = $this->calculateLuhnChecksum($cardNumberBase);
        
        // Ajouter le chiffre de contrôle pour compléter le numéro de carte
        $cardNumber = $cardNumberBase . $checksum;
        
        // Formater le numéro de carte pour ressembler à un format standard
        return implode(' ', str_split($cardNumber, 4));
    }

    private function calculateLuhnChecksum(string $number): int
    {
        $sum = 0;
        $shouldDouble = false;

        // Parcourir le numéro de carte de droite à gauche
        for ($i = strlen($number) - 1; $i >= 0; $i--) {
            $digit = (int) $number[$i];

            // Doubler chaque deuxième chiffre
            if ($shouldDouble) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9; // Ajouter les chiffres du résultat
                }
            }

            $sum += $digit;
            $shouldDouble = !$shouldDouble; // Alterner entre doubler et ne pas doubler
        }

        // Le chiffre de contrôle est celui qui rend la somme divisible par 10
        return (10 - ($sum % 10)) % 10;
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
