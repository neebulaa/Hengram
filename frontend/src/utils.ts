import axios, { AxiosRequestConfig } from "axios";
import { API_URL, APP_AUTHTOKEN } from "./config.ts";
import moment from "moment";

const fetching = async (
	method: string,
	path: string,
	options: AxiosRequestConfig & {
		type?: string | null;
	} = {}
) => {
	const token = localStorage.getItem(APP_AUTHTOKEN);
	const headers = {
		Authorization: `Bearer ${token}`,
		"Content-Type": options.type ?? "application/json",
		Accept: "application/json",
	};

	options.headers = {
		...headers,
		...options.headers,
	};

	try {
		const config: AxiosRequestConfig = {
			method: method,
			url: `${API_URL}/${path}`,
			headers: options.headers,
		};

		if (method.toLowerCase() === "get") {
			config.params = options.data;
		} else {
			config.data = options.data;
		}

		const { data, status } = await axios(config);

		return {
			status,
			data,
		};
	} catch (e: any) {
		return {
			status: e.response.status,
			data: e.response.data,
		};
	}
};

const getFormattedTime = (timestamp: string) => {
	const diffDays = moment().diff(moment(timestamp), "days");
	const diffHours = moment().diff(moment(timestamp), "hours");
	const diffMinutes = moment().diff(moment(timestamp), "minutes");
	const diffSeconds = moment().diff(moment(timestamp), "seconds");

	let formattedDiff = "";
	if (diffDays > 0) {
		formattedDiff = `${diffDays} days ago`;
	} else if (diffHours > 0) {
		formattedDiff = `${diffHours} hours ago`;
	} else if (diffMinutes > 0) {
		formattedDiff = `${diffMinutes} minutes ago`;
	} else {
		formattedDiff = `${diffSeconds} seconds ago`;
	}

	return formattedDiff;
};

export { fetching, getFormattedTime };
