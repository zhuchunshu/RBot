import axios from "axios";

setInterval(function(){
    axios.post("/admin/api/logger",{
        _token:csrf_token
    }).then(r=>{
        $("#logger").html(r.data.result)
    })


},1500)
axios.post("/admin/api/getQrcode",{
    _token:csrf_token,
}).then(r=>{
    var data = r.data;
    if(data.success){
        var img = data.result.url;
        $("#qrcode").empty()
        $("#qrcode").html('<img src="'+img+'" />');
    }
})
setInterval(function(){
    axios.post("/admin/api/getQrcode",{
        _token:csrf_token,
    }).then(r=>{
        var data = r.data;
        if(data.success){
            var img = data.result.url;
            $("#qrcode").empty()
            $("#qrcode").html('<img src="'+img+'" height="300px" width="300px" />');
        }
    })
},500)