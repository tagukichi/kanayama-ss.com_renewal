# FTPなしで画像URLを集める

テーマに内包された画像（`/wp/wp-content/themes/.../` 配下）はメディアライブラリに出てきません。
ただし**ブラウザで表示できている画像は、必ずURLを持っています**。開発者ツールのコンソールから回収できます。

## 手順

1. 現行サイトのページを開く（例：https://kanayama-ss.com/solar/ ）
2. `F12`（Mac は `⌥⌘I`）で開発者ツール →「コンソール」タブ
3. 下のコードを貼って Enter
4. **クリップボードにURLの一覧がコピーされます。** そのまま貼って渡してください

```js
copy(JSON.stringify([...new Set(
  [
    ...[...document.querySelectorAll('img')].flatMap(i =>
      [i.currentSrc || i.src, ...(i.srcset || '').split(',').map(s => s.trim().split(/\s+/)[0])]),
    ...[...document.querySelectorAll('source')].flatMap(s =>
      (s.srcset || '').split(',').map(x => x.trim().split(/\s+/)[0])),
    ...[...document.querySelectorAll('*')]
      .map(e => getComputedStyle(e).backgroundImage)
      .filter(v => v && v !== 'none')
      .flatMap(v => [...v.matchAll(/url\(["']?(.*?)["']?\)/g)].map(m => m[1])),
  ]
    .filter(u => u && !u.startsWith('data:'))
    .map(u => new URL(u, location.href).href)
    .filter(u => /\.(jpe?g|png|webp|avif|gif|svg)(\?|$)/i.test(u))
    .map(u => u.replace(/-\d+x\d+(?=\.\w+$)/, ''))   // WPのリサイズ版をオリジナルに寄せる
).values()].sort(), null, 1));
console.log('コピーしました');
```

- `<img>` だけでなく **CSS の背景画像**も拾います（メインビジュアルが背景指定のことが多いため）
- WordPress のリサイズ版（`-1024x768.jpg`）は元ファイル名に戻します
- `data:` URI とアイコン類は除外します

## どのページで実行するか

画像が多いページから順に。全部やる必要はありません。

| 優先 | ページ | 現行の画像点数 |
|---|---|---|
| 1 | https://kanayama-ss.com/solar/ | 18 |
| 2 | https://kanayama-ss.com/interview/ | 13 |
| 3 | https://kanayama-ss.com/recruit/ | 11 |
| 4 | https://kanayama-ss.com/message/ | 10 |
| 5 | https://kanayama-ss.com/ | 9 |

## 集めたあと

URLを `src/media-pool.json` の `media` 配列に足して `node build.mjs` すると、
割り当てツール（`assign.html`）にそのまま反映されます。
こちらに貼っていただければ、追加とビルドはこちらで行います。
