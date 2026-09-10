/**
 * WordPressテーマのアセットを src/ から生成する。
 *   src/assets/style.css → theme/kanayama-ss/style.css（テーマヘッダ＋WP標準クラスを付与）
 *   src/assets/app.js    → theme/kanayama-ss/assets/js/app.js
 *   src/assets/img/*     → theme/kanayama-ss/assets/img/（既定画像として同梱するものだけ）
 * PHPテンプレートは手書きなので、このスクリプトは触らない。
 */
import { readFileSync, writeFileSync, copyFileSync, mkdirSync, existsSync } from 'node:fs';
import { join, dirname } from 'node:path';

const SRC = 'src';
const THEME = 'theme/kanayama-ss';
const VERSION = '1.0.0';

const HEADER = `/*
Theme Name: Kanayama Seisakusho
Theme URI: https://kanayama-ss.com/
Author: エイトフィールズ株式会社
Author URI: https://eight-fields.co.jp/
Description: 有限会社金山製作所（太陽光発電・蓄電池工事／電気・水道工事）のコーポレートサイト用クラシックテーマ。トップのFVスライダー、工事実績、社員紹介、対応する工事、お知らせを管理画面から更新できます。
Version: ${VERSION}
Requires at least: 6.0
Requires PHP: 7.4
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: kanayama
Tags: custom-menu, featured-images, translation-ready, threaded-comments
*/

/* ===========================================================
   このファイルは build-theme.mjs が src/assets/style.css から
   生成しています。直接編集せず、src 側を直して再生成してください。
   =========================================================== */
`;

/* WordPressがテーマに要求する標準クラス。プロトタイプには不要だったもの。 */
const WP_CORE = `

/* ===========================================================
   WordPress 標準クラス
   投稿本文（.article 内）で使われる、コアが出力するクラス
   =========================================================== */
.screen-reader-text{
  position:absolute; width:1px; height:1px; margin:-1px; padding:0;
  overflow:hidden; clip:rect(0 0 0 0); clip-path:inset(50%); white-space:nowrap; border:0;
}
.screen-reader-text:focus{
  position:fixed; top:12px; left:12px; z-index:200; width:auto; height:auto;
  clip:auto; clip-path:none; padding:12px 20px; background:var(--white);
  color:var(--green-deep); font-weight:700; border-radius:8px;
  box-shadow:0 6px 24px rgba(11,74,12,.18);
}
.alignleft{ float:left; margin:.4em 1.6em 1em 0; }
.alignright{ float:right; margin:.4em 0 1em 1.6em; }
.aligncenter{ display:block; margin-inline:auto; }
.alignnone{ margin:1em 0; }
.alignwide{ margin-inline:calc(50% - 50vw); max-width:100vw; }
.alignfull{ margin-inline:calc(50% - 50vw); max-width:100vw; }
.article > .alignwide, .article > .alignfull{ margin-inline:0; max-width:100%; }
.wp-caption{ max-width:100%; margin-bottom:1.4em; }
.wp-caption img{ display:block; width:100%; height:auto; border-radius:12px; }
.wp-caption-text, .gallery-caption, figcaption{
  margin-top:8px; font-size:13px; line-height:1.7; color:var(--muted);
}
.sticky, .bypostauthor{ /* コアのテーマチェック用 */ }
.article img, .article iframe, .article video{ max-width:100%; height:auto; }
.article img{ border-radius:12px; }
.article blockquote{
  margin:1.6em 0; padding:2px 0 2px 20px; border-left:3px solid var(--green);
  color:var(--ink-2);
}
.article ol{ margin:1em 0 1em 1.3em; padding:0; }
.article ol li{ margin-bottom:.5em; line-height:1.95; }
/* .tbl は独自の体裁を持っているので、素の表だけを対象にする */
.article table:not(.tbl){ width:100%; border-collapse:collapse; margin:1.4em 0; }
.article table:not(.tbl) th, .article table:not(.tbl) td{ padding:12px 14px; border:1px solid var(--line); text-align:left; }
.article table:not(.tbl) th{ background:var(--soft); font-weight:700; }
.article pre{ overflow-x:auto; padding:16px; background:var(--soft); border-radius:12px; }
.article code{ padding:.15em .45em; background:var(--soft-2); border-radius:5px; font-size:.92em; }

/* ページ送り（お知らせ・工事実績の一覧） */
.pager{ display:flex; justify-content:center; gap:8px; flex-wrap:wrap; margin-top:52px; }
.pager .page-numbers{
  display:inline-flex; align-items:center; justify-content:center;
  min-width:42px; height:42px; padding:0 12px; border-radius:10px;
  border:1px solid var(--line-strong); background:var(--white);
  font-family:var(--en); font-size:16px; font-weight:600; color:var(--ink-2);
  text-decoration:none; transition:background .2s ease, color .2s ease, border-color .2s ease;
}
.pager .page-numbers:hover{ border-color:var(--green); color:var(--green); }
.pager .page-numbers.current{ background:var(--green); border-color:var(--green); color:var(--white); }
.pager .dots{ border:0; background:transparent; }

/* 検索結果・記事が0件のとき */
.empty{ padding:56px 0; text-align:center; color:var(--muted); }

/* ===========================================================
   Contact Form 7 を入れたときの見た目あわせ
   ショートコードが出す素のマークアップでも .field と同じ体裁になる
   =========================================================== */
.form .wpcf7-form-control-wrap{ display:block; }
.form > p{ display:grid; gap:9px; margin:0; }
.form label{ display:grid; gap:9px; font-size:14px; font-weight:700; }
.form input[type=\"text\"], .form input[type=\"email\"], .form input[type=\"tel\"],
.form input[type=\"url\"], .form input[type=\"date\"], .form input[type=\"number\"],
.form select, .form textarea{
  font:inherit; font-size:15px; width:100%; padding:14px 16px;
  background:var(--soft); border:1px solid var(--line-strong); color:var(--ink);
  transition:border-color .18s ease, background .18s ease;
}
.form input:focus, .form select:focus, .form textarea:focus{ background:#fff; border-color:var(--green); }
.form textarea{ min-height:190px; resize:vertical; }
.form .wpcf7-list-item{ margin:0 18px 0 0; }
.form .wpcf7-not-valid-tip{ font-size:13px; font-weight:700; color:var(--orange-deep); }
.form .wpcf7-response-output{
  margin:0; padding:14px 18px; font-size:14px; line-height:1.8;
  border:1px solid var(--line-strong); background:var(--soft);
}
.form input[type=\"submit\"], .form button[type=\"submit\"]{
  justify-self:start; width:auto;
  display:inline-flex; align-items:center; gap:12px; padding:17px 34px;
  font-family:var(--jp); font-size:15px; font-weight:700; letter-spacing:.06em;
  background:var(--orange); color:#fff; border:1px solid var(--orange); cursor:pointer;
  transition:background .2s ease, border-color .2s ease;
}
.form input[type=\"submit\"]:hover, .form button[type=\"submit\"]:hover{
  background:var(--orange-deep); border-color:var(--orange-deep);
}
`;

/* プロトタイプ専用のログインゲートはテーマには不要なので取り除く */
function stripGate(css) {
  const start = css.indexOf('/* ===========================================================\n   ログインゲート');
  if (start === -1) return css.replace(/^.*\.gate.*$\n?/gm, '');
  const end = css.indexOf('/* ===========================================================', start + 10);
  return end === -1 ? css.slice(0, start) : css.slice(0, start) + css.slice(end);
}

const css = stripGate(readFileSync(join(SRC, 'assets/style.css'), 'utf8'));
writeFileSync(join(THEME, 'style.css'), HEADER + '\n' + css.trimStart() + WP_CORE);

/* app.js はそのまま。テーマ側でも同じDOMを出力しているため無改造で動く */
mkdirSync(join(THEME, 'assets/js'), { recursive: true });
writeFileSync(join(THEME, 'assets/js/app.js'), readFileSync(join(SRC, 'assets/app.js'), 'utf8'));

/* 既定画像（管理画面で未設定のときに使う保険）だけをテーマに同梱する */
const DEFAULTS = [
  ['assets/img/bg_01.jpg', 'assets/img/default-header.jpg'],
  ['assets/img/slide_01_full.jpg', 'assets/img/default-fv.jpg'],
];
let copied = 0;
for (const [from, to] of DEFAULTS) {
  const src = join(SRC, from);
  if (!existsSync(src)) { console.log(`  ${from} が無いのでスキップ`); continue; }
  mkdirSync(dirname(join(THEME, to)), { recursive: true });
  copyFileSync(src, join(THEME, to));
  copied++;
}

console.log(`テーマのアセットを生成: style.css / assets/js/app.js / 既定画像${copied}点`);
