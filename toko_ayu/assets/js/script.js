
// Modal Functions
function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.remove('active');
    }
}

// Confirm Delete
function confirmDelete(message) {
    return confirm(message || 'Apakah Anda yakin ingin menghapus data ini?');
}

// Format Currency
function formatCurrency(amount) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(amount);
}

// Auto-calculate total in transaction form
function calculateTotal() {
    const barangSelect = document.getElementById('id_barang');
    const jumlahInput = document.getElementById('jumlah');
    const totalDisplay = document.getElementById('total_display');
    
    if (barangSelect && jumlahInput && totalDisplay) {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const harga = parseFloat(selectedOption.getAttribute('data-harga') || 0);
        const jumlah = parseInt(jumlahInput.value || 0);
        const total = harga * jumlah;
        
        totalDisplay.textContent = formatCurrency(total);
    }
}

// Stock validation
function validateStock() {
    const barangSelect = document.getElementById('id_barang');
    const jumlahInput = document.getElementById('jumlah');
    
    if (barangSelect && jumlahInput) {
        const selectedOption = barangSelect.options[barangSelect.selectedIndex];
        const stok = parseInt(selectedOption.getAttribute('data-stok') || 0);
        const jumlah = parseInt(jumlahInput.value || 0);
        
        if (jumlah > stok) {
            alert(`Stok tidak mencukupi! Stok tersedia: ${stok}`);
            jumlahInput.value = stok;
            return false;
        }
    }
    
    return true;
}

// Search functionality
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toUpperCase();
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        let found = false;
        const cells = rows[i].getElementsByTagName('td');
        
        for (let j = 0; j < cells.length; j++) {
            const cell = cells[j];
            if (cell) {
                const textValue = cell.textContent || cell.innerText;
                if (textValue.toUpperCase().indexOf(filter) > -1) {
                    found = true;
                    break;
                }
            }
        }
        
        rows[i].style.display = found ? '' : 'none';
    }
}

// Print function
function printReport() {
    window.print();
}

// Export to CSV
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    for (let row of rows) {
        let cols = row.querySelectorAll('td, th');
        let csvRow = [];
        for (let col of cols) {
            // Skip action column
            if (!col.classList.contains('action-column')) {
                csvRow.push(col.innerText);
            }
        }
        csv.push(csvRow.join(','));
    }
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename || 'export.csv';
    a.click();
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Close modal when clicking outside
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.remove('active');
            }
        });
    });
    
    // Auto uppercase for barang name
    const namaBarangInput = document.getElementById('nama_barang');
    if (namaBarangInput) {
        namaBarangInput.addEventListener('input', function() {
            this.value = this.value.toUpperCase();
        });
    }
});
