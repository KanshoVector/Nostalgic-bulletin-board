# 投稿型懐古アプリ (位置情報連動型メディアログ)

位置情報・メモ・画像を紐づけて記録・公開できる Web 掲示板アプリです。

## 稼働 URL

**本番:** https://muds.gdl.jp/~s2422073/login.php

| 項目 | 値 |
|---|---|
| テストアカウント | `r` / `r` |

## 開発の背景

ゼミの要件として大学サーバー（muds.gdl.jp）にホスティングしています。  
企画・設計・バックエンド実装は自主制作として、3 人チームのリードとして自発的に進めたプロジェクトです。

## 技術スタック

| レイヤ | 構成 |
|---|---|
| バックエンド | PHP 8.x（メイン処理） |
| データベース | PostgreSQL（認証・投稿・コメント） |
| フロントエンド | HTML / CSS / JavaScript（素のコード） |
| ローカル検証 | Docker Compose（PHP 8.2-apache + PostgreSQL 16） |

## インフラの工夫

- **Docker Compose 同梱** — 他者が手元で 1 コマンドで動作検証できる構成
- **ハイブリッド DB 接続** — `includes/db_connection.php` が接続先を自動判定
  - 環境変数あり（ローカル Docker）→ `.env` / Compose 注入値を使用
  - 環境変数なし（大学サーバー）→ Git 管理外の `db_config_local.php` を読み込み
- **Credential Leak 対策** — 大学 DB のパスワード等はリポジトリに含めず、サーバー側の除外ファイルにのみ保持
- 大学サーバーへは **PHP ファイルをそのまま上書きアップロード** 可能（`db_config_local.php` は初回配置後そのまま残す）

## 品質保証

GitHub Actions により、コミット・PR 時に全 PHP ファイルの構文チェック（`php -l`）を自動実行します。

## ローカル起動手順

```bash
# 1. リポジトリのクローンと移動
git clone [https://github.com/KanshoVector/Nostalgic-bulletin-board.git](https://github.com/KanshoVector/Nostalgic-bulletin-board.git)
cd Nostalgic-bulletin-board

# 2. 環境変数ファイルの準備
cp .env.example .env
docker compose up -d --build

ブラウザで http://localhost:8080/register.php からユーザー登録後、利用できます。

### 停止

```bash
docker compose down      # コンテナ停止
docker compose down -v   # DB ボリュームも削除（初期化）
```

## ディレクトリ構成（主要ファイル）

```
├── index.php                  # 投稿フォーム（要ログイン）
├── login.php / register.php
├── view.php                   # 投稿一覧・コメント
├── db_config_local.php.example # 大学サーバー用設定テンプレート（Git 管理）
├── includes/
│   └── db_connection.php      # ハイブリッド DB 接続（pg + PDO）
├── init.sql                   # Docker 初回起動時のスキーマ
├── Dockerfile
└── docker-compose.yml
```

## 自分用：大学サーバーへのアップロード

### 1 回限りの初期設定（必須）

大学サーバーは環境変数を設定できない共有環境のため、**Git 管理外の設定ファイルを 1 度だけ手動配置**します。

```bash
# ローカルでテンプレートをコピー
cp db_config_local.php.example db_config_local.php
```

`db_config_local.php` を大学サーバーの **`db.php` と同じ階層（public_html 直下）** にアップロードし、接続情報を記述します。

> **セキュリティ:** このファイルは `.gitignore` により GitHub には送信されません。  
> アプリ本体（`.php`）を何度上書きアップロードしても、`db_config_local.php` はサーバー上に残る限り DB 接続は維持されます。

### 通常アップロード（初回設定後）

Cyberduck 等で以下をアップロード（`.env` / `db_config_local.php` の再アップロードは不要）。

- ルート配下の `.php` ファイル
- `includes/` / `templates/` ディレクトリ
- `uploads/` ディレクトリ（書き込み権限が必要）

**アップロード不要:** `Dockerfile`, `docker-compose.yml`, `.env`, `.github/`, `db_config_local.php.example`
