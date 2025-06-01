<?php include 'auth.php'; ?>

<?php
$BLANK_BOARD = 'boards/blank';
$BOARDS_DIR = 'boards';
$BANNER_DIR = 'banners';
$HEADER_PATH = 'inc/header.html';

function list_banners($dir) {
    $banners = array_filter(glob("$dir/*"), 'is_file');
    sort($banners);
    return array_map('basename', $banners);
}

function recursive_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst, 0777, true);
    while(false !== ($file = readdir($dir))) {
        if (($file !== '.') && ($file !== '..')) {
            $srcPath = "$src/$file";
            $dstPath = "$dst/$file";
            if (is_dir($srcPath)) {
                recursive_copy($srcPath, $dstPath);
            } else {
                copy($srcPath, $dstPath);
            }
        }
    }
    closedir($dir);
}

function customize_header($path, $board_name, $slogan, $banner_filename) {
    if (!file_exists($path)) return false;

    $content = file_get_contents($path);
    $content = preg_replace('/<title>.*?<\/title>/', "<title>9chan - $board_name</title>", $content);
    $content = preg_replace('/<h1>.*?<\/h1>/', "<h1>9chan - $board_name</h1>", $content);
    $content = preg_replace('/<h2 class="subtitle">.*?<\/h2>/', "<h2 class=\"subtitle\">\"$slogan\"</h2>", $content);
    $content = preg_replace('/<img src="\/banners\/[^"]+"/', "<img src=\"/banners/$banner_filename\"", $content);
    return file_put_contents($path, $content) !== false;
}

$messages = [];
$banners = list_banners($BANNER_DIR);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $board_id = trim($_POST['board_id']);
    $slogan = trim($_POST['slogan']);
    $banner_filename = $_POST['banner'] ?? '';

    $board_name = "/$board_id/";
    $new_board_path = "$BOARDS_DIR/$board_id";

    if (empty($board_id) || empty($slogan) || empty($banner_filename)) {
        $messages[] = "❌ All fields are required.";
    } elseif (file_exists($new_board_path)) {
        $messages[] = "❌ Board '$board_id' already exists!";
    } else {
        recursive_copy($BLANK_BOARD, $new_board_path);
        $header_file = "$new_board_path/$HEADER_PATH";

        if (customize_header($header_file, $board_name, $slogan, $banner_filename)) {
            $messages[] = "✅ Board '$board_name' created with banner '$banner_filename' and slogan \"$slogan\".";
        } else {
            $messages[] = "❌ Header file not found or failed to update at: $header_file.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create New Board</title>
    <meta charset="UTF-8">
</head>
<body>
    <h1>Create New Board</h1>

    <?php foreach ($messages as $msg): ?>
        <p><?php echo htmlspecialchars($msg); ?></p>
    <?php endforeach; ?>

    <form method="post">
        <label>Board folder name (e.g. a):<br>
            <input type="text" name="board_id" required>
        </label><br><br>

        <label>Slogan (e.g. "anime is cool"):<br>
            <input type="text" name="slogan" required>
        </label><br><br>

        <label>Choose Banner:<br>
            <select name="banner" required>
                <option value="">-- Select Banner --</option>
                <?php foreach ($banners as $banner): ?>
                    <option value="<?php echo htmlspecialchars($banner); ?>"><?php echo htmlspecialchars($banner); ?></option>
                <?php endforeach; ?>
            </select>
        </label><br><br>

        <button type="submit">Create Board</button>
    </form>
</body>
</html>
