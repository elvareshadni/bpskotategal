<?php if ($msg = session()->getFlashdata('success')): ?>
  <div id="popup-notif" class="popup-notif">
    <?= esc($msg) ?>
  </div>
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const popup = document.getElementById("popup-notif");
      if (popup) {
        setTimeout(() => {
          popup.style.opacity = "0";
          popup.style.transform = "translateY(-20px)";
          setTimeout(() => popup.remove(), 600); // hapus dari DOM setelah animasi
        }, 4000); // tampil 4 detik
      }
    });
  </script>
  <style>
    .popup-notif {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background: #22bb33;
      color: #fff;
      padding: 12px 20px;
      border-radius: 6px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.2);
      font-size: 14px;
      z-index: 9999;
      opacity: 1;
      transition: all 0.6s ease;
    }
  </style>
<?php endif; ?>
