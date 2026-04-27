// ─── services/roleService.ts ──────────────────────────────────────────────────

import api from "./api";
import type { Role, Permission, Paginated } from "@/types";

interface RoleFormData {
    name: string;
    permissions: string[];
}

export const roleService = {
    async getRoles(params?: {
        page?: number;
        search?: string;
    }): Promise<Paginated<Role>> {
        const { data } = await api.get("/roles", { params });
        return data;
    },

    async getRole(id: number): Promise<Role> {
        const { data } = await api.get(`/roles/${id}`);
        return data;
    },

    async createRole(
        roleData: RoleFormData,
    ): Promise<{ message: string; role: Role }> {
        const { data } = await api.post("/roles", roleData);
        return data;
    },

    async updateRole(
        id: number,
        roleData: Partial<RoleFormData>,
    ): Promise<{ message: string; role: Role }> {
        const { data } = await api.put(`/roles/${id}`, roleData);
        return data;
    },

    async deleteRole(id: number): Promise<{ message: string }> {
        const { data } = await api.delete(`/roles/${id}`);
        return data;
    },
};


// ─── services/permissionService.ts ───────────────────────────────────────────

interface PermissionFormData {
    name: string;
}

export const permissionService = {
    async getPermissions(params?: {
        page?: number;
        search?: string;
    }): Promise<Paginated<Permission>> {
        const { data } = await api.get("/permissions", { params });
        return data;
    },

    async getPermission(id: number): Promise<Permission> {
        const { data } = await api.get(`/permissions/${id}`);
        return data;
    },

    async createPermission(
        permData: PermissionFormData,
    ): Promise<{ message: string; permission: Permission }> {
        const { data } = await api.post("/permissions", permData);
        return data;
    },

    async updatePermission(
        id: number,
        permData: PermissionFormData,
    ): Promise<{ message: string; permission: Permission }> {
        const { data } = await api.put(`/permissions/${id}`, permData);
        return data;
    },

    async deletePermission(id: number): Promise<{ message: string }> {
        const { data } = await api.delete(`/permissions/${id}`);
        return data;
    },
};
