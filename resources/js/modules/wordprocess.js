const getOverlappedChild = async (self, cloneEditorElem, lastChildElemHolder, callback) => {
	try{
		// let mobileMaxHeight = 750
		let desktopMaxHeight = 750
		console.log(cloneEditorElem.scrollHeight)
		console.dir(cloneEditorElem)

		// if(self.mobile && cloneEditorElem.lastChild && cloneEditorElem.scrollHeight > mobileMaxHeight){

		// 	if(cloneEditorElem.children.length < 2)
		// 		throw('Error: Not allowed to paste long content!')

		// 	// get the last child element
		// 	lastChildElemHolder.push(cloneEditorElem.lastChild)

		// 	if(lastChildElemHolder.length > 0){
		// 		// remove last insert child in lastchildelemholder in cloneEditorElem
		// 		cloneEditorElem
		// 		.removeChild(lastChildElemHolder[lastChildElemHolder.length - 1]) 

		// 		return callback(self, cloneEditorElem, lastChildElemHolder, callback)
		// 	}
		// }

		if(cloneEditorElem.lastChild && cloneEditorElem.scrollHeight > desktopMaxHeight){

			if(cloneEditorElem.children.length < 2)
				throw('Error: Not allowed to paste long content!')

			// get the last child element
			lastChildElemHolder.push(cloneEditorElem.lastChild)

			if(lastChildElemHolder.length > 0){
				// remove last insert child in lastchildelemholder in cloneEditorElem
				cloneEditorElem
				.removeChild(lastChildElemHolder[lastChildElemHolder.length - 1]) 

				return callback(self, cloneEditorElem, lastChildElemHolder, callback)
			}
		}

		// console.log('Get overlapped!')
		return [cloneEditorElem, lastChildElemHolder, null]
	}catch(err){
		return [null, null, err]
	}
};

const assignContentToIndexPage = async (self, arr, index, elem) => {
	try{
		let cloneEditorElem = arr[0]
		let lastChildElemHolder = arr[1]

		if(lastChildElemHolder.length > 0 && elem.childElementCount > 1) {
			// remove cloned element to container
			elem.removeChild(elem.lastChild)

			// check if the wordprocess index is same to current page index
			if(index == self.currentPage) {
				// if yes then get the position of current cursor

				// change content value of current page
				self.story.pages[index].content = cloneEditorElem.innerHTML
			}else{
				self.story.pages[index].content = cloneEditorElem.innerHTML
			}
		}

		// console.log('Assign Data!')
		return elem;
	}catch(err){
		console.log(err)
	}
}

const appendElement = async (self, handler, container) => {
	try{
		for await (let elem of handler){
			container.append(elem)
		}

		// console.log('append element!')
		return container;
	}catch(err){
		console.log(err)
	}
}

const setNewContent = async (self, index, childsContainer) => {
	try{
		// create element to hold the next page content
		let newElem = document.createElement('div')

		// check current index if exists
		if(typeof self.story.pages[index + 1] == 'undefined'){
			// create new page if not exists
			self.story.pages.push({
				'id': null,
				'is_colored': 0,
				'content': ''
			})
		}

		// move the content of next page to new element
		newElem.innerHTML = childsContainer.innerHTML + self.story.pages[index + 1].content
		
		// move the new element content to next page
		self.story.pages[index + 1].content = newElem.innerHTML

		// call wordprocess to double check content overlapped
		// console.log('Set New Data')
		return true;
	}catch(err){
		console.log(err)
	}
}

const wordProcess2 = async (self, currentPage) => {
	try{

		// get current index page, get container element by class, get editor element, and define initial bool to false
		let getCurrentIndex = currentPage, getContainerElem = document.getElementsByClassName("big-paper"), getEditorElem = document.getElementById("editor"), bool = false
		// console.dir(getContainerElem[0])
		// get single element of container
		let getElem = getContainerElem[0]

		// initialized last child holder as array
		let lastChildElemHolder = [];

		// clone editor element
		let cloneEditorElem = getEditorElem.cloneNode(true)
		
		cloneEditorElem.classList.add('clone')

		// insert current page content to cloned editor element
		cloneEditorElem.innerHTML = self.story.pages[getCurrentIndex].content

		// append clone editor element to container
		getElem.append(cloneEditorElem)
		
		// do looping for checking the content of container editor if the 
		// container is overlapped or not and move the overlapped value to next container
		const getOverlappedChildResult = await getOverlappedChild(self, cloneEditorElem, lastChildElemHolder, getOverlappedChild)
		// console.log(getOverlappedChildResult)

		if(getOverlappedChildResult[2])
			throw(getOverlappedChildResult[2])

		cloneEditorElem = getOverlappedChildResult[0]
		lastChildElemHolder = getOverlappedChildResult[1]
		
		if(lastChildElemHolder.length < 1){
			// getElem.removeChild
			getElem.removeChild(getElem.lastChild)
			return getCurrentIndex
		}

		const getNewElem = await assignContentToIndexPage(self, getOverlappedChildResult, getCurrentIndex, getElem)
		
		// console.log('override data elem!')
		getElem = getNewElem

		// initialized variable that hold the last childs
		let lastChildsContent = document.createElement('div')

		// reverse the sorting of lastchildelemholder
		lastChildElemHolder.reverse()

		// console.log('reverse!')
		const getAppendElement = await appendElement(self, lastChildElemHolder, lastChildsContent)

		// console.log('create new page')
		const getFinalResult = await setNewContent(self, getCurrentIndex, getAppendElement)

		console.log('%cWord Process: %cInprogress...', 'color: #fff; font-size: 18px;', 'color: #e3342f; font-size: 18px;', )
		return await wordProcess2(self, getCurrentIndex + 1)
	}catch(err){
		self.story.pages[currentPage].content = ''
		self.$notify.error({
			title: 'Error',
			message: err
		})
		return false
	}
}

const splitContent = async(newElem) => {
    let content = ''
	for (let child of newElem.children) {
        let getChild = child.outerText.match(/.{0,80}/g)
        // console.log('getChild:', getChild)
        if(getChild.length > 0){
            for (let newChild of getChild){
            	if(newChild != '')
	                content += `<p>${newChild}</p>`
            }
        }
    }

    return content
}

export { wordProcess2, splitContent }