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

/**
 * 2. FUNGSI PAPARKAN REKOD HAIWAN (RENDER CARDS)
 * Membina elemen kad haiwan secara dinamik mengikut data yang ditarik dari pangkalan data.
 */
function paparkanPet(data) {
    const kontena = document.getElementById("senarai-pet");
    if (!kontena) return;

    // Kosongkan sebarang kad lama di dalam kontena
    kontena.innerHTML = "";

    // Jika NGO belum mendaftarkan mana-mana haiwan atau hasil carian kosong
    if (data.length === 0) {
        kontena.innerHTML = `
            <div style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #8175ba; font-style: italic;">
                No pets registered or found under this category.
            </div>`;
        return;
    }

    // Lakukan gelung (loop) untuk setiap data haiwan
    data.forEach(pet => {
        const kad = document.createElement("div");
        kad.className = "pet-card"; // Mengikut kelas di dalam Pet_Listing.css

        // Penukaran logik status ketersediaan tinyint(1) -> Teks paparan
        let statusTeks = "Available";
        if (pet.IsAvailable == 0) {
            statusTeks = "Adopted";
        } else if (pet.IsAvailable == 2 || pet.IsAvailable === "In Progress") {
            statusTeks = "In Progress";
        }

        // Penukaran status kembiri (Neutered)
        let statusMandul = (pet.Neutered == 1 || pet.Neutered === "Yes") ? "Yes" : "No";

        // Susun HTML dalaman bagi setiap kad haiwan
        kad.innerHTML = `
            <div class="pet-img-container" style="position: relative; width: 100%; height: 200px; overflow: hidden; border-radius: 12px;">
                <img src="../image/pets/${pet.Photo}" alt="${pet.PetName}" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            <div class="pet-info" style="padding: 12px 0;">
                <h3 style="margin-bottom: 6px; color: var(--deep-brown);">${pet.PetName}</h3>
                <p style="font-size: 0.9rem; margin: 3px 0;"><strong>Breed:</strong> ${pet.Breed}</p>
                <p style="font-size: 0.9rem; margin: 3px 0;"><strong>Age:</strong> ${pet.Age} months</p>
                <p style="font-size: 0.9rem; margin: 3px 0;"><strong>Gender:</strong> ${pet.Gender}</p>
                <p style="font-size: 0.9rem; margin: 3px 0;"><strong>Location:</strong> ${pet.Location}</p>
                <p style="font-size: 0.9rem; margin: 3px 0;"><strong>Neutered:</strong> ${statusMandul}</p>
                <p style="font-size: 0.9rem; margin: 3px 0;"><strong>Status:</strong> <span class="status-tag">${statusTeks}</span></p>
                ${pet.Allergies && pet.Allergies !== 'None' ? `<p style="font-size: 0.85rem; color: #c97d7d; margin-top: 6px;">⚠️ <em>Medical: ${pet.Allergies}</em></p>` : ''}
            </div>
            <div class="aksi-pet" style="display: flex; gap: 8px; margin-top: 10px;">
                <button type="button" class="btn-edit" onclick="bukaModalEdit('${pet.PetID}')" style="flex: 1; padding: 8px; cursor: pointer;">Edit</button>
                <button type="button" class="btn-delete" onclick="sahkanPadam('${pet.PetID}')" style="padding: 8px; background: none; border: 1px solid var(--rose); color: var(--rose); border-radius: 6px; cursor: pointer;" title="Delete Pet">
                    🗑️
                </button>
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