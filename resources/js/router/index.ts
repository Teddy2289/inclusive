import { createRouter, createWebHistory } from "vue-router";
import { useAuthStore } from "@/stores/auth";

const routes = [
    // ─── Public ───────────────────────────────
    {
        path: "/login",
        name: "login",
        component: () => import("@/pages/auth/LoginPage.vue"),
        meta: { guest: true },
    },
    {
        path: "/register",
        name: "register",
        component: () => import("@/pages/auth/RegisterPage.vue"),
        meta: { guest: true },
    },

    // ─── Backoffice (auth requis) ─────────────
    {
        path: "/",
        component: () => import("@/layouts/AppLayout.vue"),
        meta: { requiresAuth: true },
        children: [
            {
                path: "",
                redirect: "/dashboard",
            },
            {
                path: "dashboard",
                name: "dashboard",
                component: () => import("@/pages/DashboardPage.vue"),
            },
            {
                path: "partenaires",
                name: "partenaires.index",
                component: () =>
                    import("@/pages/partenaires/PartenairesPage.vue"),
            },
            {
                path: "partenaires/:id",
                name: "partenaires.show",
                component: () =>
                    import("@/pages/partenaires/PartenaireDetailPage.vue"),
            },
            {
                path: "contacts",
                name: "contacts.index",
                component: () => import("@/pages/contacts/ContactsPage.vue"),
            },
        ],
    },

    { path: "/:pathMatch(.*)*", redirect: "/dashboard" },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

// Guard global
router.beforeEach(async (to) => {
    const auth = useAuthStore();

    if (to.meta.requiresAuth && !auth.isAuthenticated) {
        return { name: "login" };
    }

    if (to.meta.guest && auth.isAuthenticated) {
        return { name: "dashboard" };
    }
});

export default router;
