export interface User {
    id: number;
    name: string;
    email: string;
    roles: Role[];
    permissions: Permission[];
    created_at: string | null;
}

export interface Statut {
    value: string;
    label: string;
    color: string;
}

export interface Partenaire {
    id: number;
    raison_sociale: string;
    adresse: string | null;
    cp: string | null;
    ville: string | null;
    nbrs_salaries: number | null;
    secteur_activite: string | null;
    telephone_1: string | null;
    telephone_2: string | null;
    ca: number | null;
    siret: string | null;
    statut: Statut;
    contacts?: Contact[];
}

export interface Contact {
    id: number;
    partenaire_id: number;
    conseiller_nom: string | null;
    conseiller_prenom: string | null;
    conseiller: string | null;
    statut: Statut;
    date_premier_contact: string | null;
    commentaires: string | null;
    poste: string | null;
    tel: string | null;
    fonction: string | null;
    date_rdv: string | null;
    heure_rdv: string | null;
    partenaires?: Partenaire[];
}

export interface PaginationLink {
    url: string | null;
    label: string | null;
    active: boolean;
}

export interface Paginated<T> {
    data: T[];
    links: {
        first: string | null;
        last: string | null;
        prev: string | null;
        next: string | null;
    };
    meta: {
        current_page: number;
        last_page: number;
        per_page: number;
        total: number;
        links: PaginationLink[];
    };
}

export interface ApiError {
    message: string;
    errors?: Record<string, string[]>;
}

export interface EnumOption {
    value: string;
    label: string;
}

export interface PartenaireForm {
    raison_sociale: string;
    adresse: string | null;
    cp: string | null;
    ville: string | null;
    secteur_activite: string | null;
    telephone_1: string | null;
    telephone_2: string | null;
    nbrs_salaries: number | null;
    ca: number | null;
    siret: string | null;
    statut: string;
}

export interface ContactForm {
    conseiller_nom: string | null;
    conseiller_prenom: string | null;
    fonction: string | null;
    tel: string | null;
    poste: string | null;
    date_rdv: string | null;
    heure_rdv: string | null;
    date_premier_contact: string | null;
    commentaires: string | null;
    statut: string;
     partenaire_ids: number[]
}

export interface Role {
    id: number;
    name: string;
    guard_name: string;
    permissions?: Permission[];
    created_at?: string;
    updated_at?: string;
}

export interface Permission {
    id: number;
    name: string;
    guard_name: string;
    created_at?: string;
    updated_at?: string;
}
