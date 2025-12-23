// Validasi Form
document.addEventListener('DOMContentLoaded', function() {
    const formTugas = document.getElementById('formTugas');
    if (formTugas) {
        formTugas.addEventListener('submit', function(e) {
            const judul = document.getElementById('judul').value.trim();
            
            if (judul === '') {
                e.preventDefault();
                alert('❌ Judul tugas tidak boleh kosong!');
                document.getElementById('judul').focus();
                return false;
            }
            
            // Validasi deadline tidak di masa lalu
            const deadline = new Date(document.getElementById('deadline').value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (deadline < today) {
                if (!confirm('⚠️ Deadline sudah lewat. Tetap tambahkan tugas?')) {
                    e.preventDefault();
                    return false;
                }
            }
            
            return true;
        });
    }
    
    // Fitur Pencarian
    const searchInput = document.getElementById('search');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const tasks = document.querySelectorAll('.task-item');
            
            tasks.forEach(task => {
                const title = task.querySelector('.task-title').textContent.toLowerCase();
                const description = task.querySelector('.task-description').textContent.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    task.style.display = 'block';
                } else {
                    task.style.display = 'none';
                }
            });
        });
    }
    
    // Set deadline minimal hari ini
    const deadlineInput = document.getElementById('deadline');
    if (deadlineInput) {
        const today = new Date().toISOString().split('T')[0];
        deadlineInput.min = today;
    }
    
    // Auto-hide pesan setelah 5 detik
    setTimeout(() => {
        const messages = document.querySelectorAll('.message');
        messages.forEach(msg => {
            msg.style.transition = 'opacity 0.5s';
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 500);
        });
    }, 5000);
    
    // Konfirmasi sebelum hapus
    document.querySelectorAll('.btn-delete').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Yakin ingin menghapus tugas ini?')) {
                e.preventDefault();
            }
        });
    });
});