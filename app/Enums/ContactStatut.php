<?php

namespace App\Enums;

enum ContactStatut: string
{
    case A_CONTACTER    = 'a_contacter';
    case CONTACTE       = 'contacte';
    case RDV_PLANIFIE   = 'rdv_planifie';
    case RDV_EFFECTUE   = 'rdv_effectue';
    case SANS_SUITE     = 'sans_suite';
    case CONVERTI       = 'converti';

    public function label(): string
    {
        return match($this) {
            self::A_CONTACTER  => 'À contacter',
            self::CONTACTE     => 'Contacté',
            self::RDV_PLANIFIE => 'RDV planifié',
            self::RDV_EFFECTUE => 'RDV effectué',
            self::SANS_SUITE   => 'Sans suite',
            self::CONVERTI     => 'Converti',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::A_CONTACTER  => 'gray',
            self::CONTACTE     => 'blue',
            self::RDV_PLANIFIE => 'yellow',
            self::RDV_EFFECTUE => 'green',
            self::SANS_SUITE   => 'red',
            self::CONVERTI     => 'emerald',
        };
    }

    public function transitionsAutorisees(): array
    {
        return match($this) {
            self::A_CONTACTER  => [self::CONTACTE, self::SANS_SUITE],
            self::CONTACTE     => [self::RDV_PLANIFIE, self::SANS_SUITE],
            self::RDV_PLANIFIE => [self::RDV_EFFECTUE, self::SANS_SUITE],
            self::RDV_EFFECTUE => [self::CONVERTI, self::SANS_SUITE],
            self::SANS_SUITE   => [self::A_CONTACTER], 
            self::CONVERTI     => [], 
        };
    }

    public function peutPasserA(self $nouveau): bool
    {
        return in_array($nouveau, $this->transitionsAutorisees());
    }

    public static function options(): array
    {
        return array_map(
            fn(self $case) => ['value' => $case->value, 'label' => $case->label()],
            self::cases()
        );
    }
}