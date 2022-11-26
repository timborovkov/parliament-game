$(document).ready(function(){
    $('.mobilemenu').hide();

    var mySwiper = new Swiper ('.swiper-container', {
        loop: true
    })
});

function formatUSD(n) {
	return String(n).replace(/(.)(?=(\d{3})+$)/g,'$1 ');
}