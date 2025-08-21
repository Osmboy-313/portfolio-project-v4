export function customFileUpload() {
    let customFileUpload = document.querySelector("#custom-file-upload");
    let fileUploadInput = document.querySelector('#file-upload-input');
    let fileUploadMsg = document.querySelector('#file-upload-msg');

    if (!customFileUpload) return;
    
    customFileUpload.addEventListener("click", function () {
        fileUploadInput.click();
        customFileUpload.classList.add('focus');
    });

    document.addEventListener("click", function (e) {
        if (!e.target.closest("#custom-file-upload")) {
            customFileUpload.classList.remove('focus');
        }
    })

    fileUploadInput.addEventListener("change", async function () {
        if (fileUploadInput.value) {
            // fileUploadMsg.innerHTML = fileUploadInput.value.match(
            //     /[\/\\]([\w\d\s\.\-\(\)]+)$/
            // );
            fileUploadMsg.textContent = fileUploadInput.files[0].name;
        }

        if(this.files.length > 0){

            const hasTempFile = document.querySelector('input[name="existing-temp-file"]');

            if(hasTempFile){
                const response = await fetch('index.php?c=post&a=clearTempFile', {
                    method : 'POST',
                    headers : {'Content-Type' : 'application/json'}
                });
                const result = await response.json();
                
                if(result.status === 'success'){
                    console.log('TEMP FILE CLEARANCE FROM PHP : ',result.status);
                    hasTempFile.remove();
                }
            }

        }

    });
}
