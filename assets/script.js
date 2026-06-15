// Minimal JS for demo
document.addEventListener('DOMContentLoaded', function(){
	// Smooth scroll for hero CTA
	document.querySelectorAll('a[href^="#"]').forEach(function(a){
		a.addEventListener('click', function(e){
			var t = document.querySelector(this.getAttribute('href'));
			if (t) { e.preventDefault(); t.scrollIntoView({behavior:'smooth',block:'start'}); }
		});
	});
	// Simple hover lift for product cards
	document.querySelectorAll('.product').forEach(function(card){
		card.addEventListener('mouseenter', ()=> card.style.transform = 'translateY(-6px)');
		card.addEventListener('mouseleave', ()=> card.style.transform = 'translateY(0)');
	});
});
