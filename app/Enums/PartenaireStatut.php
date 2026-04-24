<?php

namespace App\Enums;

enum PartenaireStatut: string
{
    case A_PROSPECTER       = 'a_prospecter';
    case EN_PROSPECTION     = 'en_prospection';
    case PROSPECT_COMMERCIAL = 'prospect_avec_commercial';
    case PLANIFIE           = 'planifie';
    case DESACTIVE          = 'desactive';

    /**
     * Label lisible pour l'affichage UI
     */
    public function label(): string
    {
        return match($this) {
            self::A_PROSPECTER        => 'À prospecter',
            self::EN_PROSPECTION      => 'En prospection',
            self::PROSPECT_COMMERCIAL => 'Prospect avec commercial',
            self::PLANIFIE            => 'Planifié',
            self::DESACTIVE           => 'Désactivé',
        };
    }

    /**
     * Couleur badge (Tailwind / CSS class)
     */
    public function color(): string
    {
        return match($this) {
            self::A_PROSPECTER        => 'gray',
            self::EN_PROSPECTION      => 'blue',
            self::PROSPECT_COMMERCIAL => 'purple',
            self::PLANIFIE            => 'yellow',
            self::DESACTIVE           => 'red',
        };
    }

    /**
     * Transitions autorisées depuis ce statut
     */
    public function transitionsAutorisees(): array
    {
        return match($this) {
            self::A_PROSPECTER        => [self::EN_PROSPECTION],
            self::EN_PROSPECTION      => [self::PROSPECT_COMMERCIAL, self::DESACTIVE],
            self::PROSPECT_COMMERCIAL => [self::PLANIFIE, self::DESACTIVE],
            self::PLANIFIE            => [self::DESACTIVE],
            self::DESACTIVE           => [self::A_PROSPECTER], // réactivation possible
        };
    }

    /**
     * Vérifie si la transition vers un autre statut est autorisée
     */
    public function peutPasserA(self $nouveau): bool
    {
        return in_array($nouveau, $this->transitionsAutorisees());
    }

    /**
     * Retourne tous les cas sous forme de tableau [value => label]
     * Utile pour les selects Vue.js
     */
    public static function options(): array
    {
        return array_map(
            fn(self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}