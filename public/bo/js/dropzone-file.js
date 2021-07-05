

function handleDrop (e) {
    e.preventDefault();
    e.stopPropagation();
    let files = [...e.dataTransfer.files];
    files = triFile(files);
    let final =new FileListItems(files);
    inputRef.files = final;
    if (files && files.length) {
        files.forEach(function(file, index){
            displayImage(files, index) ;
        }) ;
        
        
    }
};

function triFile(files){
    var filesExisting = inputRef.files;
    const existingFiles = Array.from(filesExisting).map((f) => f.name);
    files = files.filter((f) => !existingFiles.includes(f.name));
    files = [...files,...filesExisting ];
    return files;
};

function displayImage (files, i) {
    document.getElementById("dropzone-zone-file-description").style.display = "none";
    var reader = new FileReader();
    let images =[]
    reader.onload = (evnt) => {
        var div = document.createElement('div');
        div.classList.add("image");
        div.id = 'image'+i;
        var span = document.createElement('span');
        span.id = "span"+i;
        span.innerHTML= "x"
        var img = document.createElement('img');
        
        var fileType = files[i].type ;
        var fileTypes = fileType.split("/") ;
        if(fileTypes[0] == 'image'){
            img.src =  evnt.target.result;
        }else{
            var name = files[i].name ;
            var extensions = name.split('.') ;
            var extension = extensions[extensions.length - 1] ;
            img.src =  "/bo/images/file.png";
            switch(extension){
                case "doc":
                case "docx" :
                    img.src =  "/bo/images/doc.png";
                    break;
                case "ptt" :
                case "pttx" :
                    img.src =  "/bo/images/ptt.png";
                    break;
                case "xls" :
                case "xlsx" :
                    img.src =  "/bo/images/xls.png";
                    break;
                case "pdf" :
                    img.src =  "/bo/images/pdf.png";
                    break;
                default :
                    img.src =  "/bo/images/file.png";
                    break;
            }
        }
        
        var p = document.createElement('p');
        p.innerHTML= files[i].name.substring(0,13);
        img.setAttribute("title", files[i].name);
        div.appendChild(img);
        div.appendChild(span);
        div.appendChild(p);
        document.getElementById("images").appendChild(div);
        span.onclick = function(){
            document.getElementById("images").removeChild(document.getElementById("image"+i));
            let files = [...inputRef.files] ;
            let finaleFiles = files.filter((e,index)=> index != i );
            finaleFiles = new FileListItems(finaleFiles);
            inputRef.files = finaleFiles;
        };


        images.push(evnt.target.result);
    };
    reader.readAsDataURL(files[i]);
};

function handleDragEnter (e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDragLeave (e) {
    e.preventDefault();
    e.stopPropagation();
};

function handleDragOver (e){
    e.preventDefault();
    e.stopPropagation();
    e.dataTransfer.dropEffect = "copy";
}
function selectFiles(images) {
    let files = [...images];
    files.forEach(function(file, index){
    console.log(file) ;
    displayImage(files, index) ;
}) ;
};
function FileListItems (files) {
    var b = new ClipboardEvent("").clipboardData || new DataTransfer()
    for (var i = 0, len = files.length; i<len; i++) b.items.add(files[i])
    return b.files
}