document.addEventListener('DOMContentLoaded', function() {
    let lastScrollTop = 0;
    const navbar = document.querySelector('nav');

    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop) {
            navbar.style.top = '-80px';
        } else {
            navbar.style.top = '0';
        }
        lastScrollTop = scrollTop;
    });
});

document.addEventListener('DOMContentLoaded', function() {
    var myCarousel = document.querySelector('.carousel');
    var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 5000,
        ride: 'carousel'
    });
});

function goToTop() {
    window.scrollTo({top: 0, behavior: 'smooth'});
}

window.onscroll = function() {
    var goTopBtn = document.getElementById("goTopBtn");
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        goTopBtn.classList.add("show");
    } else {
        goTopBtn.classList.remove("show");
    }
};

document.getElementById("menuBtn").onclick = function() {
    var sidebar = document.getElementById("tiendaSidebar");
    var menuBtn = document.querySelector(".menu-btn");
    
    menuBtn.classList.toggle("expanded");
    
    if (menuBtn.classList.contains("expanded")) {
        sidebar.style.width = "200px";
        sidebar.style.height = "100%";
        sidebar.style.paddingTop = "25%"; 
        sidebar.style.display = "block"; 
    } else {
        sidebar.style.width = "0";
        sidebar.style.height = "auto";
        sidebar.style.paddingTop = "0";
        sidebar.style.display = "none";
    }
};
