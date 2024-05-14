<template>
	<main class="mt-5">
		<div class="container py-5">
			<div class="row justify-content-center">
				<div class="col-md-5">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
						>
							<h5 class="mb-0">Login</h5>
						</div>
						<div class="card-body">
							<div
								class="alert alert-danger"
								v-if="errors.message"
							>
								{{ errors.message }}
							</div>
							<form @submit.prevent="submit">
								<div>
									<label for="username">Username</label>
									<input
										type="text"
										class="form-control"
										id="username"
										name="username"
										v-model="formData.username"
									/>
									<div class="text-danger">
										<p
											v-if="errors.username"
											v-for="error in errors.username"
										>
											{{ error }}
										</p>
									</div>
								</div>

								<div class="mt-2">
									<label for="password">Password</label>
									<input
										type="password"
										class="form-control"
										id="password"
										name="password"
										v-model="formData.password"
									/>
									<div class="text-danger">
										<p
											v-if="errors.password"
											v-for="error in errors.password"
										>
											{{ error }}
										</p>
									</div>
								</div>

								<button
									type="submit"
									class="btn btn-primary w-100 mt-4"
								>
									Login
								</button>
							</form>
						</div>
					</div>

					<div class="text-center mt-4">
						Don't have account?
						<RouterLink :to="{ name: 'register' }"
							>Register</RouterLink
						>
					</div>
				</div>
			</div>
		</div>
	</main>
</template>
<script lang="ts">
import { APP_AUTHTOKEN } from "../config.ts";
import { defineComponent, ref } from "vue";
import { fetching } from "../utils.ts";
import router from "../router";
import { useStore } from "vuex";
export default defineComponent({
	name: "Login page",
	setup() {
		const formData = ref({
			username: "",
			password: "",
		});

		const errors = ref({
			username: [],
			password: [],
			message: "",
		});

		const store = useStore();

		const submit = async () => {
			errors.value = {
				username: [],
				password: [],
				message: "",
			};
			const response = await fetching("post", "auth/login", {
				data: formData.value,
			});

			if (response.status == 422) {
				errors.value = response.data.errors;
				errors.value.message = response.data.message;
			} else if (response.status == 401) {
				errors.value.message = response.data.message;
			} else if (response.status == 200) {
				localStorage.setItem(APP_AUTHTOKEN, response.data.token);
				store.commit("setUser", response.data.user);
				router.push({ name: "home" });
			}
		};

		return { formData, submit, errors };
	},
});
</script>
