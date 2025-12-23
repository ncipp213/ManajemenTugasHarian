<?php
require_once 'koneksi.php';

// PROSES FORM TAMBAH TUGAS 
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['tambah'])) {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $deskripsi = mysqli_real_escape_string($koneksi, $_POST['deskripsi']);
    $deadline = $_POST['deadline'];
    
    if (!empty($judul)) {
        $query = "INSERT INTO tugas (judul, deskripsi, deadline) VALUES ('$judul', '$deskripsi', '$deadline')";
        
        if (mysqli_query($koneksi, $query)) {
            $message = "✓ Tugas berhasil ditambahkan!";
            $message_type = "success";
        } else {
            $message = "✗ Gagal menambahkan tugas: " . mysqli_error($koneksi);
            $message_type = "error";
        }
    } else {
        $message = "✗ Judul tugas tidak boleh kosong!";
        $message_type = "error";
    }
}

// PROSES UBAH STATUS
if (isset($_GET['ubah_status'])) {
    $id = $_GET['id'];
    $status_baru = $_GET['status'] == 'Pending' ? 'Selesai' : 'Pending';
    
    $query = "UPDATE tugas SET status = '$status_baru' WHERE id = $id";
    mysqli_query($koneksi, $query);
    
    header("Location: index.php");
    exit();
}


// PROSES HAPUS TUGAS
if (isset($_GET['hapus'])) {
    $id = $_GET['id'];
    $query = "DELETE FROM tugas WHERE id = $id";
    mysqli_query($koneksi, $query);
    
    header("Location: index.php");
    exit();
}

// AMBIL DATA TUGAS
$query_tugas = "SELECT * FROM tugas ORDER BY deadline ASC";
$result_tugas = mysqli_query($koneksi, $query_tugas);

// Hitung statistik
$total_tugas = mysqli_num_rows($result_tugas);

$query_selesai = "SELECT COUNT(*) as jumlah FROM tugas WHERE status = 'Selesai'";
$result_selesai = mysqli_query($koneksi, $query_selesai);
$row_selesai = mysqli_fetch_assoc($result_selesai);
$tugas_selesai = $row_selesai['jumlah'];

$tugas_pending = $total_tugas - $tugas_selesai;

// Reset pointer result untuk digunakan di loop
mysqli_data_seek($result_tugas, 0);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Tugas Harian</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <!-- HEADER -->
        <header>
            <h1>📋 Manajemen Tugas Harian</h1>
            <p>Atur dan pantau tugas harian Anda dengan mudah</p>
        </header>
        
        <!-- NAVIGASI -->
        <nav>
            <ul class="nav-links">
                <li><a href="#dashboard">Dashboard</a></li>
                <li><a href="#tambah-tugas">Tambah Tugas</a></li>
                <li><a href="#daftar-tugas">Daftar Tugas</a></li>
                <li><a href="#pencarian">Pencarian</a></li>
            </ul>
        </nav>
        
        <!-- PESAN -->
        <?php if (!empty($message)): ?>
            <div class="message <?php echo isset($message_type) ? $message_type : ''; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <main>
            <!-- FORM TAMBAH TUGAS -->
            <section class="form-section" id="tambah-tugas">
                <h2>➕ Tambah Tugas Baru</h2>
                <form method="POST" action="" id="formTugas">
                    <div class="form-group">
                        <label for="judul">Judul Tugas *</label>
                        <input type="text" id="judul" name="judul" placeholder="Judul tugas..." required>
                    </div>
                    
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Detail tugas..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="deadline">Deadline</label>
                        <input type="date" id="deadline" name="deadline" required>
                    </div>
                    
                    <button type="submit" name="tambah" class="btn">➕ Tambah Tugas</button>
                </form>
            </section>
            
            <!-- DAFTAR TUGAS -->
            <section class="tasks-section" id="daftar-tugas">
                <div class="tasks-header">
                    <h2>📝 Daftar Tugas Anda</h2>
                    <input type="text" id="search" placeholder="🔍 Cari tugas...">
                </div>
                
                <!-- STATISTIK -->
                <div class="stats" id="dashboard">
                    <div class="stat-card">
                        <h3><?php echo $total_tugas; ?></h3>
                        <p>Total Tugas</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $tugas_selesai; ?></h3>
                        <p>Selesai</p>
                    </div>
                    <div class="stat-card">
                        <h3><?php echo $tugas_pending; ?></h3>
                        <p>Pending</p>
                    </div>
                </div>
                
                <!-- DAFTAR TUGAS -->
                <div class="task-list" id="pencarian">
                    <?php if ($total_tugas > 0): ?>
                        <?php while ($tugas = mysqli_fetch_assoc($result_tugas)): ?>
                            <div class="task-item <?php echo $tugas['status'] == 'Selesai' ? 'selesai' : ''; ?>">
                                <div class="task-header">
                                    <div class="task-title"><?php echo htmlspecialchars($tugas['judul']); ?></div>
                                    <span class="task-status <?php echo $tugas['status'] == 'Pending' ? 'status-pending' : 'status-selesai'; ?>">
                                        <?php echo $tugas['status']; ?>
                                    </span>
                                </div>
                                
                                <div class="task-description">
                                    <?php echo nl2br(htmlspecialchars($tugas['deskripsi'])); ?>
                                </div>
                                
                                <div class="task-footer">
                                    <div class="task-deadline">
                                        ⏳ Deadline: <?php echo date('d M Y', strtotime($tugas['deadline'])); ?>
                                    </div>
                                    <div class="task-actions">
                                        <?php if ($tugas['status'] == 'Pending'): ?>
                                            <a href="?ubah_status&id=<?php echo $tugas['id']; ?>&status=<?php echo $tugas['status']; ?>" 
                                            class="btn-action btn-complete">✓ Selesai</a>
                                        <?php else: ?>
                                            <a href="?ubah_status&id=<?php echo $tugas['id']; ?>&status=<?php echo $tugas['status']; ?>" 
                                            class="btn-action btn-complete">↻ Pending</a>
                                        <?php endif; ?>
                                        
                                        <a href="?hapus&id=<?php echo $tugas['id']; ?>" 
                                        class="btn-action btn-delete">🗑 Hapus</a>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <div>📭</div>
                            <h3>Belum ada tugas</h3>
                            <p>Tambahkan tugas pertama Anda menggunakan form di samping!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </section>
        </main>
        
        <footer>
            <p>© <?php echo date('Y'); ?> Manajemen Tugas Harian - Proyek Akhir Pemrograman Web</p>
            <p>Total tugas: <?php echo $total_tugas; ?> | Selesai: <?php echo $tugas_selesai; ?> | Pending: <?php echo $tugas_pending; ?></p>
        </footer>
    </div>
    
    <script src="script.js"></script>
</body>
</html>
<?php
// Tutup koneksi database
mysqli_close($koneksi);
?>