window.onload = function (){
    const getHome = document.getElementById('btn-home');
    if(getHome){
        getHome.addEventListener("click", function(){
            window.location.href = "index.php";
        })
    }
}