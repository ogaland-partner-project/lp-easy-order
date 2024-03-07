#bin/bash

echo "[start]:create_apiDocs--------------"
# Apiのドキュメント作成
php artisan scribe:generate
echo "[end]  :create_apiDocs--------------"

echo "[start]:create_appDocs--------------"
# appのドキュメント作成
php phpDocumentor.phar -d ./app -t ./public/appDocs
echo "[end]  :create_appDocs--------------"
