<?php
$root = __DIR__;

$path = isset($_GET['path']) ? realpath($root.'/'.$_GET['path']) : $root;
if (!$path || strpos($path, $root) !== 0) {
    die('Akses ditolak');
}

if (isset($_GET['del'])) {
    $target = realpath($path.'/'.$_GET['del']);
    if ($target && strpos($target, $root) === 0 && is_file($target)) {
        unlink($target);
        header("Location: ?path=" . urlencode(str_replace($root,'',$path)));
        exit;
    }
}

$files = scandir($path);
?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Simple File Manager</title>
<style>
body{background:#111;color:#eee;font-family:monospace}
a{color:#0f0;text-decoration:none}
.box{max-width:900px;margin:20px auto}
.item{padding:6px;border-bottom:1px solid #333}
.del{color:#f33;margin-left:10px}
</style>
</head>
<body>

<div class="box">
<h3>📁 <?= htmlspecialchars(str_replace($root,'',$path) ?: '/') ?></h3>

<?php if ($path !== $root): ?>
<a href="?path=<?= urlencode(dirname(str_replace($root,'',$path))) ?>">⬅ Back</a><br><br>
<?php endif; ?>

<?php foreach ($files as $f): if ($f=='.'||$f=='..') continue; ?>
<div class="item">
<?php if (is_dir($path.'/'.$f)): ?>
📂 <a href="?path=<?= urlencode(trim(str_replace($root,'',$path).'/'.$f,'/')) ?>"><?= $f ?></a>
<?php else: ?>
📄 <?= $f ?>
<a class="del" href="?path=<?= urlencode(str_replace($root,'',$path)) ?>&del=<?= urlencode($f) ?>" onclick="return confirm('Hapus?')">[x]</a>
<?php endif; ?>
</div>
<?php endforeach; ?>
</div>

</body>
</html>