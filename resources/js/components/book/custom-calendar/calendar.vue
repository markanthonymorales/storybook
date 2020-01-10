<template>
	<div class="el-custom_calendar">
		<div class="el-custom_calendar_title">
			<label>{{ title }}</label>
		</div>
		<div class="el-custom_calendar_body">
			<table class="el-custom_calendar_table" v-on:click="handleClick" ref="table">
				<thead>
					<tr>
						<td>Sun</td>
						<td>Mon</td>
						<td>Tue</td>
						<td>Wed</td>
						<td>Thu</td>
						<td>Fri</td>
						<td>Sat</td>
					</tr>
				</thead>
				<tbody v-html="date">
				</tbody>
			</table>
		</div>
	</div>
</template>
<script>
	export default {
		name: 'ElCustomCalendar',
		props: {
			value: String,
			title: String,
			selected: String,
			range: {
			    type: Array,
			    validator(range) {
			    	return true;
			        if (Array.isArray(range)) {
			          	return range.length === 2 && range.every(
			            	item => typeof item === 'string' ||
			            	typeof item === 'number' ||
			            	item instanceof Date);
			        } else {
			        	return true;
			        }
			    }
		    },
		    storyList: {
		    	type: Array,
			    validator(range) {
			    	return true;
			        if (Array.isArray(range)) {
			          	return range.length === 2 && range.every(
			            	item => typeof item === 'string' ||
			            	typeof item === 'number' ||
			            	item instanceof Date);
			        } else {
			        	return true;
			        }
			    }
		    }
		},
		provide() {
		    return {
		    	elCustomCalendar: this
		    };
		},
		created(){
			let self = this
			setTimeout(function(){
				let currentMonth = self.value.split('-')
				let dd = self.selected.split('-')
				if(currentMonth[1] == dd[1]){

					let elem = self.$refs.table
					let elems = elem.getElementsByTagName('td')
					for (let i = 0; i < elems.length; i++)
						if(elems[i].attributes.length > 0){
							let getDay = elems[i].attributes[elems[i].attributes.length - 1].value.split('-')
							if(parseInt(getDay[getDay.length - 1]) == dd[2])
								elems[i].children[0].classList.add('active')
						}
				}
			}, 2000)
		},
		watch: {
			selected(){
				let self = this
				let getMin = self.range[0].split('-')
				let getMax = self.range[1].split('-')
				let dd = self.selected.split('-')

				let from = new Date(getMin[0], parseInt(getMin[1])-1, 0)
				let to = new Date(getMax[0], parseInt(getMax[1])-1, 0)
				let check = new Date(dd[0], parseInt(dd[1])-1, 0)

                if(check >= from && check <= to){
                	// console.log('Good')
                }else{
                	let elem = self.$refs.table
					let elems = elem.getElementsByTagName('span')
					for (let i = 0; i < elems.length; i++) {
					  	elems[i].classList.remove('active')
					}
                }
			}
		},
		methods: {
			handleClick (e) {
				let path = e.path || (e.composedPath && e.composedPath())
				if((path[0].localName == 'span') || (path[0].localName == 'i')){
					let self = this
					let elem = self.$refs.table
					let elems = elem.getElementsByTagName('span')
					for (var i = 0; i < elems.length; i++) {
					  	elems[i].classList.remove('active')
					}

					let index = 1
					if(path[0].localName == 'i')
						index = 2

			      	path[index - 1].classList.add('active')
			      	Event.$emit('selectedDate', path[index].attributes[0].value)
				}
		    }
		},
		computed: {
			date(){
				let self = this
				let minDate = new Date(self.range[0])
				let maxDate = new Date(self.range[1])
				let split_range = self.range[1].split('-')
				let startDay = minDate.getDay()
				let end = maxDate.getDate()

				let table_body = '';
				let days = 7
				let weeks = Math.ceil(parseInt(end + startDay) / days)
				let day = 1
				for(let w = 0; w <= weeks; w++){
					if(day <= end){
						table_body += `<tr>`
						for(let c = 1; c <= days; c++){
							if(w < 1){
								if(c <= startDay){
									table_body += `<td><span></span></td>`
									continue
								}
							}

							if(day > end){
								table_body += `<td><span></span></td>`
								continue
							}

							table_body += `<td rel="`+split_range[0]+`-`+split_range[1]+`-`+day+`"><span>`
							let count_owned = 0;
							let count_shared = 0;
							self.storyList.forEach(function(i){

								let sd = i.story_date
								if(!sd)
									sd = i.from_date
								sd = sd.split('-')
								let story_date = new Date(sd[0], sd[1]-1, sd[2])
								let td = new Date(split_range[0], split_range[1]-1, day)
								
								if(+story_date === +td){

									let icon =  ``

									if(i.is_shared){
										if(count_shared == 0){
											count_shared++
											icon =  `<i class="icon-shared"></i> `
										}
									}else{
										if(count_owned == 0){
											count_owned++
											icon =  `<i class="icon-owned"></i> `
										}
									}

									table_body += icon
								}
							})

							table_body += day+`</span></td>`
							day++
						}
					}
					table_body += `</tr>`
				}

				return table_body
			}
		},
		data () {
		    return {
		      	// selectedDate: this.selected
		    }
	  	},
	}
</script>