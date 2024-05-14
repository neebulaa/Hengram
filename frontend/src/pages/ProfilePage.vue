<template>
	<main>
		<div class="container py-5">
			<div
				class="px-5 py-4 bg-light mb-4 d-flex align-items-center justify-content-between"
			>
				<div>
					<div class="d-flex align-items-center gap-2 mb-1">
						<h5 class="mb-0">{{ user.full_name }}</h5>
						<span>@{{ user.username }}</span>
					</div>
					<small class="mb-0 text-muted">
						{{ user.bio }}
					</small>
				</div>
				<div>
					<button
						v-if="
							!user.is_your_account &&
							user.following_status == 'not-following'
						"
						class="btn btn-primary w-100 mb-2"
						@click="follow"
					>
						Follow
					</button>
					<button
						v-if="
							!user.is_your_account &&
							(user.following_status == 'requested' ||
								user.following_status == 'following')
						"
						class="btn btn-secondary w-100 mb-2"
						@click="unfollow"
					>
						{{
							user.following_status == "requested"
								? "Requested"
								: "Following"
						}}
					</button>
					<RouterLink
						v-if="user.is_your_account"
						class="btn btn-primary w-100 mb-2"
						:to="{ name: 'create-new-post' }"
					>
						+ Create new post
					</RouterLink>
					<div class="d-flex gap-3">
						<div>
							<div class="profile-label">
								<b>{{ user.posts_count }}</b> posts
							</div>
						</div>
						<div class="profile-dropdown">
							<div class="profile-label">
								<b>{{ user.followers_count }}</b> followers
							</div>
						</div>
						<div class="profile-dropdown">
							<div class="profile-label">
								<b>{{ user.following_count }}</b> following
							</div>
						</div>
					</div>
				</div>
			</div>
			<div
				class="card py-4"
				v-if="
					!user.is_your_account &&
					user.is_private &&
					user.following_status != 'following'
				"
			>
				<div class="card-body text-center">
					&#128274; This account is private
				</div>
			</div>

			<div
				class="row"
				v-if="
					user.is_your_account ||
					!user.is_private ||
					(user.is_private && user.following_status == 'following')
				"
			>
				<div class="col-md-4" v-for="post in user.posts">
					<div class="card mb-4">
						<div
							class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
						>
							<small class="text-muted"
								>Posted
								{{ getFormattedTime(post.created_at) }}</small
							>
						</div>
						<div class="card-body">
							<div class="card-images mb-2">
								<img
									alt="image"
									class="w-100"
									:src="attachment.full_path"
									v-for="attachment in post.attachments"
								/>
							</div>
							<p class="mb-0">
								{{ post.caption }}
							</p>
						</div>
					</div>
				</div>
			</div>

			<div
				class="card py-4"
				v-if="
					user.is_your_account && user.posts && user.posts.length == 0
				"
			>
				<div class="card-body text-center">
					🙅‍♂️ Currently no posts. Share a post now!
				</div>
			</div>
			<div
				class="card py-4"
				v-if="
					!user.is_your_account &&
					user.posts &&
					user.posts.length == 0
				"
			>
				<div class="card-body text-center">
					🙅‍♂️ This user has not posted anything
				</div>
			</div>
		</div>
	</main>
</template>
<script lang="ts">
import { defineComponent, ref } from "vue";
import { fetching, getFormattedTime } from "../utils.ts";
import User from "../types/User.ts";
import Post from "../types/Post.ts";
export default defineComponent({
	name: "Profile page",
	props: {
		username: String,
	},
	setup(props) {
		const user = ref<
			User & {
				is_your_account?: boolean;
				posts_count?: number;
				followers_count?: number;
				following_count?: number;
				posts?: Post[];
				following_status?: string;
			}
		>({} as User);

		const getUser = async () => {
			const response = await fetching("get", `users/${props.username}`);
			if (response.status == 200) {
				user.value = response.data.user;
			}
		};

		const follow = async () => {
			const response = await fetching(
				"post",
				`users/${props.username}/follow`
			);
			if (response.status == 200) {
				user.value.following_status = response.data.status;
				if (user.value.following_status != "requested") {
					(user.value.followers_count as number) += 1;
				}
			}
		};

		const unfollow = async () => {
			const response = await fetching(
				"delete",
				`users/${props.username}/unfollow`
			);
			if (response.status == 200) {
				user.value.following_status = "not-following";
				(user.value.followers_count as number) -= 1;
			}
		};

		getUser();

		return {
			follow,
			user,
			unfollow,
			getFormattedTime,
		};
	},
});
</script>
