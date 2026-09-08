/* 金山製作所 プロトタイプ — 最小限の挙動 */
(function () {
  var burger = document.querySelector('.burger');
  var nav = document.getElementById('gnav');
  if (burger && nav) {
    burger.addEventListener('click', function () {
      var open = burger.getAttribute('aria-expanded') === 'true';
      burger.setAttribute('aria-expanded', String(!open));
      burger.setAttribute('aria-label', open ? 'メニューを開く' : 'メニューを閉じる');
      nav.setAttribute('data-open', String(!open));
      document.body.style.overflow = open ? '' : 'hidden';
    });
  }

  var reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* ---- FVスライダー ----
     画像の入れ替えだけを行う。キャッチコピーは data-overlay="false" のスライドで退避する。
     WordPress 化の際は、スライドのDOMをテンプレート側で出力すればこのJSはそのまま使える。 */
  (function () {
    var box = document.querySelector('[data-slider]');
    if (!box) return;
    var slides = [].slice.call(box.querySelectorAll('.fv__slide'));
    if (slides.length < 2) return;

    var fv = box.closest('.fv');
    var dots = [].slice.call(document.querySelectorAll('.fv__dot'));
    var toggle = document.querySelector('[data-toggle]');
    var interval = parseInt(box.dataset.interval, 10) || 6000;
    var i = 0, timer = null, paused = reduce;

    function show(next) {
      i = (next + slides.length) % slides.length;
      slides.forEach(function (s, n) {
        var on = n === i;
        if (on) s.setAttribute('data-active', ''); else s.removeAttribute('data-active');
        s.setAttribute('aria-hidden', String(!on));
      });
      dots.forEach(function (d, n) {
        if (n === i) d.setAttribute('aria-current', 'true'); else d.removeAttribute('aria-current');
      });
      // 画像だけを見せるスライドではキャッチコピーを隠す
      fv.classList.toggle('fv--bare', slides[i].dataset.overlay === 'false');
    }

    function start() { stop(); if (!paused) timer = setInterval(function () { show(i + 1); }, interval); }
    function stop() { if (timer) { clearInterval(timer); timer = null; } }

    document.querySelectorAll('[data-move]').forEach(function (btn) {
      btn.addEventListener('click', function () { show(i + Number(btn.dataset.move)); start(); });
    });
    dots.forEach(function (d) {
      d.addEventListener('click', function () { show(Number(d.dataset.go)); start(); });
    });
    if (toggle) {
      toggle.setAttribute('aria-pressed', String(paused));
      toggle.addEventListener('click', function () {
        paused = !paused;
        toggle.setAttribute('aria-pressed', String(paused));
        toggle.setAttribute('aria-label', paused ? '自動切り替えを再開する' : '自動切り替えを止める');
        if (paused) stop(); else start();
      });
    }

    // 操作中・非表示中は自動送りを止める
    fv.addEventListener('mouseenter', stop);
    fv.addEventListener('mouseleave', start);
    fv.addEventListener('focusin', stop);
    fv.addEventListener('focusout', start);
    document.addEventListener('visibilitychange', function () { document.hidden ? stop() : start(); });

    show(0);
    start();
  })();

  // ファーストビューの実績数をカウントアップ（最終値はHTML側に持たせてあるのでJSが動かなくても表示は正しい）
  var counter = document.querySelector('[data-count]');
  if (counter && !reduce) {
    var goal = parseInt(counter.textContent.replace(/[^0-9]/g, ''), 10);
    if (goal > 0) {
      var t0 = null, dur = 1500, delay = 700;
      counter.textContent = '0';
      var step = function (t) {
        if (t0 === null) t0 = t;
        var p = Math.min((t - t0) / dur, 1);
        var eased = 1 - Math.pow(1 - p, 3);
        counter.textContent = Math.round(goal * eased).toLocaleString('ja-JP');
        if (p < 1) requestAnimationFrame(step);
      };
      setTimeout(function () { requestAnimationFrame(step); }, delay);
    }
  }

  var targets = document.querySelectorAll('.reveal');
  if (reduce || !('IntersectionObserver' in window)) {
    targets.forEach(function (el) { el.classList.add('is-in'); });
    return;
  }
  var io = new IntersectionObserver(function (entries) {
    entries.forEach(function (e) {
      if (e.isIntersecting) { e.target.classList.add('is-in'); io.unobserve(e.target); }
    });
  }, { rootMargin: '0px 0px -12% 0px' });
  targets.forEach(function (el) { io.observe(el); });
})();
