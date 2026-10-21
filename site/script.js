(function () {
  var btn = document.querySelector('.menu-btn'), nav = document.getElementById('nav');
  btn.addEventListener('click', function () {
    var open = nav.classList.toggle('open');
    btn.setAttribute('aria-expanded', open);
  });
  nav.addEventListener('click', function () { nav.classList.remove('open'); btn.setAttribute('aria-expanded', false); });

  var top = document.querySelector('.to-top');
  addEventListener('scroll', function () { top.classList.toggle('show', scrollY > 600); }, { passive: true });

  var io = new IntersectionObserver(function (es) {
    es.forEach(function (e) {
      if (!e.isIntersecting) return;
      io.unobserve(e.target);
      var el = e.target, end = +el.dataset.count, suf = el.dataset.suffix || '', t0 = performance.now();
      (function tick(t) {
        var p = Math.min((t - t0) / 1200, 1);
        el.textContent = Math.round(end * p) + (p === 1 ? suf : '');
        if (p < 1) requestAnimationFrame(tick);
      })(t0);
    });
  }, { threshold: .6 });
  document.querySelectorAll('[data-count]').forEach(function (el) { io.observe(el); });
})();
