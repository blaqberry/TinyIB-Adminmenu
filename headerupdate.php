<?php include 'auth.php'; ?>

<?php
function getHeaderFiles() {
    return glob('boards/*/inc/header.html');
}

function extractLinksFromHtml($html, &$fullBlock) {
    if (preg_match('/<div class="links">.*?<\/div>/s', $html, $matches)) {
        $fullBlock = $matches[0];
        preg_match_all('/<a href="([^"]+)">([^<]+)<\/a>/', $fullBlock, $linkMatches, PREG_SET_ORDER);
        return $linkMatches;
    }
    $fullBlock = '';
    return [];
}

function buildLinksHtml($links) {
    $html = "<div class=\"links\">\n";
    foreach ($links as $link) {
        $href = htmlspecialchars($link[0]);
        $label = htmlspecialchars($link[1]);
        $html .= "    <a href=\"$href\">$label</a> \n";
    }
    $html .= "</div>";
    return $html;
}

function updateAllHeaders($newLinksHtml) {
    $files = getHeaderFiles();
    foreach ($files as $path) {
        $content = file_get_contents($path);

        if (preg_match('/<div class="links">.*?<\/div>/is', $content)) {
            // Replace existing block
            $newContent = preg_replace('/<div class="links">.*?<\/div>/is', $newLinksHtml, $content);
        } else {
            // Insert before </body>, or append
            if (preg_match('/<\/body>/i', $content)) {
                $newContent = preg_replace('/<\/body>/i', $newLinksHtml . "\n</body>", $content, 1);
            } else {
                $newContent = $content . "\n" . $newLinksHtml;
            }
        }

        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "✅ Updated: $path<br>";
        } else {
            echo "➡️ No change: $path<br>";
        }
    }
}


$files = getHeaderFiles();
if (!$files) {
    echo "No header files found.";
    exit;
}

$content = file_get_contents($files[0]);
$fullBlock = '';
$links = extractLinksFromHtml($content, $fullBlock);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newLinks = [];
    if (!empty($_POST['href']) && !empty($_POST['label'])) {
        for ($i = 0; $i < count($_POST['href']); $i++) {
            $href = trim($_POST['href'][$i]);
            $label = trim($_POST['label'][$i]);
            if ($href && $label) {
                $newLinks[] = [$href, $label];
            }
        }
    }

    $newLinksHtml = buildLinksHtml($newLinks);
    updateAllHeaders($newLinksHtml);
    echo "<hr><strong>New Links Block:</strong><pre>" . htmlspecialchars($newLinksHtml) . "</pre>";
}
?>

<h2>Edit Board Links</h2>
<form method="post">
    <div id="link-list">
        <?php foreach ($links as $i => $link): ?>
            <div class="link-pair">
                <input name="label[]" value="<?= htmlspecialchars($link[2]) ?>" placeholder="Label" required>
                <input name="href[]" value="<?= htmlspecialchars($link[1]) ?>" placeholder="Href" required>
                <button type="button" onclick="this.parentElement.remove()">❌</button>
            </div>
        <?php endforeach; ?>
    </div>
    <button type="button" onclick="addLink()">➕ Add Link</button><br><br>
    <input type="submit" value="Save and Apply">
</form>

<script>
function addLink() {
    const div = document.createElement('div');
    div.className = 'link-pair';
    div.innerHTML = `
        <input name="label[]" placeholder="Label" required>
        <input name="href[]" placeholder="Href" required>
        <button type="button" onclick="this.parentElement.remove()">❌</button>
    `;
    document.getElementById('link-list').appendChild(div);
}
</script>

<style>
.link-pair {
    margin-bottom: 8px;
}
input {
    padding: 4px;
    margin-right: 5px;
}
</style>
