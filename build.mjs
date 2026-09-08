/**
 * 金山製作所 プロトタイプ ビルド
 *  src/pages/*.html（本文）＋ src/layout.html ＋ src/partials
 *   → prototype/*.html          … 通常の静的サイト
 *   → prototype/preview.html    … 全ページを1ファイルにまとめた確認用（CSS/JSインライン）
 */
import { readFileSync, writeFileSync, mkdirSync, readdirSync, cpSync, rmSync, existsSync, statSync } from 'node:fs';
import { join } from 'node:path';

const SRC = 'src', OUT = 'prototype';
const read = (p) => readFileSync(p, 'utf8');

const layout = read(join(SRC, 'layout.html'));
const header = read(join(SRC, 'partials/header.html'));
const footer = read(join(SRC, 'partials/footer.html'));
const css = read(join(SRC, 'assets/style.css'));
const gateJs = read(join(SRC, 'assets/gate.js'));
const manifest = JSON.parse(read(join(SRC, 'images.json')));
const slider = JSON.parse(read(join(SRC, 'slides.json')));
const byId = new Map(manifest.images.map((e) => [e.id, e]));

/**
 * src/assets/img/<ページのスラッグ>/ に置かれた写真を、
 * そのページの「まだ空いている枠」へファイル名順で自動的に流し込む。
 * → 画像を追加して push するだけで反映される（images.json を触らなくてよい）
 */
const IMG_DIR = join(SRC, 'assets/img');
if (existsSync(IMG_DIR)) {
  for (const page of readdirSync(IMG_DIR)) {
    const dir = join(IMG_DIR, page);
    if (!statSync(dir).isDirectory()) continue;
    const files = readdirSync(dir).filter((f) => /\.(jpe?g|png|webp|avif)$/i.test(f)).sort();
    if (!files.length) continue;
    const slots = manifest.images.filter((e) => e.page === page && !e.src);
    if (!slots.length) {
      console.log(`  img/${page}/: ${files.length}枚あるが空き枠なし（images.json で明示的に割り当ててください）`);
      continue;
    }
    files.forEach((f, i) => { if (slots[i]) slots[i].src = `assets/img/${page}/${f}`; });
    const used = Math.min(files.length, slots.length);
    console.log(`  img/${page}/: ${used}枚を自動割り当て` +
      (files.length > slots.length ? `（${files.length - slots.length}枚は空き枠がないため未使用）` : ''));
  }
}
const js = read(join(SRC, 'assets/app.js'));

/**
 * FVスライダーを描画する。
 *  slides.json の定義から <div class="fv__slide"> を並べ、操作UIも同時に生成する。
 *  → WordPress ではこの配列をカスタム投稿／ACFの繰り返しフィールドに置き換える
 */
function renderSlider(html) {
  const slides = slider.slides.map((sl, i) => {
    const missing = !/^https?:/.test(sl.src) && !existsSync(join(SRC, sl.src));
    const style = [sl.bg ? `background:${sl.bg}` : '', sl.fit ? `--fit:${sl.fit}` : '']
      .filter(Boolean).join(';');
    const media = missing
      ? `<div class="fv__ph"><span>［画像］${sl.alt || sl.id}<br><small>${sl.src} を配置すると表示されます</small></span></div>`
      // 画面幅で画像を出し分けると1枚目と2枚目の見え方がずれるため、どの幅でも同じ画像を使う
      : `<img class="fv__photo" src="${sl.src}" alt="${sl.alt}" style="object-position:${sl.pos || '50% 50%'}"${i ? ' loading="lazy"' : ' fetchpriority="high"'} decoding="async">`;
    return `      <div class="fv__slide" data-overlay="${sl.overlay !== false}"${style ? ` style="${style}"` : ''}${i === 0 ? ' data-active' : ''} aria-hidden="${i !== 0}">
        ${media}
        <span class="fv__wash" aria-hidden="true"></span>
        <span class="fv__flare" aria-hidden="true"></span>
      </div>`;
  }).join('\n');

  // インジケーターはスライダーの中（下端中央）に置く
  const pager = `      <div class="fv__pager"><div class="fv__pins" role="tablist" aria-label="スライドの選択">`
    + slider.slides.map((sl, i) =>
        `<button type="button" class="fv__pin" role="tab" data-go="${i}" aria-label="${i + 1}枚目を表示"${i === 0 ? ' aria-current="true"' : ''}></button>`
      ).join('')
    + `</div></div>`;

  return html
    .replace('<div class="fv__media" data-slider></div>',
      `<div class="fv__media" data-slider data-interval="${slider.interval}" role="group" aria-roledescription="カルーセル" aria-label="メインビジュアル">\n${slides}\n${pager}\n    </div>`);
}

/**
 * 画像スロットを描画する。
 *  images.json に src があれば <img>、無ければプレースホルダーのまま。
 *  → URL を1行入れるだけで実画像に切り替わる
 */
function renderSlots(html) {
  return html.replace(/<div class="([^"]*)"\s+data-img="([^"]+)"\s*><\/div>/g, (_m, cls, id) => {
    const e = byId.get(id);
    if (!e) throw new Error(`images.json に画像スロット "${id}" の定義がありません`);
    // ローカル参照は実ファイルが置かれるまでプレースホルダーのまま（壊れ画像を出さない）
    const pending = e.src && !/^https?:/.test(e.src) && !existsSync(join(SRC, e.src));
    if (!e.src || pending) {
      const label = pending
        ? `［${e.kind}］${e.label} ／ ${e.src} を配置すると表示されます`
        : `［${e.kind}］${e.label}${e.size ? ' ／ ' + e.size : ''}`;
      return `<div class="${cls}" data-ph="${label}"></div>`;
    }
    const mods = cls.split(/\s+/)
      .filter((c) => c.startsWith('ph--') && c !== 'ph--dark')
      .map((c) => 'media--' + c.slice(4));
    const isBackdrop = cls.includes('ph--fill');
    // 見出しの背後に敷く写真は装飾扱い（alt=""）。本文中の写真は必ず alt を持たせる
    const alt = e.alt ? e.alt : (isBackdrop ? '' : e.label.split(' ／ ')[0]);
    const load = e.eager ? 'loading="eager" fetchpriority="high"' : 'loading="lazy"';
    // pos が指定されていれば表示位置を上書きする（切り取り位置の微調整用）
    const pos = e.pos ? ` style="object-position:${e.pos}"` : '';
    return `<img class="media ${mods.join(' ')}" src="${e.src}" alt="${alt.replace(/"/g, '&quot;')}" ${load} decoding="async"${pos}>`;
  });
}

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
  const head = meta.head === 'light'
    ? `<section class="phd phd--light">
  <span class="phd__diag" aria-hidden="true"></span>
  <div class="wrap phd__in">
    <h1 class="phd__en">${meta.en}</h1>
    <p class="phd__ja">${meta.title}</p>
  </div>
</section>`
    : `<section class="phd">
  <div class="phd__media">
    <div class="ph ph--fill ph--dark" data-img="${meta.slug}-header"></div>
  </div>
  <div class="phd__veil"></div>
  <div class="wrap phd__in">
    <h1 class="phd__en">${meta.en}</h1>
    <p class="phd__ja">${meta.title}</p>
  </div>
</section>`;
  return `${head}
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
mkdirSync(OUT, { recursive: true });
// src/assets をまるごと複製（style.css / app.js / img/ …）
cpSync(join(SRC, 'assets'), join(OUT, 'assets'), { recursive: true });

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
  const inner = renderSlider(renderSlots(meta.slug === 'index' ? body : pageHead(meta) + '\n' + body));
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
  const inner = renderSlider(renderSlots(p.meta.slug === 'index' ? p.body : pageHead(p.meta) + '\n' + p.body));
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
<script>${gateJs}</script>
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

// プロトタイプは検索エンジンに拾わせない（本番テーマ化の際に削除する）
writeFileSync(join(OUT, 'robots.txt'), 'User-agent: *\nDisallow: /\n');

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

const filled = manifest.images.filter((e) => e.src && (/^https?:/.test(e.src) || existsSync(join(SRC, e.src)))).length;
console.log(`画像スロット: ${filled}/${manifest.images.length} 件にURL設定済み`);
await import('./make-assigner.mjs');
console.log('built ' + pages.length + ' pages -> ' + OUT + '/ (+ preview.html, preview.artifact.html)');
