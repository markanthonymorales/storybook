import axios from 'axios';

/**
 *
 * @param {*} uri // path to API ex. 127.0.0.1:8000/posts
 * @param {*} callback // returns a callback with error or data
 */

const save = async (uri, payloads, callback) => {
	try {
		const res = await axios.post(uri, { ...payloads });
		return callback(null, res.data);
	} catch (e) {
		return callback(e, null);
	}
};

const fetchAll = async (uri, callback) => {
	try {
		const res = await axios.get(uri);
		return callback(null, res.data);
	} catch (e) {
		return callback(e, null);
	}
};

const find = async (uri, callback) => {
	try {
		const res = await axios.get(uri);
		return callback(null, res.data);
	} catch (e) {
		return callback(e, null);
	}
};

const remove = async (uri, callback) => {
	try {
		const res = await axios.delete(uri);
		return callback(null, res.data);
	} catch (e) {
		return callback(e, null);
	}
};

const update = async (uri, payloads, callback) => {
	try {
		const res = await axios.patch(uri, { ...payloads });
		return callback(null, res.data);
	} catch (e) {
		return callback(e, null);
	}
};

export { fetchAll, remove, find, update, save };
