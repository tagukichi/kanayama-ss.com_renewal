# WordPress スタブ

WordPress を用意せずに `theme/kanayama-ss/` のテンプレートを描画して、
HTMLとデザインを確認するための足場です。

```
php tools/wp-stub/render.php [出力先]     # 既定は theme-preview/
```

- `wp-stub.php` … テーマが呼ぶ WordPress 関数を、最低限の挙動だけで用意したもの
- `render.php` … `inc/starter-content-data.php` の原稿からダミーデータを組み立てて各テンプレートを描画する

## 注意

これは **WordPress の代用品ではありません**。フック、クエリ、書き換え規則、
プラグイン、権限まわりは再現していません。
「テンプレートが意図どおりのHTMLを吐くか」「デザインが崩れていないか」を
見る目的だけに使ってください。本番の動作確認は実際の WordPress で行う必要があります。
