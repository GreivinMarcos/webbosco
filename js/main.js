document.addEventListener("DOMContentLoaded", () => {
  const cartCount = document.getElementById("cart-count");
  let itemsInCart = 0; // ejemplo
  cartCount.textContent = itemsInCart;

  console.log("Bosco Box listo y completamente responsivo 💪");

  // Inicializar VanillaTilt solo en entrenadores
  VanillaTilt.init(document.querySelectorAll(".tilt-entrenador"));
});

