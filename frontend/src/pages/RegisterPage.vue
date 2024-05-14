<template>
	<main class="mt-5">
		<div class="container py-5">
			<div class="row justify-content-center">
				<div class="col-md-5">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
						>
							<h5 class="mb-0">Register</h5>
						</div>
						<div class="card-body">
							<form @submit.prevent="submit">
								<div>
									<label for="full_name">Full Name</label>
									<input
										type="text"
										class="form-control"
										id="full_name"
										name="full_name"
										v-model="formData.full_name"
									/>
									<div class="text-danger">
										<p
											v-if="errors.full_name"
											v-for="error in errors.full_name"
										>
											{{ error }}
										</p>
									</div>
								</div>

								<div class="mt-2">
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

								<div class="mt-2">
									<label for="bio">Bio</label>
									<textarea
										name="bio"
										id="bio"
										cols="30"
										rows="3"
										class="form-control"
										v-model="formData.bio"
										>{{ formData.bio }}</textarea
									>
									<div class="text-danger">
										<p
											v-if="errors.bio"
											v-for="error in errors.bio"
										>
											{{ error }}
										</p>
									</div>
								</div>

								<div
									class="mt-2 d-flex align-items-center gap-2"
								>
									<input
										type="checkbox"
										id="is_private"
										name="is_private"
										v-model="formData.isPrivate"
									/>
									<label for="is_private"
										>Private Account</label
									>
								</div>

								<button
									type="submit"
									class="btn btn-primary w-100 mt-4"
								>
									Register
								</button>
							</form>
						</div>
					</div>

					<div class="text-center mt-4">
						Already have an account?
						<RouterLink :to="{ name: 'login' }">Login</RouterLink>
					</div>
				</div>
			</div>
		</div>
	</main>
</template>
<script lang="ts">
import { fetching } from "../utils.ts";
import { APP_AUTHTOKEN } from "../config.ts";
import router from "../router";
import { defineComponent, ref } from "vue";
export default defineComponent({
	name: "Register page",
	setup() {
		const formData = ref({
			full_name: "",
			username: "",
			password: "",
			bio: "",
			isPrivate: false,
		});

		const errors = ref({
			full_name: "",
			username: "",
			password: "",
			bio: "",
			isPrivate: false,
			message: "",
		});

		const submit = async () => {
			errors.value = {
				full_name: "",
				username: "",
				password: "",
				bio: "",
				isPrivate: false,
				message: "",
			};
			const response = await fetching("post", "auth/register", {
				data: formData.value,
			});

			if (response.status == 422) {
				errors.value = response.data.errors;
				errors.value.message = response.data.message;
			} else if (response.status == 401) {
				errors.value.message = response.data.message;
			} else if (response.status == 200) {
				localStorage.setItem(APP_AUTHTOKEN, response.data.token);
				router.push({ name: "home" });
			}
		};

		return { formData, submit, errors };
	},
});
</script>
