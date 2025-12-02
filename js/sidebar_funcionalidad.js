document.addEventListener('DOMContentLoaded', function () {
    const cartToggle = document.getElementById('cartToggle');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const closeCart = document.getElementById('closeCart');

    function openCart() {
        cartSidebar.classList.add('active');
        cartOverlay.classList.add('active');
    }
    function closeCartFn() {
        cartSidebar.classList.remove('active');
        cartOverlay.classList.remove('active');
    }

    if (cartToggle) cartToggle.addEventListener('click', openCart);
    if (closeCart) closeCart.addEventListener('click', closeCartFn);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCartFn);
});