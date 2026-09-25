$(function(){
  /* Navbar scroll */
  $(window).on('scroll',function(){$('#navbar').toggleClass('scrolled',$(this).scrollTop()>40)});
  /* Mobile menu */
  $('#menuBtn').on('click',function(){$('#mobileMenu').toggleClass('open')});
  $('#mobileMenu a').on('click',function(){$('#mobileMenu').removeClass('open')});
  /* Smooth scroll */
  $('a[href^="#"]').on('click',function(e){
    var t=$(this.getAttribute('href'));
    if(t.length){e.preventDefault();$('html,body').animate({scrollTop:t.offset().top-68},500)}
  });
  /* Scroll reveal */
  var obs=new IntersectionObserver(function(entries){
    entries.forEach(function(en){if(en.isIntersecting)$(en.target).addClass('visible')});
  },{threshold:0.1});
  document.querySelectorAll('.fade-in').forEach(function(el){obs.observe(el)});
});
