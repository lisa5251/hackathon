document.addEventListener("DOMContentLoaded", () => {
  const tablesContainer = document.getElementById("tables");
  for (let i = 1; i <= 10; i++) {
    const div = document.createElement("div");
    div.className = "table-card";
    div.textContent = `Table ${i}`;
    div.onclick = () => openModal(i);
    tablesContainer.appendChild(div);
  }

  document.getElementById("orderForm").addEventListener("submit", async function(e) {
    e.preventDefault();

    const table = document.getElementById("tableNumber").value;
    const items = document.getElementById("orderItems").value;

    const formData = new FormData();
    formData.append("table", table);
    formData.append("items", items);

    const res = await fetch("submit_order.php", {
      method: "POST",
      body: formData,
    });

    const message = await res.text();
    document.getElementById("orderMessage").textContent = message;
    document.getElementById("orderItems").value = "";
  });
});

function openModal(tableNum) {
  document.getElementById("modalTableNum").textContent = tableNum;
  document.getElementById("tableNumber").value = tableNum;
  document.getElementById("orderModal").style.display = "flex";
}

function closeModal() {
  document.getElementById("orderModal").style.display = "none";
}
