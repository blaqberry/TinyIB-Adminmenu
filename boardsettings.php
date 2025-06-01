<?php include 'auth.php'; ?>

<?php
// settingsedit.php

function find_settings_files($base_dir = 'boards') {
    $matches = [];
    $rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base_dir));
    foreach ($rii as $file) {
        if (!$file->isDir() && $file->getFilename() === 'settings.php') {
            $matches[] = $file->getPathname();
        }
    }
    return $matches;
}

function parse_defines($filepath) {
    $defines = [];
    $lines = file($filepath);
    foreach ($lines as $line) {
        if (preg_match("/define\(\s*'([^']+)'\s*,\s*(.+?)\s*\);/", $line, $matches)) {
            $val = trim($matches[2]);
            // Check if value is a quoted string
            if (preg_match("/^'(.*)'$/s", $val, $m)) {
                // Remove wrapping quotes and un-escape escaped characters
                $val = stripslashes($m[1]);
            }
            // Otherwise keep raw value (e.g., true, false, null, number)
            $defines[$matches[1]] = $val;
        }
    }
    return $defines;
}

function save_defines($filepath, $defines) {
    $header = "<?php\n\n// Board Settings\n// ``````````````\n\n";
    $lines = [];
    foreach ($defines as $k => $v) {
        // If value is a boolean/null or number, output as is
        if (preg_match('/^(true|false|null|\d+)$/i', $v)) {
            $lines[] = "define('$k', $v);";
        } else {
            // Otherwise escape and quote string values
            $lines[] = "define('$k', '" . addslashes($v) . "');";
        }
    }
    $content = $header . implode("\n", $lines) . "\n";
    file_put_contents($filepath, $content);
}

function merge_common_keys($all_defines) {
    $common_keys = null;
    foreach ($all_defines as $defines) {
        if ($common_keys === null) {
            $common_keys = array_keys($defines);
        } else {
            $common_keys = array_intersect($common_keys, array_keys($defines));
        }
    }
    sort($common_keys);
    return $common_keys;
}

$files = find_settings_files();
$all_defines = [];
foreach ($files as $file) {
    $all_defines[$file] = parse_defines($file);
}
$common_keys = merge_common_keys($all_defines);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    foreach ($common_keys as $key) {
        $val = trim($_POST[$key]);
        if (!preg_match('/^(true|false|null|\d+)$/i', $val)) {
            // Save raw input, escaping done in save_defines()
            $val = $val;
        }
        foreach ($all_defines as $file => $defs) {
            $all_defines[$file][$key] = $val;
        }
    }
    foreach ($all_defines as $file => $defs) {
        save_defines($file, $defs);
    }
    echo "<div style='color: green;'>✅ Settings saved to all files.</div>";
}

// Sample values for display
$sample_file = array_key_first($all_defines);
$sample_defines = $all_defines[$sample_file];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Settings Editor</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f0f0f0; }
        form { background: white; padding: 20px; border-radius: 8px; max-width: 600px; margin: auto; }
        input[type=text] { width: 100%; padding: 6px; margin-bottom: 12px; }
        button { padding: 10px 20px; font-size: 16px; }
        h2 { text-align: center; }
    </style>
</head>
<body>
    <form method="POST">
        <h2>🛠 Mass Edit Board Settings</h2>
        <?php foreach ($common_keys as $key): ?>
            <label for="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($key) ?></label>
            <input type="text" name="<?= htmlspecialchars($key) ?>" value="<?= htmlspecialchars($sample_defines[$key]) ?>">
        <?php endforeach; ?>
        <button type="submit" name="save">💾 Save All</button>
    </form>
</body>
</html>
