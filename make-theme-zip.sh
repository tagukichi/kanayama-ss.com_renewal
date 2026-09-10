#!/bin/sh
# WordPress管理画面からアップロードできる ZIP を作る。
# 使い方: sh make-theme-zip.sh  → dist/kanayama-ss.zip
set -e
node build-theme.mjs
mkdir -p dist
rm -f dist/kanayama-ss.zip
cd theme
zip -rq ../dist/kanayama-ss.zip kanayama-ss -x '*.DS_Store'
cd ..
echo "作成: dist/kanayama-ss.zip ($(du -h dist/kanayama-ss.zip | cut -f1))"
