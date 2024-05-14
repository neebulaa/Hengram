<template>
	<main>
		<div class="container py-5">
			<div class="row justify-content-center">
				<div class="col-md-5">
					<div class="card">
						<div
							class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
						>
							<h5 class="mb-0">Create new post</h5>
						</div>
						<div class="card-body">
							<form @submit.prevent="submit">
								<div class="mb-2">
									<label for="caption">Caption</label>
									<textarea
										class="form-control"
										name="caption"
										id="caption"
										cols="30"
										rows="3"
										v-model="data.caption"
										>{{ data.caption }}</textarea
									>
									<div class="text-danger">
										<p
											v-if="errors.caption"
											v-for="error in errors.caption"
										>
											{{ error }}
										</p>
									</div>
								</div>

								<div class="mb-3">
									<div class="card-images mb-2">
										<img
											v-for="url in attachmentsUrl"
											:src="url"
											alt="image"
											class="w-100"
										/>
									</div>

									<label for="attachments">Image(s)</label>
									<input
										type="file"
										class="form-control"
										id="attachments"
										name="attachments"
										multiple
										accept="image/*"
										@change="handleUploadImage"
									/>
									<div class="text-danger">
										<p
											v-if="errors.attachments"
											v-for="error in errors.attachments"
										>
											{{ error }}
										</p>
									</div>
									<div
										class="text-danger"
										v-for="(_, i) in attachments"
									>
										<p
											v-if="errors[`attachments.${i}`]"
											v-for="error in errors[
												`attachments.${i}`
											]"
										>
											{{ error }}
										</p>
									</div>
								</div>

								<button
									type="submit"
									class="btn btn-primary w-100"
								>
									Share
								</button>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</main>
</template>
<script lang="ts">
import { defineComponent, ref } from "vue";
import { fetching } from "../utils";
import router from "../router";
export default defineComponent({
	name: "Create new post page",
	setup() {
		const data = ref({
			caption: "",
		});

		const errors = ref<{ [key: string]: string[] }>({});

		const attachments = ref<File[]>([]);
		const attachmentsUrl = ref<string[]>([]);

		const submit = async () => {
			errors.value = {};
			let formData = new FormData();
			formData.append("caption", data.value.caption);
			attachments.value.forEach((attachment) => {
				formData.append("attachments[]", attachment);
			});

			const response = await fetching("post", "posts", {
				type: "multipart/form-data",
				data: formData,
			});

			if (response.status == 422) {
				errors.value = response.data.errors;
			} else if (response.status == 200) {
				router.push({ name: "home" });
			}
		};

		const handleUploadImage = (event: Event) => {
			const target = event.target as HTMLInputElement;
			if (target.files!.length == 0) return;
			for (const file of target.files!) {
				attachments.value.push(file);
				const fileReader = new FileReader();
				fileReader.readAsDataURL(file);
				fileReader.addEventListener("load", function () {
					attachmentsUrl.value.push(fileReader.result as string);
				});
			}
		};

		return {
			data,
			attachmentsUrl,
			handleUploadImage,
			submit,
			errors,
			attachments,
		};
	},
});
</script>
