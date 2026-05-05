<?php

namespace App\Imports;

use App\Enums\ContactStatut;
use App\Models\Contact;
use App\Models\Import;
use App\Models\Partenaire;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class PartenairesImport implements ToCollection
{

    private Import $importRecord;
    private int $rowsImported = 0;
    private int $rowsSkipped  = 0;

    public function __construct(Import $importRecord)
    {
        $this->importRecord = $importRecord;
    }
    public function collection(Collection $rows)
    {
        try {
            // Chercher la ligne d'en-tête — soit avec RAISON col 4, soit avec RAISON col E
            $header = $rows->first(
                fn($row) =>
                collect($row)->contains(
                    fn($val) =>
                    str_contains(mb_strtoupper((string)$val, 'UTF-8'), 'RAISON')
                )
            );

            if (!$header) return;

            $map = $this->mapColumns($header);

            foreach ($rows as $row) {
                // Sauter les lignes d'en-tête
                if (collect($row)->contains(
                    fn($val) =>
                    str_contains(mb_strtoupper((string)$val, 'UTF-8'), 'RAISON')
                )) {
                    continue;
                }

                // Sauter les lignes de titre fusionné type "Infos générales"
                if (collect($row)->contains(
                    fn($val) =>
                    str_contains(mb_strtoupper((string)$val, 'UTF-8'), 'INFOS') ||
                        str_contains(mb_strtoupper((string)$val, 'UTF-8'), 'GÉNÉRAL')
                )) {
                    continue;
                }

                // Sauter si pas de raison sociale
                if (empty($row[$map['raison']])) {
                    continue;
                }

                // Sauter si colonne Etat contient le titre
                if (isset($row[$map['etat']]) && in_array($row[$map['etat']], ['Etat', 'statut', 'État'])) {
                    continue;
                }

                $siret = $this->cleanSiret($row[$map['siret']] ?? null);
                $partenaire = Partenaire::updateOrCreate(
                    ['siret' => $siret],
                    [
                        'raison_sociale'   => $this->truncate($row[$map['raison']] ?? null, 255),
                        'adresse'          => $this->truncate($row[$map['adresse']] ?? null, 255),
                        'cp'               => $this->truncate($row[$map['cp']] ?? null, 10),
                        'ville'            => $this->truncate($row[$map['ville']] ?? null, 255),
                        'nbrs_salaries'    => $this->toNum($row[$map['salaries']] ?? null),
                        'secteur_activite' => $this->truncate($row[$map['secteur']] ?? null, 255),
                        'telephone_1'      => $this->truncate($row[$map['tel1']] ?? null, 20),
                        'telephone_2'      => $this->truncate($row[$map['tel2']] ?? null, 20),
                        'ca'               => $this->toNum($row[$map['ca']] ?? null),
                    ]
                );

                // Créer le contact si conseiller ou etat présent
                $hasConseiller = !empty($row[$map['conseiller'] ?? 0]);
                $hasEtat       = !empty($row[$map['etat'] ?? 1]);

                if ($hasConseiller || $hasEtat) {
                    $conseiller = $this->parseConseiller($row[$map['conseiller'] ?? 0] ?? null);

                    // Chercher un contact existant avec ce conseiller, ou en créer un seul
                    $contact = Contact::firstOrCreate(
                        [
                            'conseiller_nom'    => $conseiller['nom'],
                            'conseiller_prenom' => $conseiller['prenom'],
                        ],
                        [
                            'statut'               => $row[$map['etat'] ?? 1] ?? ContactStatut::A_CONTACTER->value,
                            'date_premier_contact' => $this->parseDate($row[$map['date'] ?? 2] ?? null),
                            'commentaires'         => $row[$map['commentaires'] ?? 3] ?? null,
                        ]
                    );

                    // Attacher le partenaire sans créer de doublon
                    $contact->partenaires()->syncWithoutDetaching([$partenaire->id]);
                }

                \App\Jobs\SyncToVTiger::dispatch($partenaire)->afterCommit();
            }
            $this->importRecord->update([
                'status'        => 'success',
                'rows_imported' => $this->rowsImported,
                'rows_skipped'  => $this->rowsSkipped,
            ]);
        } catch (\Exception $e) {
            $this->importRecord->update([
                'status'        => 'failed',
                'error_message' => $e->getMessage(),
                'rows_imported' => $this->rowsImported,
            ]);
            throw $e;
        }
    }

    private function mapColumns($header): array
    {
        $header = $header->toArray();

        $find = fn($needles) => collect($header)->search(
            fn($val) => collect($needles)->contains(
                fn($n) => str_contains(mb_strtoupper((string)$val, 'UTF-8'), $n)
            )
        );

        // Colonnes gauche (conseiller/etat) — toujours col 0,1,2,3
        $conseiller  = $find(['CONSEIL']) !== false ? $find(['CONSEIL'])  : 0;
        $etat        = $find(['ETAT', 'ÉTAT', 'STATUT']) !== false ? $find(['ETAT', 'ÉTAT', 'STATUT']) : 1;
        $date        = $find(['DATE', '1ER', 'PREMIER']) !== false ? $find(['DATE', '1ER', 'PREMIER']) : 2;
        $commentaires = $find(['COMMENT', 'SITUATION']) !== false ? $find(['COMMENT', 'SITUATION']) : 3;

        // Colonnes droite (infos entreprise)
        $raison   = ($r = $find(['RAISON', 'SOCIALE']))   !== false ? $r : 4;
        $adresse  = ($r = $find(['ADRESSE']))             !== false ? $r : 5;
        $cp       = ($r = $find(['CP', 'CODE POSTAL']))   !== false ? $r : 6;
        $ville    = ($r = $find(['VILLE']))               !== false ? $r : 7;
        $tel1 = ($r = $find(['TÉLÉPHONE 1', 'TELEPHONE 1', 'TEL 1', 'TEL1', 'TÉL. 1', 'TÉL'])) !== false ? $r : 8;
        $tel2     = ($r = $find(['TÉLÉPHONE 2', 'TELEPHONE 2', 'TEL 2', 'TEL2'])) !== false ? $r : 9;
        $salaries = ($r = $find(['SALARI', 'NBRS', 'NBRE']))  !== false ? $r : 10;
        $secteur  = ($r = $find(['SECTEUR']))             !== false ? $r : 11;
        $siret    = ($r = $find(['SIRET']))               !== false ? $r : 12;
        $ca       = ($r = $find(['CA', 'CHIFFRE']))       !== false ? $r : 13;

        return compact(
            'conseiller',
            'etat',
            'date',
            'commentaires',
            'raison',
            'adresse',
            'cp',
            'ville',
            'tel1',
            'tel2',
            'salaries',
            'secteur',
            'siret',
            'ca'
        );
    }

    // ← NOUVEAU : tronquer les valeurs trop longues
    private function truncate($value, int $max): ?string
    {
        if (is_null($value)) return null;
        $str = trim((string)$value);
        return mb_strlen($str) > $max ? mb_substr($str, 0, $max) : $str;
    }

    private function toNum($val)
    {
        if (is_null($val)) return null;
        $val = preg_replace('/[^0-9.]/', '', (string)$val);
        return is_numeric($val) ? $val : null;
    }

    private function cleanSiret($value): ?string
    {
        return $value ? preg_replace('/[^0-9]/', '', (string)$value) : null;
    }

    private function parseConseiller($value): array
    {
        $parts = explode(' ', trim($value ?? ''), 2);
        return ['nom' => $parts[0] ?? null, 'prenom' => $parts[1] ?? null];
    }

    private function parseDate($value): ?string
    {
        if (!$value) return null;
        try {
            return \Carbon\Carbon::parse($value)->toDateString();
        } catch (\Exception $e) {
            return null;
        }
    }
}
