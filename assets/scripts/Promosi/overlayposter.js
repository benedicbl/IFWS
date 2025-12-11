// Ambil elemen-elemen yang dibutuhkan
const overlay = document.getElementById("poster-overlay");
const closeBtn = document.getElementById("close-overlay-btn");
const posterPreviewImg = document.getElementById("poster-preview-img");
const viewPosterBtns = document.querySelectorAll(".view-poster-btn");

// Fungsi untuk membuka overlay
function openOverlay(imageSrc) {
  posterPreviewImg.src = imageSrc; // Atur sumber gambar
  overlay.classList.remove("hidden"); // Tampilkan overlay
}

// Fungsi untuk menutup overlay
function closeOverlay() {
  overlay.classList.add("hidden"); // Sembunyikan overlay
}

// Tambahkan event listener ke setiap tombol mata
viewPosterBtns.forEach((btn) => {
  btn.addEventListener("click", function (event) {
    event.preventDefault(); // Mencegah link berpindah halaman
    const posterSrc = this.getAttribute("data-poster-src"); // Ambil path gambar
    openOverlay(posterSrc);
  });
});

// Event listener untuk tombol '< Back'
closeBtn.addEventListener("click", closeOverlay);

// Event listener untuk menutup overlay saat mengklik di luar area konten
overlay.addEventListener("click", function (event) {
  if (event.target === overlay) {
    closeOverlay();
  }
});
