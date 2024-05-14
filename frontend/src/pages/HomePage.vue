<template>
	<main class="mt-5 mb-5">
		<div class="container">
			<div class="row justify-content-between home-page">
				<div class="col-md-8">
					<h5 class="mb-3">News Feed</h5>
					<div
						class="card mb-4"
						v-for="post in posts"
						v-if="posts.length"
					>
						<div
							class="card-header d-flex align-items-center justify-content-between bg-transparent py-3"
						>
							<h6 class="mb-0" v-if="post.user">
								{{
									post.user.username == loginUser.username
										? "You"
										: post.user.full_name
								}}
							</h6>
							<small class="text-muted">{{
								getFormattedTime(post.created_at)
							}}</small>
						</div>
						<div class="card-body">
							<div class="card-images mb-2">
								<img
									v-for="attachment in post.attachments"
									:src="attachment.full_path"
									alt="image"
									class="w-100"
								/>
							</div>
							<p class="mb-0 text-muted" v-if="post.user">
								<b
									><RouterLink
										:to="{
											name: 'profile',
											params: {
												username: post.user.username,
											},
										}"
										>{{ post.user.username }}</RouterLink
									></b
								>
								{{ post.caption }}
							</p>
						</div>
					</div>
				</div>
				<div class="col-md-4 main-sidebar">
					<div className="mb-4">
						<h6 class="mb-3">Find People</h6>
						<div class="input-group mb-3 search-container">
							<span class="input-group-text" id="basic-addon1"
								>@</span
							>
							<input
								type="text"
								class="form-control"
								placeholder="Username"
								v-model="search"
								@focus="() => (openSearchBox = true)"
								@blur="onSearchBlur"
							/>

							<div
								class="search-box"
								v-if="openSearchBox && searchedAllPeople.length"
							>
								<RouterLink
									v-for="people in searchedAllPeople"
									:to="{
										name: 'profile',
										params: { username: people.username },
									}"
									>@{{ people.username }}</RouterLink
								>
							</div>
						</div>
					</div>
					<div
						class="request-follow mb-4"
						v-if="followers.filter((f) => f.is_requested).length"
					>
						<h6 class="mb-3">Follow Requests</h6>
						<div class="request-follow-list">
							<template v-for="follower in followers">
								<div
									class="card mb-2"
									v-if="follower.is_requested"
								>
									<div
										class="card-body d-flex align-items-center justify-content-between p-2"
									>
										<RouterLink
											:to="{
												name: 'profile',
												params: {
													username: follower.username,
												},
											}"
											>@{{
												follower.username
											}}</RouterLink
										>
										<button
											class="btn btn-primary btn-sm"
											v-if="!follower.is_accepted"
											@click="
												acceptFollower(
													follower.username
												)
											"
										>
											Confirm
										</button>
										<span
											v-if="follower.is_accepted"
											className="text-muted"
											:disabled="true"
										>
											Accepted
										</span>
									</div>
								</div>
							</template>
						</div>
					</div>
					<div class="explore-people" v-if="people.length">
						<h6 class="mb-3">Explore People</h6>
						<div class="explore-people-list">
							<div class="card mb-2" v-for="user in people">
								<div class="card-body w-100 p-2">
									<RouterLink
										:to="{
											name: 'profile',
											params: { username: user.username },
										}"
										>@{{ user.username }}</RouterLink
									>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			<nav>
				<ul class="pagination">
					<li class="page-item" @click="getPosts(page - 1)">
						<button class="page-link" aria-label="Previous">
							<span aria-hidden="true">&laquo;</span>
						</button>
					</li>
					<li
						class="page-item"
						v-for="index in totalPage"
						@click="getPosts(index - 1)"
					>
						<button
							class="page-link"
							:class="{ active: index == page + 1 }"
						>
							{{ index }}
						</button>
					</li>
					<li class="page-item" @click="getPosts(page + 1)">
						<button class="page-link" aria-label="Next">
							<span aria-hidden="true">&raquo;</span>
						</button>
					</li>
				</ul>
			</nav>
		</div>
	</main>
</template>
<script lang="ts">
import { defineComponent, ref, computed } from "vue";
import Post from "../types/Post.ts";
import User from "../types/User.ts";
import { fetching, getFormattedTime } from "../utils";
import { useStore } from "vuex";
export default defineComponent({
	name: "Home page",
	setup() {
		const store = useStore();
		const posts = ref<Post[]>([]);
		const page = ref(0);
		const totalPage = ref(1);
		const people = ref<User[]>([]);
		const allPeople = ref<User[]>([]);
		const followers = ref<
			(User & {
				is_accepted?: boolean;
				is_requested?: boolean;
			})[]
		>([]);
		const search = ref("");
		const openSearchBox = ref(false);
		const loginUser = computed<User>(() => store.state.user);

		const searchedAllPeople = computed<User[]>(() => {
			return allPeople.value.filter((user) => {
				return user.username.includes(search.value);
			});
		});

		const onSearchBlur = () => {
			setTimeout(() => (openSearchBox.value = false), 260);
		};

		const getPosts = async (pageAt: number) => {
			if (pageAt == -1 || pageAt >= totalPage.value) return;
			const response = await fetching("get", "posts", {
				data: {
					page: pageAt,
				},
			});

			if (response.status == 200) {
				posts.value = response.data.posts;
				page.value = Number(response.data.page);
				totalPage.value = response.data.total_page;
			}
		};

		const getPeople = async () => {
			const response = await fetching("get", "users");

			if (response.status == 200) {
				people.value = response.data.users;
			}
		};

		const getAllPeople = async () => {
			const response = await fetching("get", "all_users");

			if (response.status == 200) {
				allPeople.value = response.data.users;
			}
		};

		const getFollowers = async () => {
			const response = await fetching(
				"get",
				`users/${loginUser.value.username}/followers`
			);
			if (response.status == 200) {
				followers.value = response.data.followers;
			}
		};

		const acceptFollower = async (username: string) => {
			const response = await fetching("put", `users/${username}/accept`);
			if (response.status == 200) {
				followers.value = followers.value.map((follower) => {
					if (follower.username === username) {
						return {
							...follower,
							is_accepted: true,
						};
					}
					return follower;
				});
			}
		};

		getPosts(page.value);
		getPeople();
		getFollowers();
		getAllPeople();

		return {
			loginUser,
			posts,
			totalPage,
			page,
			openSearchBox,
			getPosts,
			getFormattedTime,
			people,
			followers,
			search,
			acceptFollower,
			searchedAllPeople,
			onSearchBlur,
		};
	},
});
</script>
