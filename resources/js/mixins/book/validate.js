let checkFromDate = (rule, value, callback) => {
	if (!value) {
		return callback(new Error('Please pick a date'));
	}
	setTimeout(() => {
		if (value > this.book.to_date) {
			callback(new Error('Date must be lesser than or equal to date "To"'));
		} else {
			callback();
		}
	}, 500);
};

let checkToDate = (rule, value, callback) => {
	if (!value) {
		return callback(new Error('Please pick a date'));
	}
	setTimeout(() => {
		if (value < this.book.from_date) {
			callback(new Error('Date must be greater than or equal to date "From"'));
		} else {
			callback();
		}
	}, 500);
};