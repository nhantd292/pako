<?php
function loadEnv($path)
{
    if (!file_exists($path)) {
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        $line = trim($line);

        // Bỏ qua comment hoặc dòng rỗng
        if ($line === '' || $line[0] === '#' || $line[0] === ';') {
            continue;
        }

        // Tách KEY=VALUE
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $name  = trim($parts[0]);
        $value = trim($parts[1]);

        // Bỏ dấu nháy nếu có
        $value = trim($value, '"\'');

        // ✅ Convert kiểu dữ liệu
        $lower = strtolower($value);
        if ($lower === 'true') {
            $value = true;
        } elseif ($lower === 'false') {
            $value = false;
        } elseif ($lower === 'null') {
            $value = null;
        } elseif (is_numeric($value)) {
            // nếu là số (nguyên hoặc thập phân)
            if (strpos($value, '.') !== false) {
                $value = (float) $value;
            } else {
                $value = (int) $value;
            }
        }

        // Set vào environment nếu chưa có
        if (getenv($name) === false) {
            // putenv chỉ nhận string, nên convert lại để lưu
            putenv($name . '=' . (is_bool($value) ? ($value ? 'true' : 'false') : $value));
            $_ENV[$name]    = $value;
            $_SERVER[$name] = $value;
        }
    }
}
