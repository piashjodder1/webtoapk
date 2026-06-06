<script src="https://unpkg.com/lucide@latest"></script>
<script>
  lucide.createIcons();
  const navbar = document.getElementById('navbar');
  if(navbar) {
      window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 40);
      });
  }



  const revealEls = document.querySelectorAll('.reveal');
  if(revealEls.length > 0 && typeof IntersectionObserver !== 'undefined') {
      const io = new IntersectionObserver((entries) => {
        entries.forEach((e, i) => {
          if (e.isIntersecting) {
            setTimeout(() => e.target.classList.add('visible'), i * 80);
            io.unobserve(e.target);
          }
        });
      }, { threshold: 0.12 });
      revealEls.forEach(el => io.observe(el));
  }
</script>
