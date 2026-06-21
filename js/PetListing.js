/**
 * Furever Pet Home — PetListing.js
 * Menguruskan paparan kad haiwan dinamik, tapisan kategori, serta modal tambah & edit.
 */

// 1. JALANKAN PAPARAN SEBAIK SAHAJA HALAMAN BERJAYA DIMUAT NAIK
document.addEventListener("DOMContentLoaded", () => {
    // Semak jika data array dari PHP wujud
    if (typeof senaraiPet !== 'undefined') {
        paparkanPet(senaraiPet);
    } else {
        console.error("Ralat: Pemboleh ubah 'senaraiPet' tidak ditemui dari backend PHP.");
    }
});

// Emoji ikut jenis haiwan — dipaparkan jika foto tiada/gagal dimuatkan
const EMOJI_PET = {
    cat: "🐱",
    dog: "🐶"
};

/**
 * Helper: escape HTML supaya data daripada DB selamat disuntik ke dalam template
 */
function escapeHtml(str) {
    if (str === null || str === undefined) return '';
    return String(str)
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

/**
 * 2. FUNGSI PAPARKAN REKOD HAIWAN (RENDER CARDS)
 * Membina elemen kad haiwan secara dinamik mengikut data yang ditarik dari pangkalan data,
 * menggunakan struktur & class yang sepadan dengan Pet_Listing.css (.kad-pet, .gambar-pet, .info-pet, .aksi-pet)
 */
function paparkanPet(data) {
    const kontena = document.getElementById("senarai-pet");
    if (!kontena) return;

    // Kosongkan sebarang kad lama di dalam kontena
    kontena.innerHTML = "";

    // Jika NGO belum mendaftarkan mana-mana haiwan atau hasil carian kosong
    if (data.length === 0) {
        kontena.innerHTML = `<div class="tiada-pet">No pets registered or found under this category.</div>`;
        return;
    }

    // Lakukan gelung (loop) untuk setiap data haiwan
    data.forEach(pet => {
        const kad = document.createElement("div");
        kad.className = "kad-pet"; // Sepadan dengan Pet_Listing.css

        // Penukaran logik status ketersediaan -> kelas badge + label paparan (BM)
        let statusClass = "tersedia";
        let statusLabel = "TERSEDIA";
        if (pet.IsAvailable == 0 || pet.IsAvailable === "Adopted") {
            statusClass = "dipelihara";
            statusLabel = "DIPELIHARA";
        } else if (pet.IsAvailable == 2 || pet.IsAvailable === "In Progress") {
            statusClass = "proses";
            statusLabel = "DALAM PROSES";
        }

        // Penukaran status kembiri (Neutered)
        const statusMandul = (pet.Neutered == 1 || pet.Neutered === "Yes") ? "Yes" : "No";

        const emoji = EMOJI_PET[(pet.PetType || '').toLowerCase()] || "🐾";
        const namaSelamat = escapeHtml(pet.PetName);
        const bakaSelamat = escapeHtml(pet.Breed);

        // Susun HTML dalaman bagi setiap kad haiwan — ikut struktur CSS sedia ada
        kad.innerHTML = `
            <div class="gambar-pet">
                <mark class="${statusClass}">${statusLabel}</mark>
                <img src="../image/pets/${escapeHtml(pet.Photo)}" alt="${namaSelamat}"
                     style="width:100%; height:100%; object-fit:cover;"
                     onerror="this.replaceWith(Object.assign(document.createElement('span'), {className:'emoji', textContent:'${emoji}'}))">
            </div>
            <div class="info-pet">
                <header>
                    <hgroup>
                        <h3>${namaSelamat}</h3>
                        <p>${bakaSelamat}</p>
                    </hgroup>
                    <strong>${parseInt(pet.Age) || 0} bulan</strong>
                </header>

                <ul>
                    <li>${escapeHtml(pet.PetType)}</li>
                    <li>${bakaSelamat}</li>
                    <li>${escapeHtml(pet.Gender)}</li>
                    <li>Mandul: ${statusMandul}</li>
                </ul>

                ${pet.Allergies && pet.Allergies !== 'None'
                    ? `<p style="font-size:0.78rem; color:var(--rose); margin:-0.3rem 0 0;">⚠️ ${escapeHtml(pet.Allergies)}</p>`
                    : ''}

                <div class="aksi-pet">
                    <button type="button" class="btn-pelihara" onclick="bukaModalEdit('${pet.PetID}')">
                        Update
                    </button>
                    <button type="button" class="btn-info" title="Delete Pet" onclick="sahkanPadam('${pet.PetID}')">
                        <svg viewBox="0 0 24 24"><path d="M3 6h18M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m3 0-1 14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2L4 6"/></svg>
                    </button>
                </div>
            </div>
        `;
        kontena.appendChild(kad);
    });
}

/**
 * 3. FUNGSI TAPISAN (FILTER SYSTEM)
 * Menapis paparan Cat / Dog / All secara real-time tanpa memuat semula halaman web.
 */
function tapis(jenis, butang) {
    // Padam kelas 'active' dari semua butang tapis dalam baris
    const butangButang = document.querySelectorAll(".tab-row button");
    butangButang.forEach(btn => btn.classList.remove("active"));

    // Setkan butang yang diklik menjadi aktif
    butang.classList.add("active");

    if (jenis === 'all') {
        paparkanPet(senaraiPet);
    } else {
        // Tapis array berdasarkan nilai PetType (Cat / Dog)
        const hasilTapis = senaraiPet.filter(pet => pet.PetType.toLowerCase() === jenis.toLowerCase());
        paparkanPet(hasilTapis);
    }
}

/**
 * 4. KAWALAN MODAL TAMBAH (ADD PET MODAL)
 */
function bukaModalTambah() {
    const modal = document.getElementById("modal-tambah");
    if (modal) {
        document.getElementById("form-tambah").reset(); // Bersihkan form dari data lama
        modal.showModal();
    }
}

function tutupModalTambah() {
    const modal = document.getElementById("modal-tambah");
    if (modal) {
        modal.close();
    }
}

/**
 * 5. KAWALAN MODAL EDIT (EDIT PET MODAL + AUTO-POPULATE DATA)
 * Mencari data spesifik daripada array 'senaraiPet' mengikut PetID, mengisi input borang, dan membuka modal.
 */
function bukaModalEdit(petID) {
    // Cari objek haiwan yang sepadan berdasarkan ID
    const pet = senaraiPet.find(p => p.PetID === petID);

    if (pet) {
        // Isi form edit menggunakan data sedia ada dari database
        document.getElementById("edit-id").value = pet.PetID;
        document.getElementById("edit-nama").value = pet.PetName;
        document.getElementById("edit-jenis").value = pet.PetType;
        document.getElementById("edit-gender").value = pet.Gender;
        document.getElementById("edit-baka").value = pet.Breed;
        document.getElementById("edit-umur").value = pet.Age;
        document.getElementById("edit-lokasi").value = pet.Location;
        document.getElementById("edit-allergies").value = pet.Allergies;

        // Tukarkan status tinyint database (1 / 0) kepada padanan pilihan form (Yes / No)
        document.getElementById("edit-neutered").value = (pet.Neutered == 1 || pet.Neutered === "Yes") ? "Yes" : "No";

        // Laraskan pilihan dropdown status mengikut nilai database
        if (pet.IsAvailable == 1 || pet.IsAvailable === "Available") {
            document.getElementById("edit-status").value = "Available";
        } else if (pet.IsAvailable == 0 || pet.IsAvailable === "Adopted") {
            document.getElementById("edit-status").value = "Adopted";
        } else {
            document.getElementById("edit-status").value = "In Progress";
        }

        // Buka paparan modal edit <dialog>
        const modalEdit = document.getElementById("modal-edit");
        if (modalEdit) {
            modalEdit.showModal();
        }
    } else {
        alert("Error: Pet record not found.");
    }
}

function tutupModalEdit() {
    const modal = document.getElementById("modal-edit");
    if (modal) {
        modal.close();
    }
}

/**
 * 6. PENGURUSAN PADAM (DELETE PET RECORD ACTION)
 * Membawa ID haiwan ke form tersembunyi 'form-delete' dan menghantar submits secara automatik ke PHP.
 */
function sahkanPadam(petID) {
    const sah = confirm("Are you sure you want to remove this pet record? This action cannot be undone.");

    if (sah) {
        const deleteInput = document.getElementById("delete-id");
        const deleteForm = document.getElementById("form-delete");

        if (deleteInput && deleteForm) {
            // Masukkan ID haiwan ke dalam hidden input form-delete
            deleteInput.value = petID;
            // Jalankan submit form ke backend PHP
            deleteForm.submit();
        } else {
            alert("System Error: Delete form elements could not be found.");
        }
    }
}

function toggleProfileDropdown() {
  const menu = document.getElementById('profileDropdown');
  menu.classList.toggle('open');
}

// Tutup dropdown bila click luar
document.addEventListener('click', function(e) {
  const dropdown = document.querySelector('.profile-dropdown');
  if (dropdown && !dropdown.contains(e.target)) {
    document.getElementById('profileDropdown').classList.remove('open');
  }
});
