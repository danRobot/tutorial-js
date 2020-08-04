function myFn(id,cont){
    if (isNaN(cont)===false) {
        cont=parseInt(cont)+1
    }
    document.getElementById(id).innerHTML=cont
}