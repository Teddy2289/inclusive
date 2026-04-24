import { useAuthStore } from "@/stores/auth";

export function usePermissions() {
    const authStore = useAuthStore();

    const hasPermission = (permission: string): boolean => {
        const user = authStore.user;
        if (!user) return false;

        // Admin a toutes les permissions
        if (user.roles.some((role) => role.name === "admin")) {
            return true;
        }

        // Vérifier les permissions directes
        if (user.permissions.some((p) => p.name === permission)) {
            return true;
        }

        // Vérifier les permissions des rôles
        return user.roles.some((role) =>
            role.permissions?.some((p) => p.name === permission),
        );
    };

    const hasAnyPermission = (permissions: string[]): boolean => {
        return permissions.some((permission) => hasPermission(permission));
    };

    const hasAllPermissions = (permissions: string[]): boolean => {
        return permissions.every((permission) => hasPermission(permission));
    };

    return {
        hasPermission,
        hasAnyPermission,
        hasAllPermissions,
    };
}
