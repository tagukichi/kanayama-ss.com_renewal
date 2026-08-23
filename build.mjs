/**
 * 金山製作所 プロトタイプ ビルド
 *  src/pages/*.html（本文）＋ src/layout.html ＋ src/partials
 *   → prototype/*.html          … 通常の静的サイト
 *   → prototype/preview.html    … 全ページを1ファイルにまとめた確認用（CSS/JSインライン）
 */
import { readFileSync, writeFileSync, mkdirSync, readdirSync, copyFileSync, rmSync } from 'node:fs';
import { join } from 'node:path';

const SRC = 'src', OUT = 'prototype';
const read = (p) => readFileSync(p, 'utf8');

const layout = read(join(SRC, 'layout.html'));
const header = read(join(SRC, 'partials/header.html'));
const footer = read(join(SRC, 'partials/footer.html'));
const css = read(join(SRC, 'assets/style.css'));
const js = read(join(SRC, 'assets/app.js'));

/** ページ本文の先頭 <!--meta ... --> を読む */
function parse(raw) {
  const m = raw.match(/^<!--meta\s*([\s\S]*?)-->\s*/);
  if (!m) throw new Error('meta ブロックがありません');
  const meta = {};
  for (const line of m[1].trim().split('\n')) {
    const i = line.indexOf(':');
    if (i > 0) meta[line.slice(0, i).trim()] = line.slice(i + 1).trim();
  }
  return { meta, body: raw.slice(m[0].length) };
}

/** 下層ページの共通ページヘッダー＋パンくず */
function pageHead(meta) {
  const crumbs = [`<li><a href="index.html">ホーム</a></li>`];
  if (meta.parent) {
    const [label, href] = meta.parent.split('|');
    crumbs.push(`<li><a href="${href}">${label}</a></li>`);
  }
  crumbs.push(`<li aria-current="page">${meta.title}</li>`);
  return `<section class="phd">
  <div class="phd__media">
    <div class="ph ph--fill ph--dark" data-ph="${meta.ph || '［写真］ページヘッダー背景 ／ 1920×540px'}"></div>
  </div>
  <div class="phd__veil"></div>
  <div class="wrap phd__in">
    <h1 class="phd__en">${meta.en}</h1>
    <p class="phd__ja">${meta.title}</p>
  </div>
</section>
<nav class="bc" aria-label="パンくずリスト"><div class="wrap"><ol>${crumbs.join('')}</ol></div></nav>`;
}

/** ナビの現在地に aria-current を付ける */
function markCurrent(html, slug) {
  return html.replace(
    new RegExp(`(<a class="nav__link" href="${slug}\\.html")`, 'g'),
    '$1 aria-current="page"'
  );
}

rmSync(OUT, { recursive: true, force: true });
mkdirSync(join(OUT, 'assets'), { recursive: true });
copyFileSync(join(SRC, 'assets/style.css'), join(OUT, 'assets/style.css'));
copyFileSync(join(SRC, 'assets/app.js'), join(OUT, 'assets/app.js'));

// ページ順（プレビューの並び順にもなる）
const ORDER = ['index', 'concept', 'solar', 'electric', 'works', 'works-detail',
               'about', 'message', 'recruit', 'interview', 'news', 'contact', 'privacy'];

const files = readdirSync(join(SRC, 'pages')).filter((f) => f.endsWith('.html'));
const pages = files.map((f) => {
  const { meta, body } = parse(read(join(SRC, 'pages', f)));
  meta.slug = meta.slug || f.replace(/\.html$/, '');
  return { meta, body };
}).sort((a, b) => ORDER.indexOf(a.meta.slug) - ORDER.indexOf(b.meta.slug));

const missing = pages.filter((p) => ORDER.indexOf(p.meta.slug) === -1);
if (missing.length) throw new Error('ORDER 未登録: ' + missing.map((p) => p.meta.slug).join(', '));

for (const { meta, body } of pages) {
  const inner = meta.slug === 'index' ? body : pageHead(meta) + '\n' + body;
  const html = layout
    .replace('{{TITLE}}', meta.title)
    .replace('{{DESC}}', meta.desc || '')
    .replace('{{SLUG}}', meta.slug)
    .replace('{{HEADER}}', markCurrent(header, meta.slug))
    .replace('{{BODY}}', inner)
    .replace('{{FOOTER}}', footer);
  writeFileSync(join(OUT, meta.slug + '.html'), html);
}

/* ---------- 1ファイル版プレビュー ---------- */
const tabs = pages.map((p, i) =>
  `<button class="pv__tab" type="button" data-go="${p.meta.slug}"${i === 0 ? ' aria-current="true"' : ''}>${p.meta.title}</button>`
).join('');

const frames = pages.map((p, i) => {
  const inner = p.meta.slug === 'index' ? p.body : pageHead(p.meta) + '\n' + p.body;
  return `<div class="pv__page" id="pv-${p.meta.slug}"${i === 0 ? '' : ' hidden'}>
${markCurrent(header, p.meta.slug)}
<main>${inner}</main>
${footer}
</div>`;
}).join('\n');

const preview = `<!doctype html>
<html lang="ja">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>金山製作所 リニューアル案</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Noto+Sans+JP:wght@400;500;700&family=Noto+Serif+JP:wght@500;600&display=swap">
<style>
${css}

/* ===== プレビュー用シェル（本番テーマには含めない） ===== */
.pv{ position:sticky; top:0; z-index:200; background:#0E120D; color:#fff; border-bottom:2px solid var(--orange); }
.pv__bar{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; padding:10px clamp(14px,3vw,28px); }
.pv__badge{ font-family:var(--en); font-size:11px; font-weight:700; letter-spacing:.16em; background:var(--orange); color:#fff; padding:5px 10px; flex:none; }
.pv__note{ font-size:11.5px; color:rgba(255,255,255,.72); line-height:1.6; }
.pv__tabs{ display:flex; gap:4px; overflow-x:auto; padding:0 clamp(14px,3vw,28px) 10px; scrollbar-width:thin; }
.pv__tab{ flex:none; font-size:12.5px; font-weight:700; padding:8px 13px; color:rgba(255,255,255,.66); border:1px solid rgba(255,255,255,.16); white-space:nowrap; transition:.15s ease; }
.pv__tab:hover{ color:#fff; border-color:rgba(255,255,255,.42); }
.pv__tab[aria-current="true"]{ background:var(--green); border-color:var(--green); color:#fff; }
.pv .hd{ top:0; }
</style>
</head>
<body data-page="preview">
<div class="pv">
  <div class="pv__bar">
    <span class="pv__badge">Prototype</span>
    <span class="pv__note">デザイン検証用のプロトタイプです。<b>写真は未支給のためプレースホルダー、テキストは公開情報をもとにした仮原稿</b>です（実際の原稿・素材で差し替えます）。電話番号・住所は伏せ字です。</span>
  </div>
  <div class="pv__tabs">${tabs}</div>
</div>
${frames}
<script>
(function () {
  var tabs = document.querySelectorAll('.pv__tab');
  tabs.forEach(function (t) {
    t.addEventListener('click', function () {
      var slug = t.dataset.go;
      tabs.forEach(function (x) { x.removeAttribute('aria-current'); });
      t.setAttribute('aria-current', 'true');
      document.querySelectorAll('.pv__page').forEach(function (p) { p.hidden = true; });
      document.getElementById('pv-' + slug).hidden = false;
      window.scrollTo({ top: 0, behavior: 'auto' });
    });
  });
  // プレビュー内リンクはページ切替に変換
  document.addEventListener('click', function (e) {
    var a = e.target.closest('a[href$=".html"]');
    if (!a) return;
    var slug = a.getAttribute('href').replace(/\\.html$/, '');
    var target = document.getElementById('pv-' + slug);
    if (!target) return;
    e.preventDefault();
    var tab = document.querySelector('.pv__tab[data-go="' + slug + '"]');
    if (tab) tab.click();
  });
})();
${js}
</script>
</body>
</html>`;

writeFileSync(join(OUT, 'preview.html'), preview);

/* ---------- Artifact 公開用（doctype/html/head/body を持たない断片） ---------- */
const FONTS = "https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@500;600;700&family=Noto+Sans+JP:wght@400;500;700&family=Noto+Serif+JP:wght@500;600&display=swap";
const artifact = preview
  .slice(preview.indexOf('<body'), preview.lastIndexOf('</body>'))
  .replace(/^<body[^>]*>/, '');
writeFileSync(join(OUT, 'preview.artifact.html'),
`<title>金山製作所 リニューアル案</title>
<style>
@import url('${FONTS}');
${css}

.pv{ position:sticky; top:0; z-index:200; background:#0E120D; color:#fff; border-bottom:2px solid var(--orange); }
.pv__bar{ display:flex; align-items:center; gap:14px; flex-wrap:wrap; padding:10px clamp(14px,3vw,28px); }
.pv__badge{ font-family:var(--en); font-size:11px; font-weight:700; letter-spacing:.16em; background:var(--orange); color:#fff; padding:5px 10px; flex:none; }
.pv__note{ font-size:11.5px; color:rgba(255,255,255,.72); line-height:1.6; }
.pv__tabs{ display:flex; gap:4px; overflow-x:auto; padding:0 clamp(14px,3vw,28px) 10px; }
.pv__tab{ flex:none; font-size:12.5px; font-weight:700; padding:8px 13px; color:rgba(255,255,255,.66); border:1px solid rgba(255,255,255,.16); white-space:nowrap; transition:.15s ease; }
.pv__tab:hover{ color:#fff; border-color:rgba(255,255,255,.42); }
.pv__tab[aria-current="true"]{ background:var(--green); border-color:var(--green); color:#fff; }
</style>
${artifact}`);

console.log('built ' + pages.length + ' pages -> ' + OUT + '/ (+ preview.html, preview.artifact.html)');
