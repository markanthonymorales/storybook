class UploadAdapter {
    constructor(loader) {
        this.loader = loader;
    }

    upload() {
        return new Promise((resolve, reject) => {
            const data = new FormData();
            data.append('file', this.loader.file);
            data.append('allowSize', 10);// M
            $.ajax({
                url: './uploadfile',
                type: 'POST',
                data: data,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.file) {
                        resolve({
                            default: './uploads/' + data.file
                        });
                    } else {
                        reject(data.msg);
                    }

                }
            });

        });
    }

    abort() {
    }

    test(){
        return 'mark'
    }
}