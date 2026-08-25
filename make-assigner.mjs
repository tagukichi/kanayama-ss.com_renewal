/**
 * prototype/assign.html を生成する。
 * ブラウザで開くと、現行サイトの写真を見ながら45枠へ割り当てられる。
 * （このセッションからは画像を取得できないため、割り当ての目視判断はブラウザ側で行う）
 */
import { readFileSync, writeFileSync } from 'node:fs';

const manifest = JSON.parse(readFileSync('src/images.json', 'utf8'));
const pool = JSON.parse(readFileSync('src/media-pool.json', 'utf8')).media;

const PAGE_NAMES = {
  index:'トップページ', concept:'私たちの想い', solar:'太陽光発電・蓄電', electric:'電気・水道工事',
  works:'工事実績（一覧）', 'works-detail':'工事実績（詳細）', about:'会社案内', message:'代表メッセージ',
  recruit:'採用情報', interview:'社員紹介', news:'お知らせ', contact:'お問い合わせ', privacy:'プライバシーポリシー',
};

/** スロットのクラスから必要なアスペクト比を割り出す */
const RATIO = { '-header':[16/9,'横長 16:9'], '01':[16/9,'横長'] };
function ratioOf(e) {
  const s = (e.size || '').match(/(\d+)×(\d+)/);
  if (s) { const r = +s[1] / +s[2]; return [r, r > 1.25 ? '横長' : r < 0.85 ? '縦長' : '正方形寄り']; }
  return [16/9, '横長'];
}

const slots = manifest.images.map((e) => {
  const [r, kind] = ratioOf(e);
  return { ...e, ratio: +r.toFixed(3), orient: kind };
});

writeFileSync('prototype/assign.html', `<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>画像割り当てツール</title>
<style>
:root{ --green:#179918; --orange:#FF6900; --ink:#161A15; --muted:#5B655A; --line:#DEE3DC; --soft:#F4F6F2; }
*,*::before,*::after{ box-sizing:border-box; }
body{ margin:0; background:var(--soft); color:var(--ink); font:14px/1.7 "Hiragino Kaku Gothic ProN",Meiryo,sans-serif; }
header{ position:sticky; top:0; z-index:10; background:#0E120D; color:#fff; padding:12px 20px; display:flex; gap:16px; align-items:center; flex-wrap:wrap; }
header h1{ font-size:15px; margin:0; letter-spacing:.04em; }
header .count{ font-size:12px; color:#9BE49C; }
header .sp{ margin-left:auto; display:flex; gap:8px; }
button{ font:inherit; cursor:pointer; border:1px solid var(--line); background:#fff; padding:8px 14px; }
button.primary{ background:var(--green); color:#fff; border-color:var(--green); }
button.ghost{ background:transparent; color:#fff; border-color:rgba(255,255,255,.4); }
main{ display:grid; grid-template-columns:minmax(380px,1fr) minmax(340px,420px); gap:16px; padding:16px; align-items:start; }
@media (max-width:900px){ main{ grid-template-columns:1fr; } }
.panel{ background:#fff; border:1px solid var(--line); }
.panel > h2{ font-size:12px; letter-spacing:.14em; margin:0; padding:12px 16px; border-bottom:1px solid var(--line); color:var(--muted); position:sticky; top:52px; background:#fff; z-index:2; }
.grp{ font-size:11px; font-weight:700; letter-spacing:.1em; color:#fff; background:var(--green); padding:5px 16px; }
.slot{ display:grid; grid-template-columns:74px 1fr; gap:12px; padding:12px 16px; border-bottom:1px solid var(--line); cursor:pointer; }
.slot:hover{ background:var(--soft); }
.slot[aria-selected="true"]{ background:#EAF6EA; box-shadow:inset 3px 0 0 var(--orange); }
.slot .thumb{ width:74px; aspect-ratio:4/3; background:var(--soft); border:1px solid var(--line); display:grid; place-items:center; font-size:10px; color:var(--muted); overflow:hidden; }
.slot .thumb img{ width:100%; height:100%; object-fit:cover; display:block; }
.slot .id{ font:11px/1.4 ui-monospace,monospace; color:var(--muted); }
.slot .lab{ font-weight:700; font-size:13px; }
.slot .req{ font-size:11px; color:var(--muted); }
.slot .warn{ font-size:11px; color:#C23A00; font-weight:700; }
.slot input{ font:inherit; font-size:12px; width:100%; padding:5px 8px; border:1px solid var(--line); margin-top:5px; }
.pool{ display:grid; grid-template-columns:repeat(auto-fill,minmax(140px,1fr)); gap:10px; padding:14px; }
.card{ border:1px solid var(--line); background:var(--soft); cursor:pointer; }
.card:hover{ border-color:var(--green); }
.card img{ width:100%; aspect-ratio:4/3; object-fit:cover; display:block; background:#ddd; }
.card .meta{ padding:6px 8px; font:10px/1.5 ui-monospace,monospace; color:var(--muted); word-break:break-all; }
.card .dim{ color:var(--ink); font-weight:700; }
.hint{ padding:12px 16px; font-size:12px; color:var(--muted); background:#FFF8F2; border-bottom:1px solid var(--line); }
textarea{ width:100%; height:220px; font:11px/1.5 ui-monospace,monospace; padding:10px; border:1px solid var(--line); }
dialog{ border:1px solid var(--line); padding:0; width:min(760px,92vw); }
dialog .in{ padding:16px; display:grid; gap:10px; }
</style>
</head>
<body>
<header>
  <h1>画像割り当てツール</h1>
  <span class="count" id="count"></span>
  <span class="sp">
    <button class="ghost" id="clear">全解除</button>
    <button class="primary" id="export">images.json を出力</button>
  </span>
</header>

<main>
  <section class="panel">
    <h2>割り当て先（45枠）</h2>
    <div class="hint">左の枠をクリックして選択 → 右の写真をクリックで割り当て。もう一度同じ写真を押すと解除します。<br>入力内容はブラウザに自動保存されます。</div>
    <div id="slots"></div>
  </section>
  <section class="panel">
    <h2>使える写真（<span id="poolCount"></span>枚）</h2>
    <div class="pool" id="pool"></div>
  </section>
</main>

<dialog id="out"><div class="in">
  <b>src/images.json をこの内容で置き換えてください</b>
  <textarea id="json" readonly></textarea>
  <div style="display:flex;gap:8px">
    <button class="primary" id="copy">クリップボードにコピー</button>
    <button id="close">閉じる</button>
  </div>
</div></dialog>

<script>
const SLOTS = ${JSON.stringify(slots)};
const POOL = ${JSON.stringify(pool)};
const PAGES = ${JSON.stringify(PAGE_NAMES)};
const KEY = 'kanayama-assign-v1';
let state = JSON.parse(localStorage.getItem(KEY) || '{}');
let sel = null;
const dims = {};

const $ = (s) => document.querySelector(s);
const save = () => localStorage.setItem(KEY, JSON.stringify(state));

function orientOf(url) {
  const d = dims[url];
  if (!d) return null;
  const r = d.w / d.h;
  return r > 1.25 ? '横長' : r < 0.85 ? '縦長' : '正方形寄り';
}

function renderSlots() {
  const host = $('#slots');
  host.innerHTML = '';
  let page = null;
  for (const s of SLOTS) {
    if (s.page !== page) {
      page = s.page;
      const g = document.createElement('div');
      g.className = 'grp';
      g.textContent = PAGES[page] || page;
      host.appendChild(g);
    }
    const cur = state[s.id] || {};
    const el = document.createElement('div');
    el.className = 'slot';
    el.setAttribute('aria-selected', String(sel === s.id));
    const o = cur.src ? orientOf(cur.src) : null;
    const mismatch = o && o !== s.orient;
    el.innerHTML = \`
      <div class="thumb">\${cur.src ? '<img src="' + cur.src + '" alt="">' : '未割当'}</div>
      <div>
        <div class="id">\${s.id}</div>
        <div class="lab">\${s.label}</div>
        <div class="req">推奨 \${s.size || '—'}（\${s.orient}）\${mismatch ? ' <span class="warn">⚠ 割当は' + o + '</span>' : ''}</div>
        <input placeholder="alt（空欄なら用途から自動補完）" value="\${(cur.alt || '').replace(/"/g,'&quot;')}" data-alt="\${s.id}">
      </div>\`;
    el.addEventListener('click', (ev) => {
      if (ev.target.matches('input')) return;
      sel = sel === s.id ? null : s.id;
      renderSlots();
    });
    el.querySelector('input').addEventListener('input', (ev) => {
      state[s.id] = Object.assign({}, state[s.id], { alt: ev.target.value });
      save();
    });
    host.appendChild(el);
  }
  const n = SLOTS.filter((s) => (state[s.id] || {}).src).length;
  $('#count').textContent = n + ' / ' + SLOTS.length + ' 枠 割り当て済み';
}

function renderPool() {
  const host = $('#pool');
  $('#poolCount').textContent = POOL.length;
  host.innerHTML = '';
  for (const url of POOL) {
    const used = SLOTS.filter((s) => (state[s.id] || {}).src === url).length;
    const c = document.createElement('div');
    c.className = 'card';
    const name = url.split('/').pop();
    c.innerHTML = \`<img src="\${url}" alt="" loading="lazy">
      <div class="meta"><span class="dim" data-dim="\${url}">読込中…</span>\${used ? ' ・' + used + '箇所で使用' : ''}<br>\${name}</div>\`;
    c.querySelector('img').addEventListener('load', (ev) => {
      const i = ev.target;
      dims[url] = { w: i.naturalWidth, h: i.naturalHeight };
      const t = document.querySelector('[data-dim="' + CSS.escape(url) + '"]');
      if (t) t.textContent = i.naturalWidth + '×' + i.naturalHeight + '（' + orientOf(url) + '）';
    });
    c.addEventListener('click', () => {
      if (!sel) { alert('先に左側の枠を選んでください'); return; }
      const cur = state[sel] || {};
      state[sel] = Object.assign({}, cur, { src: cur.src === url ? '' : url });
      save(); renderSlots(); renderPool();
    });
    host.appendChild(c);
  }
}

$('#export').addEventListener('click', () => {
  const images = SLOTS.map((s) => {
    const c = state[s.id] || {};
    const { ratio, orient, ...rest } = s;
    return Object.assign(rest, { src: c.src || '', alt: c.alt || '' });
  });
  $('#json').value = JSON.stringify({ _readme: ${JSON.stringify(manifest._readme)}, images }, null, 2);
  $('#out').showModal();
});
$('#copy').addEventListener('click', async () => {
  try { await navigator.clipboard.writeText($('#json').value); $('#copy').textContent = 'コピーしました'; }
  catch { $('#json').select(); }
});
$('#close').addEventListener('click', () => $('#out').close());
$('#clear').addEventListener('click', () => {
  if (!confirm('割り当てをすべて解除します。よろしいですか？')) return;
  state = {}; save(); renderSlots(); renderPool();
});

renderSlots();
renderPool();
</script>
</body>
</html>`);

console.log(`assign.html を生成（${slots.length}枠 / ${pool.length}枚）`);
