# Nostalgic Bulletin Board

位置情報・メモ・画像を紐づけて記録・公開できる Web 掲示板です。

## 機能

- ユーザー登録 / ログイン
- 位置情報付き投稿（メモ・画像・公開設定）
- 投稿一覧・検索・編集・削除・コメント

## 技術構成

PHP 8.x / PostgreSQL / Tailwind CSS（CDN）  
ローカル検証: Docker Compose（PHP 8.2-apache + PostgreSQL 16）

## ローカル起動

```bash
git clone https://github.com/KanshoVector/Nostalgic-bulletin-board.git
cd Nostalgic-bulletin-board
cp .env.example .env
docker compose up -d --build
```

http://localhost:8080/register.php から利用できます。

## 大学サーバーへのデプロイ

### 初回のみ

```bash
cp db_config_local.php.example db_config_local.php
```

`pass` を大学 DB のパスワードに書き換え、`db.php` と同じ階層に **1 回だけ** アップロードします。  
Git 管理外のため、以降の更新では **上書きしない** でください。  
`.example` だけでは動きません。

### 更新時にアップロードするファイル

```
db.php
image.php
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
uploads/.htaccess
```

- `uploads/` フォルダは必要（書き込み権限必須）。**中身の画像は削除しない**
- アップロード不要: `Dockerfile`, `docker-compose.yml`, `.env`, `init.sql`, `.github/`

### DB 接続

| 環境 | 設定 |
|---|---|
| Docker | `.env` の `POSTGRES_*` |
| 大学サーバー | `db_config_local.php` |

## 画像の仕組み

1. ログイン後に画像を投稿 → `uploads/` に保存、ファイル名を `location_diary.filename` に記録
2. 一覧では `image.php?f=ファイル名` で表示（MIME 検証あり）
3. 誰でも登録して投稿すれば、サーバー上に保存され一覧に表示される

表示されない場合: DB の `filename` と `uploads/` 内の実ファイルが一致しているか、`image.php` が配置されているかを確認してください。

## セキュリティ

- DB 認証情報は `db_config_local.php` のみ（Git に含めない）
- パスワードは bcrypt で保存（旧来の平文はログイン成功時に自動移行）
- SQL はパラメータ化クエリ、出力は HTML エスケープ
- 画像ファイル名は basename のみ受理（パストラバーサル防止）
- `uploads/.htaccess` で PHP 実行を拒否

## ディレクトリ

```
├── db.php / image.php / *.php   # 画面・API
├── includes/                    # 共通ロジック
├── templates/                   # 部分テンプレート
├── uploads/                     # ユーザー画像（Git 管理外）
└── docker-compose.yml           # ローカル用
```
