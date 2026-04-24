import api from "./api";
import type { User, Role, Permission, Paginated } from "@/types";

interface UserFormData {
    name: string;
    email: string;
    password?: string;
    password_confirmation?: string;
    roles: string[];
    permissions: string[];
}

export const userService = {
    // Liste des utilisateurs
    async getUsers(params?: {
        page?: number;
        search?: string;
    }): Promise<Paginated<User>> {
        const { data } = await api.get("/users", { params });
        return data;
    },

    // Obtenir un utilisateur
    async getUser(id: number): Promise<User> {
        const { data } = await api.get(`/users/${id}`);
        return data;
    },

    // Créer un utilisateur
    async createUser(
        userData: UserFormData,
    ): Promise<{ message: string; user: User }> {
        const { data } = await api.post("/users", userData);
        return data;
    },

    // Mettre à jour un utilisateur
    async updateUser(
        id: number,
        userData: Partial<UserFormData>,
    ): Promise<{ message: string; user: User }> {
        const { data } = await api.put(`/users/${id}`, userData);
        return data;
    },

    // Supprimer un utilisateur
    async deleteUser(id: number): Promise<{ message: string }> {
        const { data } = await api.delete(`/users/${id}`);
        return data;
    },

    // Obtenir tous les rôles
    async getRoles(): Promise<Role[]> {
        const { data } = await api.get("/roles");
        return data;
    },

    // Obtenir toutes les permissions
    async getPermissions(): Promise<Permission[]> {
        const { data } = await api.get("/permissions");
        return data;
    },
};
