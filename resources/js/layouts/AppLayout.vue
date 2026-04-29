<template>
    <div class="layout">
        <!-- Sidebar -->
        <aside class="sidebar" :class="{ collapsed: isCollapsed }">
            <!-- Bouton toggle pour mobile -->
            <button class="sidebar-toggle" @click="toggleSidebar">
                <svg class="toggle-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="isCollapsed" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 5l7 7-7 7M5 5l7 7-7 7" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 19l-7-7 7-7M19 19l-7-7 7-7" />
                </svg>
            </button>

            <!-- Logo -->
            <div class="sidebar__logo">
               <img :src="logo" alt="Logo" class="logo-icon" />
            </div>

            <!-- Navigation -->
            <nav class="sidebar__nav">
                <RouterLink to="/dashboard" class="nav-item" active-class="active">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                    <span class="nav-text">Dashboard</span>
                </RouterLink>

                <RouterLink to="/partenaires" class="nav-item" active-class="active">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="nav-text">Partenaires</span>
                </RouterLink>

                <!-- <RouterLink to="/contacts" class="nav-item" active-class="active">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <span class="nav-text">Contacts</span>
                </RouterLink> -->

                <RouterLink to="/users" class="nav-item" active-class="active">
                    <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span class="nav-text">Utilisateurs</span>
                </RouterLink>
                <RouterLink to="/roles" class="nav-item" active-class="active">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="nav-text">Rôles</span>
                </RouterLink>
                <RouterLink to="/permissions" class="nav-item" active-class="active">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="nav-text">Permissions</span>
                </RouterLink>
                       <RouterLink to="crm/comptes" class="nav-item" active-class="active">
                    <svg class="nav-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span class="nav-text">Listes des comptes</span>
                </RouterLink>
                <!-- Séparateur -->
                <div class="nav-divider" v-if="!isCollapsed"></div>

                <!-- Statistiques -->
                <div class="nav-stats" v-if="!isCollapsed">
                    <div class="stat-item">
                        <span class="stat-value">{{
                            stats.totalPartenaires
                        }}</span>
                        <span class="stat-label">Partenaires</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-value">{{
                            stats.totalContacts
                        }}</span>
                        <span class="stat-label">Contacts</span>
                    </div>
                </div>

            </nav>

            <!-- Footer avec profil utilisateur -->
            <div class="sidebar__footer">
                <div class="user-profile">
                    <div class="user-avatar">
                        <span class="avatar-initials">{{
                            getUserInitials(auth.user?.name)
                        }}</span>
                        <div class="user-status online"></div>
                    </div>
                    <div class="user-info" v-if="!isCollapsed">
                        <span class="user-name">{{
                            auth.user?.name || "Utilisateur"
                        }}</span>
                        <span class="user-role">Administrateur</span>
                    </div>
                </div>

                <div class="footer-actions">
                    <button class="action-btn logout" @click="handleLogout" title="Déconnexion">
                        <svg class="action-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </div>
            </div>
        </aside>

        <!-- Overlay pour mobile -->
        <div v-if="isMobileMenuOpen" class="sidebar-overlay" @click="closeMobileMenu"></div>

        <!-- Contenu principal -->
        <main class="main-content" :class="{ collapsed: isCollapsed }">
            <RouterView />
        </main>
    </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import { partenaireService } from "@/services/partenaireService";
import { contactService } from "@/services/contactService";
import logo from '@/assets/logo_2.png'


const auth = useAuthStore();
const router = useRouter();
const isCollapsed = ref(false);
const isMobileMenuOpen = ref(false);

const stats = reactive({
    totalPartenaires: 0,
    totalContacts: 0,
});

const getUserInitials = (name?: string) => {
    if (!name) return "U";
    return name
        .split(" ")
        .slice(0, 2)
        .map((n) => n[0])
        .join("")
        .toUpperCase();
};

const toggleSidebar = () => {
    if (window.innerWidth <= 768) {
        isMobileMenuOpen.value = !isMobileMenuOpen.value;
    } else {
        isCollapsed.value = !isCollapsed.value;
    }
};

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false;
};

const openSettings = () => {
    // Navigation vers les paramètres
    console.log("Open settings");
};

const fetchStats = async () => {
    try {
        const [p, c] = await Promise.all([
            partenaireService.list({ per_page: 1 }),
            contactService.list({ per_page: 1 }),
        ]);
        stats.totalPartenaires = p.meta.total;
        stats.totalContacts = c.meta.total;
    } catch (error) {
        console.error("Erreur chargement stats:", error);
    }
};

async function handleLogout(): Promise<void> {
    await auth.logout();
    router.push({ name: "login" });
}

// Gestion du responsive
const handleResize = () => {
    if (window.innerWidth > 768) {
        isMobileMenuOpen.value = false;
    }
};

onMounted(() => {
    fetchStats();
    window.addEventListener("resize", handleResize);
});

onUnmounted(() => {
    window.removeEventListener("resize", handleResize);
});
</script>

<style scoped>
/* Layout de base */
.layout {
    display: flex;
    min-height: 100vh;
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    position: relative;
}

/* Sidebar */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, #1f1d22 0%, #1f1d22 100%);
    backdrop-filter: blur(10px);
    color: #fff;
    display: flex;
    flex-direction: column;
    position: fixed;
    top: 0;
    left: 0;
    height: 100vh;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 1000;
    box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
}

.sidebar.collapsed {
    width: 80px;
}

/* Bouton toggle */
.sidebar-toggle {
    position: absolute;
    top: 1rem;
    right: -0.75rem;
    width: 1.5rem;
    height: 1.5rem;
    background: #ff6b35;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    z-index: 10;
}

.sidebar-toggle:hover {
    transform: scale(1.1);
    background: #0284c7;
}

.toggle-icon {
    width: 1rem;
    height: 1rem;
    color: white;
}

/* Logo */
.sidebar__logo {
    padding: 1.5rem 1rem 1rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    border-bottom: 1px solid rgba(51, 65, 85, 0.5);
}

.collapsed .sidebar__logo {
    justify-content: center;
}

.logo-icon {
    width: 100%;
    object-fit: cover;
    height: 60px;
    background: white;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.logo-svg {
    width: 1.5rem;
    height: 1.5rem;
    color: white;
}

.logo-text {
    display: flex;
    flex-direction: column;
}

.collapsed .logo-text {
    display: none;
}

.logo-title {
    font-size: 1.125rem;
    font-weight: 700;
    background: linear-gradient(135deg, #fff 0%, #94a3b8 100%);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
}

.logo-subtitle {
    font-size: 0.75rem;
    color: #94a3b8;
    letter-spacing: 0.05em;
}

/* Navigation */
.sidebar__nav {
    flex: 1;
    padding: 1.5rem 0.75rem;
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    color: #94a3b8;
    text-decoration: none;
    border-radius: 0.75rem;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.nav-item::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #ff6b35 0%, #ff6b35b2 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
    z-index: -1;
}

.nav-item:hover::before,
.nav-item.active::before {
    opacity: 1;
}

.nav-item:hover,
.nav-item.active {
    color: white;
    transform: translateX(4px);
}

.nav-icon {
    width: 1.25rem;
    height: 1.25rem;
    flex-shrink: 0;
}

.nav-text {
    flex: 1;
    font-size: 0.875rem;
    font-weight: 500;
}

.collapsed .nav-text {
    display: none;
}

.nav-badge {
    background: #ef4444;
    color: white;
    font-size: 0.625rem;
    padding: 0.125rem 0.375rem;
    border-radius: 0.875rem;
    font-weight: 600;
}

/* Séparateur */
.nav-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, #334155, transparent);
    margin: 1rem 0;
}

/* Statistiques */
.nav-stats {
    background: rgba(51, 65, 85, 0.3);
    border-radius: 0.75rem;
    padding: 1rem;
    margin-top: 1rem;
    backdrop-filter: blur(10px);
}

.stat-item {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    margin-bottom: 0.5rem;
}

.stat-item:last-child {
    margin-bottom: 0;
}

.stat-value {
    font-size: 1rem;
    font-weight: 700;
    color: #ff6b35;
}

.stat-label {
    font-size: 0.75rem;
    color: #94a3b8;
}

/* Footer */
.sidebar__footer {
    padding: 1.5rem 0.75rem;
    border-top: 1px solid rgba(51, 65, 85, 0.5);
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.collapsed .user-profile {
    justify-content: center;
}

.user-avatar {
    position: relative;
    width: 2.5rem;
    height: 2.5rem;
    background: linear-gradient(135deg, #ff6b35 0%, #ff6b35 100%);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.avatar-initials {
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
}

.user-status {
    position: absolute;
    bottom: 0;
    right: 0;
    width: 0.625rem;
    height: 0.625rem;
    background: #10b981;
    border: 2px solid #1e293b;
    border-radius: 50%;
}

.user-status.online {
    background: #10b981;
}

.user-info {
    flex: 1;
    overflow: hidden;
}

.collapsed .user-info {
    display: none;
}

.user-name {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: white;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role {
    font-size: 0.75rem;
    color: #94a3b8;
}

.footer-actions {
    display: flex;
    gap: 0.5rem;
}

.collapsed .footer-actions {
    justify-content: center;
}

.action-btn {
    flex: 1;
    background: rgba(51, 65, 85, 0.5);
    border: none;
    padding: 0.5rem;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
}

.action-btn:hover {
    transform: translateY(-2px);
}

.action-btn.settings:hover {
    background: #ff6b35;
}

.action-btn.logout:hover {
    background: #ef4444;
}

.action-icon {
    width: 1rem;
    height: 1rem;
    color: white;
}

/* Overlay mobile */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    backdrop-filter: blur(4px);
}

/* Contenu principal */
.main-content {
    margin-left: 280px;
    flex: 1;
    padding: 2rem;
    background: whitesmoke;
    transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.main-content.collapsed {
    margin-left: 80px;
}

/* Scrollbar personnalisée */
.sidebar::-webkit-scrollbar {
    width: 4px;
}

.sidebar::-webkit-scrollbar-track {
    background: #334155;
}

.sidebar::-webkit-scrollbar-thumb {
    background: #ff6b35;
    border-radius: 4px;
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar {
        transform: translateX(-100%);
    }

    .sidebar:not(.collapsed) {
        transform: translateX(0);
        width: 280px;
    }

    .main-content {
        margin-left: 0 !important;
        padding: 1rem;
    }

    .sidebar-toggle {
        right: -2rem;
    }
}
</style>
