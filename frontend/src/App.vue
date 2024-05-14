<template>
	<RouterView :key="$route.fullPath"></RouterView>
</template>
<script lang="ts">
import { defineComponent } from "vue";

export default defineComponent({
	name: "Hengram - Frontend",
});
</script>
