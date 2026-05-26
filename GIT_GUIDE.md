# Hướng dẫn Đẩy Code Nhanh lên Git

Để thực hiện các thao tác `git add .`, `git commit` và `git push` một cách nhanh chóng, bạn có thể sử dụng file script đã được tạo sẵn.

## 1. Chuẩn bị (Chỉ thực hiện lần đầu)

Cấp quyền thực thi cho file script:
```bash
chmod +x git-push.sh
```

## 2. Cách sử dụng

Mỗi khi muốn đẩy code lên, bạn chỉ cần chạy lệnh sau trong terminal:

```bash
   ./git-push.sh "Nội dung commit của bạn"
```

### Ví dụ:
```bash
./git-push.sh "Cập nhật Docker và cấu hình Email"
```

---

## 3. Các bước thực hiện thủ công (Nếu không dùng script)

Nếu bạn muốn gõ lệnh trực tiếp:

1. Thêm tất cả thay đổi:
   ```bash
   git add .
   ```
2. Tạo bản cam kết:
   ```bash
   git commit -m "Nội dung commit"
   ```
3. Đẩy lên server:
   ```bash
   git push
   ```
