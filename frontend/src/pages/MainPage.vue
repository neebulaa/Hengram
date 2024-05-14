<template>
	<nav class="navbar navbar-expand bg-body-tertiary">
		<div class="container">
			<RouterLink class="navbar-brand" :to="{ name: 'home' }"
				>Hengram</RouterLink
			>
			<div class="navbar-nav" v-if="user.username">
				<RouterLink
					class="nav-link"
					:to="{
						name: 'profile',
						params: { username: user.username },
					}"
					>@{{ user.username }}</RouterLink
				>
				<button class="nav-link" @click="logout">Logout</button>
			</div>
		</div>
	</nav>
	<router-view :key="$route.fullPath"></router-view>
</template>
<script lang="ts">
import { defineComponent, ref } from "vue";
import { APP_AUTHTOKEN } from "../config";
import { fetching } from "../utils";
import router from "../router";
import User from "../types/User";
import { useStore } from "vuex";

export default defineComponent({
	name: "Main page - after auth",
	setup() {
		const store = useStore();
		const user = ref<User>({} as User);

		const getUser = async () => {
			const response = await fetching("get", "user");
			console.log(response);
			if (response.status == 200) {
				user.value = response.data;
			}
		};

		const logout = async () => {
			const response = await fetching("post", "auth/logout");
			if (response.status == 200) {
				localStorage.removeItem(APP_AUTHTOKEN);
				store.commit("setUser", {});
				router.push({ name: "login" });
			}
		};

		getUser();

		return {
			logout,
			user,
		};
	},
});
</script>
