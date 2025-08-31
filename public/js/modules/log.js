document.addEventListener("DOMContentLoaded", async () => {
  const logContent = document.getElementById("log-content");
  if (!logContent) return;

  try {
    const response = await apiCall("get", "/log/api/getLog");
    logContent.textContent = response.data;
  } catch (error) {
    logContent.textContent = "Gagal memuat file log.";
  }
});
