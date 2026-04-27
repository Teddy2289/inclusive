<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\Partenaire;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;


class PartenairesImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $header = $rows->first(fn($row) => str_contains(strtoupper($row[4] ?? ''), 'RAISON'));

        if (!$header) return;

        $map = $this->mapColumns($header);

        foreach ($rows as $index => $row) {
            if (str_contains(strtoupper($row[4] ?? ''), 'RAISON') || empty($row[$map['raison']])) {
                continue;
            }

            $siret = $this->cleanSiret($row[$map['siret']] ?? null);

            $partenaire = Partenaire::updateOrCreate(
                ['siret' => $siret],
                [
                    'raison_sociale'   => $row[$map['raison']],
                    'adresse'          => $row[$map['adresse']] ?? null,
                    'cp'               => $row[$map['cp']] ?? null,
                    'ville'            => $row[$map['ville']] ?? null,
                    'nbrs_salaries'    => $this->toNum($row[$map['salaries']] ?? null),
                    'secteur_activite' => $row[$map['secteur']] ?? null,
                    'telephone_1'      => $row[$map['tel1']] ?? null,
                    'telephone_2'      => $row[$map['tel2']] ?? null,
                    'ca'               => $this->toNum($row[$map['ca']] ?? null),
                ]
            );

            if (!empty($row[0]) || !empty($row[1])) {
                $conseiller = $this->parseConseiller($row[0]);
                Contact::create([
                    'partenaire_id'     => $partenaire->id,
                    'conseiller_nom'    => $conseiller['nom'],
                    'conseiller_prenom' => $conseiller['prenom'],
                    'etat'              => $row[1] ?? null,
                    'date_premier_contact' => $this->parseDate($row[2] ?? null),
                    'commentaires'      => $row[3] ?? null,
                ]);
            }


            // On ajoute afterCommit() pour être sûr que les contacts sont créés en base
            // avant que le job ne commence à chercher dans vTiger
            \App\Jobs\SyncToVTiger::dispatch($partenaire)->afterCommit();
        }
    }

    private function mapColumns($header): array
    {
        $header = $header->toArray();
        $find = fn($needles) => collect($header)->search(
            fn($val) =>
            collect($needles)->contains(fn($n) => str_contains(mb_strtoupper((string)$val, 'UTF-8'), $n))
        );

        return [
            'raison'   => $find(['RAISON', 'SOCIALE']) ?: 4,
            'adresse'  => $find(['ADRESSE']) ?: 5,
            'cp'       => $find(['CP', 'POSTAL']) ?: 6,
            'ville'    => $find(['VILLE']) ?: 7,
            'salaries' => $find(['SALARI', 'NBRS']) ?: 8,
            'secteur'  => $find(['SECTEUR']) ?: 9,
            'tel1' => $find(['TEL 1', 'TELEPHONE 1', 'TEL1']) ?: 10,
            'tel2' => $find(['TEL 2', 'TELEPHONE 2', 'TEL2']) ?: 11,
            'ca'       => $find(['CA', 'CHIFFRE']) ?: 12,
            'siret'    => $find(['SIRET']) ?: 13,
        ];
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
        return ['prenom' => $parts[0] ?? null, 'nom' => $parts[1] ?? null];
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
