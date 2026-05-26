#!/bin/bash

# Kiểm tra nếu có nội dung commit
if [ -z "$1" ]
then
    echo "Vui lòng nhập nội dung commit!"
    echo "Sử dụng: ./git-push.sh \"Nội dung commit của bạn\""
    exit 1
fi

# Thực hiện các bước git
echo "--- Đang thực hiện git add . ---"
git add .

echo "--- Đang thực hiện git commit ---"
git commit -m "$1"

echo "--- Đang thực hiện git push ---"
git push

echo "--- Hoàn thành! ---"
