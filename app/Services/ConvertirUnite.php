<?php

namespace App\Services;

use App\Models\Unite;
use InvalidArgumentException;

class ConvertirUnite
{
    // On définit les types pour correspondre à ta colonne 'type' en BDD
    const TYPE_MASSE = 'masse';
    const TYPE_VOLUME = 'volume';

    /**
     * Convertit une masse (ex: de 'kg' vers 'g')
     */
    public function convertirMasse(float $valeur, string $deSymbole, string $versSymbole): float
    {
        return $this->executerConversion($valeur, $deSymbole, $versSymbole, self::TYPE_MASSE);
    }

    /**
     * Convertit un volume (ex: de 'L' vers 'ml')
     */
    public function convertirVolume(float $valeur, string $deSymbole, string $versSymbole): float
    {
        return $this->executerConversion($valeur, $deSymbole, $versSymbole, self::TYPE_VOLUME);
    }

    /**
     * Retourne toutes les conversions possibles pour une masse donnée
     * Utilise la méthode siblings() de ton modèle.
     */
    public function toutesLesMasses(float $valeur, string $deSymbole): array
    {
        $source = $this->trouverUnite($deSymbole, self::TYPE_MASSE);

        $resultats = [];
        // Utilisation du helper siblings() défini dans ton modèle
        foreach ($source->siblings() as $uniteCible) {
            $resultats[$uniteCible->symbol] = ($valeur * $source->factor) / $uniteCible->factor;
        }

        return $resultats;
    }

    /**
     * Retourne toutes les conversions possibles pour un volume donné
     */
    public function tousLesVolumes(float $valeur, string $deSymbole): array
    {
        $source = $this->trouverUnite($deSymbole, self::TYPE_VOLUME);

        $resultats = [];
        foreach ($source->siblings() as $uniteCible) {
            $resultats[$uniteCible->symbol] = ($valeur * $source->factor) / $uniteCible->factor;
        }

        return $resultats;
    }

    // ─── Logique Interne ─────────────────────────────────────────────────────

    /**
     * Centralise la logique de calcul et de vérification de type
     */
    private function executerConversion(float $valeur, string $de, string $vers, string $typeAttendu): float
    {
        if ($de === $vers) return $valeur;

        $source = $this->trouverUnite($de, $typeAttendu);
        $cible  = $this->trouverUnite($vers, $typeAttendu);

        // Formule : (Valeur * Facteur Source) / Facteur Cible
        return ($valeur * $source->factor) / $cible->factor;
    }

    /**
     * Trouve une unité par son symbole et vérifie son type via le scope de ton modèle
     */
    private function trouverUnite(string $symbole, string $type): Unite
    {
        $unite = Unite::ofType($type)->where('symbol', $symbole)->first();

        if (!$unite) {
            throw new InvalidArgumentException(
                "L'unité « {$symbole} » est introuvable pour le type « {$type} »."
            );
        }

        return $unite;
    }
    /**
     * Exemple : Convertir 5 sacs en kg (ou inversement)
     * $valeur = 5, $deSymbole = 'sac', $versSymbole = 'kg'
     */
    public function convertirStock(float $valeur, string $deSymbole, string $versSymbole): float
    {
        // 1. Trouver les unités dans la BDD
        $uniteSource = Unite::where('symbol', $deSymbole)->first(); // ex: sac (facteur 50)
        $uniteCible  = Unite::where('symbol', $versSymbole)->first(); // ex: kg (facteur 1)

        // 2. Application mathématique
        // (5 sacs * 50) / 1 = 250 kg
        return ($valeur * $uniteSource->factor) / $uniteCible->factor;
    }
}
