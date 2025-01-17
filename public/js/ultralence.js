function slide(e,i) {
    var nextSlide = document.getElementById("slide-"+e);
    var currentSlide = document.getElementById("slide-"+i);
    
    if (!nextSlide || !currentSlide) {
        return;
    }
    
    slideNum = nextSlide ? e : 1;
    currentSlide.className = "slide";
    document.getElementById("slide-"+slideNum).className = "slide-active";
    
    var s = slideNum + 1;
    timeSlide = setTimeout(function(){
        slide(s,slideNum);
    }, 10000);
    stopAutoSlide();
}

function stopAutoSlide() {
    clearTimeout(timeSlide);
}

if (document.getElementById("slide-1")) {
    setTimeout(function(){
        slide(2,1);
    }, 10000);
}

var timeSlide;

$(document).ready(function(){
    $(".sidebar-menu > li.nav-dropdown a").on("click",function(e){
        if($(this).parent().hasClass("active")){
            $(this).next().slideToggle();
            $(".sidebar-menu li").removeClass("active");
        }else{
            $(".sidebar-menu li ul").slideUp();
            $(this).next().slideToggle();
            $(".sidebar-menu li").removeClass("active");
            $(this).parent().addClass("active");
        }
    });
});
