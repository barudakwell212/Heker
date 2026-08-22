<?php
// Tentukan direktori utama file manager (default: folder saat ini)
$base_dir = __DIR__;
$current_dir = isset($_GET['dir']) ? $_GET['dir'] : '';

// Mencegah Directory Traversal Vulnerability
$real_base = realpath($base_dir);
$requested_dir = realpath($base_dir . '/' . $current_dir);

if ($requested_dir === false || strpos($requested_dir, $real_base) !== 0) {
    $current_dir = '';
    $requested_dir = $real_base;
}

$path = $requested_dir;
$message = '';

// 1. Aksi: Upload File
if (isset($_FILES['upload_file'])) {
    $target_file = $path . '/' . basename($_FILES['upload_file']['name']);
    if (move_uploaded_file($_FILES['upload_file']['tmp_name'], $target_file)) {
        $message = "File berhasil diunggah!";
    } else {
        $message = "Gagal mengunggah file.";
    }
}

// 2. Aksi: Buat Folder
if (isset($_POST['new_folder']) && !empty($_POST['folder_name'])) {
    $new_folder = $path . '/' . trim($_POST['folder_name']);
    if (!file_exists($new_folder)) {
        mkdir($new_folder, 0777, true);
        $message = "Folder berhasil dibuat!";
    } else {
        $message = "Folder sudah ada.";
    }
}

// 3. Aksi: Hapus File / Folder
if (isset($_GET['delete'])) {
    $file_to_delete = $path . '/' . $_GET['delete'];
    if (is_file($file_to_delete)) {
        unlink($file_to_delete);
        $message = "File berhasil dihapus!";
    } elseif (is_dir($file_to_delete)) {
        rmdir($file_to_delete);
        $message = "Folder berhasil dihapus!";
    }
    header("Location: index.php?dir=" . urlencode($current_dir));
    exit;
}

// 4. Aksi: Simpan Edit File
if (isset($_POST['save_file'])) {
    $file_to_edit = $path . '/' . $_POST['filename'];
    if (is_file($file_to_edit)) {
        file_put_contents($file_to_edit, $_POST['content']);
        $message = "File berhasil diperbarui!";
    }
}

// Mengambil daftar file & folder
$items = array_diff(scandir($path), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mini File Manager</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f4f4f9; }
        .container { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #333; color: #fff; }
        .action-btn { text-decoration: none; padding: 5px 10px; color: #fff; border-radius: 3px; font-size: 12px; }
        .btn-edit { background: #ff9800; }
        .btn-delete { background: #f44336; }
        .btn-view { background: #2196F3; }
        .form-group { margin-bottom: 15px; }
        textarea { width: 100%; height: 250px; font-family: monospace; }
        .msg { background: #e7f3fe; color: #31708f; padding: 10px; margin-bottom: 15px; border-left: 6px solid #2196F3; }
    </style>
</head>
<body>

<div class="container">
    <h2>Mini File Manager</h2>
    <p><strong>Lokasi saat ini:</strong> /<?= htmlspecialchars($current_dir) ?></p>

    <?php if ($message): ?>
        <div class="msg"><?= $message ?></div>
    <?php endif; ?>

    <!-- Form Upload File & Buat Folder -->
    <div style="display: flex; gap: 20px;">
        <form method="POST" enctype="multipart/form-data" class="form-group">
            <strong>Upload File:</strong>
            <input type="file" name="upload_file" required>
            <button type="submit">Upload</button>
        </form>

        <form method="POST" class="form-group">
            <strong>Buat Folder:</strong>
            <input type="text" name="folder_name" placeholder="Nama folder..." required>
            <button type="submit" name="new_folder">Buat</button>
        </form>
    </div>

    <!-- Mode Edit File -->
    <?php if (isset($_GET['edit'])): 
        $edit_filename = $_GET['edit'];
        $edit_filepath = $path . '/' . $edit_filename;
        if (is_file($edit_filepath)):
            $content = file_get_contents($edit_filepath);
    ?>
        <h3>Edit File: <?= htmlspecialchars($edit_filename) ?></h3>
        <form method="POST">
            <input type="hidden" name="filename" value="<?= htmlspecialchars($edit_filename) ?>">
            <textarea name="content"><?= htmlspecialchars($content) ?></textarea><br><br>
            <button type="submit" name="save_file">Simpan Perubahan</button>
            <a href="index.php?dir=<?= urlencode($current_dir) ?>">Batal</a>
        </form>
        <hr>
    <?php endif; endif; ?>

    <!-- Tabel Daftar File dan Folder -->
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th>Tipe</th>
                <th>Ukuran</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($current_dir)): ?>
                <tr>
                    <td colspan="4">
                        <?php 
                            $parent_dir = dirname($current_dir);
                            if ($parent_dir === '.') $parent_dir = '';
                        ?>
                        <a href="index.php?dir=<?= urlencode($parent_dir) ?>">📁 <em>.. (Kembali)</em></a>
                    </td>
                </tr>
            <?php endif; ?>

            <?php foreach ($items as $item): 
                $item_path = $path . '/' . $item;
                $is_dir = is_dir($item_path);
            ?>
                <tr>
                    <td>
                        <?php if ($is_dir): ?>
                            📁 <a href="index.php?dir=<?= urlencode(($current_dir ? $current_dir . '/' : '') . $item) ?>"><strong><?= htmlspecialchars($item) ?></strong></a>
                        <?php else: ?>
                            📄 <?= htmlspecialchars($item) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $is_dir ? 'Folder' : 'File' ?></td>
                    <td><?= $is_dir ? '-' : filesize($item_path) . ' bytes' ?></td>
                    <td>
                        <?php if (!$is_dir): ?>
                            <a href="index.php?dir=<?= urlencode($current_dir) ?>&edit=<?= urlencode($item) ?>" class="action-btn btn-edit">Edit</a>
                        <?php endif; ?>
                        <a href="index.php?dir=<?= urlencode($current_dir) ?>&delete=<?= urlencode($item) ?>" class="action-btn btn-delete" onclick="return confirm('Yakin ingin menghapus?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

</body>
</html>
