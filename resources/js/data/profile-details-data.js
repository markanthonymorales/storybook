import { user_rules } from '../modules/rules.js'
import { user_data } from './user-data.js'

export const data = {
	fullScreenLoading: false,
    ...user_data,
    ...user_rules,
    genders: [
	    {
	        value: 'Male',
	        label: 'Male'
	    }, 
	    {
	        value: 'Female',
	        label: 'Female'
	    }
    ],
}