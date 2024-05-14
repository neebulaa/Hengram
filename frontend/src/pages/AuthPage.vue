<template>
	<nav class="navbar navbar-expand bg-body-tertiary">
		<div class="container">
			<RouterLink class="navbar-brand m-auto" :to="{ name: 'home' }">{{
				APP_NAME
			}}</RouterLink>
		</div>
	</nav>
	<RouterView></RouterView>
</template>
<script lang="ts">
import { APP_NAME } from "../config.ts";
import { defineComponent } from "vue";

export default defineComponent({
	name: "Auth page - credentials page",
	setup() {
		return { APP_NAME };
	},
});
</script>
