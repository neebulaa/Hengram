import { createRouter, createWebHashHistory } from "vue-router";
import HomePage from "../pages/HomePage.vue";
import MainPage from "../pages/MainPage.vue";
import AuthPage from "../pages/AuthPage.vue";
import LoginPage from "../pages/LoginPage.vue";
import RegisterPage from "../pages/RegisterPage.vue";
import ProfilePage from "../pages/ProfilePage.vue";
import CreateNewPostPage from "../pages/CreateNewPostPage.vue";
import { fetching } from "../utils";

const routes = [
	{
		path: "/",
		name: "main",
		component: MainPage,
		meta: {
			auth: true,
		},
		children: [
			{
				path: "/",
				name: "home",
				component: HomePage,
			},
			{
				path: "/profile/:username",
				name: "profile",
				props: true,
				component: ProfilePage,
			},
			{
				path: "/create-new-post",
				name: "create-new-post",
				component: CreateNewPostPage,
			},
		],
	},
	{
		path: "/",
		name: "auth",
		component: AuthPage,
		meta: {
			guest: true,
		},
		children: [
			{
				path: "/login",
				name: "login",
				component: LoginPage,
			},
			{
				path: "/register",
				name: "register",
				component: RegisterPage,
			},
		],
	},
];

const router = createRouter({
	history: createWebHashHistory(),
	routes,
});

router.beforeEach(async (to, from, next) => {
	if (to.meta.auth) {
		const { status } = await fetching("get", "users");
		if (status == 401) {
			next({ name: "login" });
		}
	}

	if (to.meta.guest) {
		const { status } = await fetching("get", "users");
		if (status != 401) {
			next({ name: "home" });
		}
	}
	next();
});

export default router;
