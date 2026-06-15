# Nostalgic Bulletin Board

位置情報・メモ・画像を紐づけて記録・公開できる Web 掲示板です。  
PHP と PostgreSQL のみで動作し、Docker Compose でローカル検証できます。

## デモ

| 環境 | URL |
|---|---|
| 本番（大学サーバー） | https://muds.gdl.jp/~s2422073/login.php |

> 本番のテスト用アカウント情報はリポジトリには含めません。必要な場合は運用者に問い合わせてください。

## 機能

- ユーザー登録 / ログイン
- 位置情報付き投稿（メモ・画像・公開 / 非公開）
- 投稿一覧・検索・編集・削除
- コメント

## 技術構成

| 項目 | 内容 |
|---|---|
| バックエンド | PHP 8.x |
| DB | PostgreSQL |
| UI | Tailwind CSS（CDN） |
| ローカル | Docker Compose（PHP 8.2-apache + PostgreSQL 16） |

## ローカル起動

```bash
git clone https://github.com/KanshoVector/Nostalgic-bulletin-board.git
cd Nostalgic-bulletin-board
cp .env.example .env
docker compose up -d --build
```

http://localhost:8080/register.php から利用できます。

```bash
docker compose down      # 停止
docker compose down -v   # DB も初期化
```

## 大学サーバーへのデプロイ

### 初回のみ（DB 接続設定）

大学サーバーでは環境変数が使えないため、`db_config_local.php` を **1 回だけ** 配置します。

```bash
cp db_config_local.php.example db_config_local.php
# pass を大学 PostgreSQL のパスワードに書き換える
```

`db_config_local.php` を **`db.php` と同じ階層** にアップロードしてください。  
このファイルは Git 管理外です。**再アップロードしないでください。**

### 更新時にアップロードするファイル

```
db.php
image.php                 # 画像配信（必須）
index.php
login.php
logout.php
register.php
upload.php
view.php
delete.php
includes/bootstrap.php
includes/db_connection.php
includes/layout.php
includes/upload_paths.php
includes/upload_service.php
includes/view_controller.php
templates/post_list.php
templates/post_edit_form.php
templates/comment_section.php
uploads/.htaccess         # 初回または未配置の場合
```

`uploads/` ディレクトリ本体は必要です（書き込み権限必須）。**既存の画像ファイルは削除しないでください。**

### アップロード不要

`Dockerfile`, `docker-compose.yml`, `.env`, `init.sql`, `.github/`, `README.md`

## 画像が表示されない場合

1. DB にファイル名があるか確認する  
   `SELECT id, filename FROM location_diary WHERE filename IS NOT NULL LIMIT 10;`
2. サーバーの `uploads/` に同名ファイルがあるか確認する
3. `image.php` をアップロードしているか確認する（画像 URL は `image.php?f=...` 形式）
4. DB に名前があっても `uploads/` 内の実ファイルが無い場合は復元できません

## ディレクトリ構成

```
├── db.php                     # エントリポイント
├── image.php                  # 画像配信
├── index.php / login.php …    # 画面
├── includes/
│   ├── bootstrap.php          # 認証・共通関数
│   ├── db_connection.php      # DB 接続
│   ├── layout.php             # レイアウト
│   ├── upload_paths.php       # uploads パス解決
│   ├── upload_service.php     # 画像アップロード
│   └── view_controller.php    # 一覧・編集・コメント
├── templates/                 # 部分テンプレート
├── uploads/                   # ユーザー画像（Git 管理外）
└── docker-compose.yml         # ローカル検証用
```

## DB 接続の仕組み

1. `POSTGRES_*` 環境変数がある → Docker 用 DB
2. ない → `db_config_local.php` を読み込み（大学サーバー）
3. どちらもない → エラー

## セキュリティ上の注意

- DB パスワードは `db_config_local.php` にのみ保持し、Git に含めない
- パスワードは `password_hash` で保存（既存の平文パスワードはログイン時に自動移行）
- 画像は `image.php` 経由で MIME タイプを検証して配信

## ライセンス / 利用

学内プロジェクトとして開発されています。再利用・改変は README と LICENSE の範囲で行ってください。
