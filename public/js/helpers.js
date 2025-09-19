const apiCall = async (method, url, data = {}) => {
  try {
    const response = await axios[method](url, data);
    return response.data;
  } catch (error) {
    console.log(error.response?.data)
    // Menampilkan pesan error yang lebih informatif
    const errorMessage =
      error.response?.data?.message || "Terjadi kesalahan tidak terduga.";
    showToast("error", errorMessage);
    throw error; // Melemparkan error agar bisa ditangani lebih lanjut jika perlu
  }
};

const showToast = (icon, title) => {
  Swal.fire({
    toast: true,
    position: "top-end",
    icon: icon,
    title: title,
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
  });
};

const showConfirmation = async ({
  title = "Konfirmasi",
  text,
  confirmButtonText = "Ya, lanjutkan",
}) => {
  const result = await Swal.fire({
    title: title,
    text: text,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: confirmButtonText,
    cancelButtonText: "Batal",
  });
  return result.isConfirmed;
};

const initModal = (modalId) => {
  const modalEl = document.getElementById(modalId);
  if (!modalEl) return null;
  return new bootstrap.Modal(modalEl, { focus: false });
};

const e = (str) => {
  if (str === null || typeof str === "undefined") return "";
  return str.toString().replace(/[&<>"']/g, (match) => {
    return {
      "&": "&amp;",
      "<": "&lt;",
      ">": "&gt;",
      '"': "&quot;",
      "'": "&#39;",
    }[match];
  });
};
