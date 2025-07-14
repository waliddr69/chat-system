const form = document.querySelector("form");

const sendBtn = form.querySelector("button");
const section = document.querySelector("section");
function loadmessages(){
    
    let xhr = new XMLHttpRequest();
        xhr.open("POST","get_messages.php");
        xhr.onload = ()=>{
            if(xhr.readyState == 4 && xhr.status==200){
            let data = xhr.response;
            
            section.innerHTML = data;

            
            

        }
        }

        xhr.send();
        
}




form.addEventListener("submit",function(e){
    e.preventDefault();
    
        
        let xhr1 = new XMLHttpRequest();
        xhr1.open("POST","send_messages.php");
        xhr1.onload = ()=>{
            if (xhr1.readyState == 4 && xhr1.status == 200){
                let data = xhr1.response;
                
                if(data == "inserted"){
                    form.reset();
                    loadmessages();
                    
                    
                }else{
                    loadmessages();
                    
                }

        }
    
   
} 
let formdata = new FormData(form);
    xhr1.send(formdata);
});
