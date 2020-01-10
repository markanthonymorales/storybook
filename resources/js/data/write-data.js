import { story } from './data.js'

export const data = {
	fullScreenLoading: false,
    mobile: false,
    story: { ...story },
    isTyping: false,

    // mark variables
    searchEmail: '',
    inputVisible: false,
    inputValue: '',
    editorData: '',
    currentPage: null,
    holdInputShare: '',
    indecator: true,
    isFileUpload: false,
    fileUploadTotal: 0,
    holdEditorData: '',
}