# WordPressテーマ「Kanayama Seisakusho」

プロトタイプ（`prototype/`）と同じデザインの、WordPress クラシックテーマです。
本体は `theme/kanayama-ss/`。プラグインなしで動きます。

---

## 導入手順

1. `theme/kanayama-ss` を ZIP にする（リポジトリ直下で `sh make-theme-zip.sh` を実行すると `dist/kanayama-ss.zip` ができます）
2. WordPress管理画面 → 外観 → テーマ → 新規追加 → テーマのアップロード
3. 有効化する
4. **外観 → 初期コンテンツ** を開き、「初期コンテンツを作成する」を押す
5. 設定 → パーマリンク を開いて「変更を保存」（`/works/` などのURLを有効にするため）

4 を実行すると、固定ページ10件・FVスライド2件・工事実績6件・社員紹介6件・
対応する工事10件・特長15件・お知らせ5件と、メニュー4件が作られます。
すでに同じスラッグのものがあれば作り直さないので、何度押しても安全です。

> 現行の kanayama-ss.com の WordPress に入れる場合は、4 の画面で
> 「テーマに同梱している写真をメディアライブラリに取り込む」のチェックを外してください。
> 既存のメディアと重複しません。画像は各ページ・各投稿で手動で割り当てます。

---

## 管理画面のどこで何を直すか

| 直したいもの | 場所 |
|---|---|
| 電話番号・住所・FAX・営業時間・登録番号 | 外観 → カスタマイズ → 会社情報 |
| トップのキャッチコピー・リード文・FVの高さ比率 | 外観 → カスタマイズ → トップ：ファーストビュー |
| 25,000棟などの数字 | 外観 → カスタマイズ → トップ：実績の数字 |
| 全ページ下部のお問い合わせ帯の文言 | 外観 → カスタマイズ → 全ページ共通：お問い合わせ帯 |
| 下層ページの既定ヘッダー画像・一覧のヘッダー画像 | 外観 → カスタマイズ → 会社情報 |
| FVスライドの画像・並び順 | FVスライド |
| 工事実績 | 工事実績 |
| 社員紹介 | 社員紹介 |
| 事業案内ページのカード | 対応する工事 |
| 選ばれる理由／工事の流れ／メリット／働く環境 | 特長・ステップ |
| お知らせ | 投稿 |
| 各ページの本文・英字見出し・ヘッダー写真 | 固定ページの編集画面 |
| グローバルナビ・フッターメニュー | 外観 → メニュー |

### 文字の書き方の約束

- タイトルや見出しの **`|`（縦棒）は改行**になります。例：`つくる、貯める、|使いきる。`
- FVのキャッチコピーだけ、**`*〜*` で囲むと緑色**になります。例：`屋根の上に、|*確かな仕事*を。`
- 社員紹介のQ&Aと、太陽光ページのFAQは **`Q. 質問` / `A. 回答`** の形で1行ずつ書きます。1組ごとに空行で区切ります。
- 募集要項の表は **`項目 | 内容`** の形で1行ずつ。内容の中の `/` は改行になります。
- 1日のスケジュールは **`6:30 出社`** のように時刻と内容を空白で区切ります。時刻のない文章を書いた場合は、そのまま文章として表示します。

---

## ファイル構成

```
theme/kanayama-ss/
├── style.css                    テーマヘッダ＋全スタイル（build-theme.mjs が生成）
├── functions.php                読み込み口
├── inc/
│   ├── setup.php                テーマサポート・アセット・メニュー登録・一覧の件数
│   ├── nav-walker.php           グローバルナビの階層出力と、未設定時の代替表示
│   ├── post-types.php           カスタム投稿タイプ5種とタクソノミー3種
│   ├── meta-boxes.php           カスタムフィールド（定義は kanayama_field_schema）
│   ├── customizer.php           会社情報・FV・実績・CTAの設定
│   ├── template-tags.php        表示用の関数
│   ├── starter-content.php      初期コンテンツの投入処理
│   └── starter-content-data.php 初期コンテンツの中身（原稿）
├── header.php / footer.php
├── front-page.php               トップ
├── page.php                     固定ページの既定
├── page-concept.php             私たちの想い
├── page-solar.php               太陽光発電・蓄電
├── page-electric.php            電気・水道工事
├── page-about.php               会社案内
├── page-recruit.php             採用情報
├── page-message.php             代表メッセージ
├── page-contact.php             お問い合わせ
├── archive-kn_work.php          工事実績 一覧
├── taxonomy-kn_work_cat.php     工事区分での絞り込み
├── single-kn_work.php           工事実績 詳細
├── archive-kn_interview.php     社員紹介 一覧
├── single-kn_interview.php      社員紹介 詳細
├── home.php / single.php / archive.php   お知らせ
├── search.php / searchform.php / 404.php / index.php
├── template-parts/
│   ├── logo-mark.php            ロゴSVG
│   ├── page-hero.php            パンくず下の大きな画像
│   └── interview-card.php       社員紹介カード
└── assets/
    ├── js/app.js                スライダー・カウントアップ・ナビ（プロトタイプと同じ）
    ├── js/admin.js              管理画面の画像選択
    └── img/
        ├── default-header.jpg / default-fv.jpg   画像未設定時の保険
        └── starter/                              初期コンテンツ用の写真
```

`page-*.php` はスラッグが一致すれば自動で適用されます。
別のスラッグで作りたい場合は、ページ編集画面の「テンプレート」から選んでください。

---

## カスタム投稿タイプ

| 投稿タイプ | 表示名 | URL | 用途 |
|---|---|---|---|
| `kn_slide` | FVスライド | 一覧なし | トップのスライダー |
| `kn_work` | 工事実績 | `/works/` | 施工事例。工事区分（`kn_work_cat`）で分類 |
| `kn_interview` | 社員紹介 | `/interview/` | 社員インタビュー |
| `kn_service` | 対応する工事 | 一覧なし | 事業区分（`kn_service_cat`）= `solar` / `electric` |
| `kn_feature` | 特長・ステップ | 一覧なし | グループ（`kn_feature_group`）= `reason` / `flow` / `merit` / `environment` |

---

## お問い合わせフォーム

フォーム本体はテーマに含めていません。お問い合わせページの**本文に
Contact Form 7 などのショートコードを貼る**と、そこに `.form` の体裁で表示されます。
CF7 の素のマークアップに合わせたスタイルは `style.css` に入れてあるので、
特別な指定なしで他ページと同じ見た目になります。

プロトタイプと同じ項目を作る場合の CF7 のフォーム定義:

```
<div class="field">
  <label class="field__l" for="f-type">お問い合わせ種別 <span class="req">必須</span></label>
  [select* type "太陽光発電・蓄電池について" "電気・水道工事について" "施工のご依頼（販売店・元請け様）" "採用へのご応募" "その他"]
</div>
<div class="field">
  <label class="field__l" for="f-name">お名前 <span class="req">必須</span></label>
  [text* your-name autocomplete:name placeholder "金山 太郎"]
</div>
<div class="field">
  <label class="field__l">会社名 <span class="opt">任意</span></label>
  [text company autocomplete:organization]
</div>
<div class="field">
  <label class="field__l">メールアドレス <span class="req">必須</span></label>
  [email* your-email autocomplete:email placeholder "example@example.com"]
</div>
<div class="field">
  <label class="field__l">電話番号 <span class="opt">任意</span></label>
  [tel tel autocomplete:tel]
</div>
<div class="field">
  <label class="field__l">工事予定のご住所 <span class="opt">任意</span></label>
  [text addr placeholder "東京都墨田区"]
</div>
<div class="field">
  <label class="field__l">お問い合わせ内容 <span class="req">必須</span></label>
  [textarea* your-message placeholder "築年数、屋根の種類、ご検討中の内容などをお書きください。"]
</div>
<p style="font-size:13px;color:var(--muted)">プライバシーポリシーに同意のうえ送信してください。</p>
[submit "入力内容を確認する"]
```

---

## 開発者向け

### スタイルの直し方

`theme/kanayama-ss/style.css` は**直接編集しないでください**。
`src/assets/style.css` を直してから、リポジトリ直下で

```
node build-theme.mjs
```

を実行すると再生成されます。プロトタイプとテーマで見た目がずれないようにするためです。
`assets/js/app.js` も同じく `src/assets/app.js` から複製されます。

### WordPress無しでテンプレートを確認する

`tools/wp-stub/` に、WordPressの関数を最低限だけ用意した足場があります。

```
php tools/wp-stub/render.php          # theme-preview/*.html を書き出す
```

デザインの目視確認と、テンプレートの取りこぼし検出のためのものです。
フックもクエリも本物ではないので、**これが通っても本番で動く保証にはなりません**。

---

## 制限・引き継ぎ事項

- **お問い合わせフォームはプラグイン前提**です（上記参照）。
- **写真が未設定の枠**は、プロトタイプと同じくグレーの枠に説明文が出ます。壊れ画像にはなりません。
  現時点で未設定なのは、対応する工事のうち **蓄電池／ソーラーカーポート／HEMS／オール電化／オフグリッド／
  水道・給排水／エアコン設置・移設／給湯器の交換** の8件と、工事実績6件のうち3件です。
  これらは現行サイトのメディアにある写真なので、現行サイトに入れる場合はそこから割り当ててください。
- **代表メッセージの本文**は仮の原稿のままです。実物の原稿に差し替えてください。
- **看板犬JAMTEの紹介文**は、画像から書き起こしたものです。内容の確認をお願いします。
- テーマの動作は PHP 構文チェックと上記の描画スタブまでで確認しています。
  **実際の WordPress 上での通し確認はまだです。** 導入後、一度ひととおり見てください。
