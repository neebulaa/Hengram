import { createStore } from "vuex";
import User from "../types/User";
import { fetching } from "../utils";

type State = {
	user: User;
};

const store = createStore<State>({
	state: {
		user: {},
	},
	mutations: {
		setUser(state: State, payload: User) {
			state.user = payload;
		},
	},
});

const login = async () => {
	const response = await fetching("get", "user");
	if (response.status == 200) {
		store.commit("setUser", response.data);
	}
};

login();

export default store;
