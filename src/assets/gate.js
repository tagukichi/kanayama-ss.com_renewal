/**
 * プロトタイプの簡易ゲート
 *
 * ⚠️ これはアクセス制御ではありません。
 *    GitHub Pages は静的配信のためサーバー側で認証できず、この判定はすべて
 *    ブラウザ内で完結します。ページや画像のURLを直接叩けば素通しになります。
 *    確実に隠す必要がある場合は Xserver 等に置いて Basic認証をかけてください。
 *
 * <head> から同期読み込みして、本文が描画される前に施錠する（ちらつき防止）。
 */
(function () {
  var KEY = 'kanayama-prototype-gate';
  var HASH = 'vi9rb3';           // djb2("<ID>:<パスワード>") の値。平文は置かない
  var doc = document;

  function djb2(s) {
    var h = 5381;
    for (var i = 0; i < s.length; i++) h = ((h * 33) ^ s.charCodeAt(i)) >>> 0;
    return h.toString(36);
  }

  try {
    if (localStorage.getItem(KEY) === HASH) return;   // 認証済み（同じブラウザなら以後は素通し）
  } catch (e) { /* プライベートモード等で参照できない場合は施錠したまま進む */ }

  doc.documentElement.classList.add('is-locked');

  function unlock() {
    try { localStorage.setItem(KEY, HASH); } catch (e) {}
    doc.documentElement.classList.remove('is-locked');
    var g = doc.querySelector('.gate');
    if (g) g.remove();
  }

  function render() {
    var g = doc.createElement('div');
    g.className = 'gate';
    g.innerHTML =
      '<form class="gate__box" novalidate>' +
        '<svg class="gate__logo" viewBox="0 0 81.962 59.95" aria-hidden="true">' +
          '<path d="M3717.073,589.63a15.734,15.734,0,0,0-28.672-7.651,15.037,15.037,0,0,0-20.822-4.32q-.606.4-1.172.853a20.335,20.335,0,1,0-7.6,37.191V599.928a5.763,5.763,0,1,1,2.137-5.353c.024.171.042.344.051.517l0,20.97a4.305,4.305,0,0,1-2.821,4.074,17.429,17.429,0,0,1-12.358-1.3v14.225a24.951,24.951,0,0,0,22.126-1.221,17.725,17.725,0,0,0,8.254-15.671l.01-24.166a2.469,2.469,0,0,1,4.938.019c0,.026,0,.053,0,.079v24.065h15.891V591.638a2.091,2.091,0,0,1,4.175-.069v24.57l15.869.008-.009-26.518" transform="translate(-3635.12 -575.126)" fill="currentColor"/>' +
        '</svg>' +
        '<p class="gate__en">Prototype</p>' +
        '<h1 class="gate__ttl">有限会社 金山製作所<br>サイトリニューアル案</h1>' +
        '<p class="gate__note">デザイン検証用のプロトタイプです。<br>IDとパスワードを入力してください。</p>' +
        '<label class="gate__f"><span>ID</span>' +
          '<input name="id" type="text" autocomplete="username" autocapitalize="off" autocorrect="off" spellcheck="false" required></label>' +
        '<label class="gate__f"><span>パスワード</span>' +
          '<input name="pw" type="password" autocomplete="current-password" required></label>' +
        '<p class="gate__err" role="alert" hidden>IDまたはパスワードが違います。</p>' +
        '<button class="btn btn--accent gate__submit" type="submit">閲覧する</button>' +
      '</form>';

    var form = g.querySelector('form');
    var err = g.querySelector('.gate__err');
    form.addEventListener('submit', function (e) {
      e.preventDefault();
      var id = form.id.value.trim();
      var pw = form.pw.value;
      if (djb2(id + ':' + pw) === HASH) { unlock(); return; }
      err.hidden = false;
      form.pw.value = '';
      form.pw.focus();
    });

    doc.body.appendChild(g);
    form.id.focus();
  }

  if (doc.readyState === 'loading') doc.addEventListener('DOMContentLoaded', render);
  else render();
})();
