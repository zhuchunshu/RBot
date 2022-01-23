import axios from "axios";

setInterval(function(){
    axios.post("/admin/api/logger",{
        _token:csrf_token
    }).then(r=>{
        $("#logger").html(r.data.result)
    })

    axios.post("/admin/api/messages",{
        _token:csrf_token
    }).then(r=>{
        $("#messages").html(r.data.result)
    })
    $("#qrcode").attr("style","background-image:url(/qrcode.png);background-size:100% 100%;")
},1500)