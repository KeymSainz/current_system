/**
 * Fix&Go — Shopping Cart Module
 * Manages cart operations for owner purchases
 */

const FGCart = (function() {
  'use strict';

  const STORAGE_KEY = 'fg_owner_cart';

  // ── Get cart from storage ──────────────────────────────────
  function getCart() {
    try {
      const data = sessionStorage.getItem(STORAGE_KEY);
      return data ? JSON.parse(data) : [];
    } catch (e) {
      console.error('Cart parse error:', e);
      return [];
    }
  }

  // ── Save cart to storage ────────────────────────────────────
  function saveCart(cart) {
    try {
      sessionStorage.setItem(STORAGE_KEY, JSON.stringify(cart));
      updateCartBadge();
      return true;
    } catch (e) {
      console.error('Cart save error:', e);
      return false;
    }
  }

  // ── Add item to cart ────────────────────────────────────────
  function addItem(product, quantity) {
    const cart = getCart();
    
    // Check if product already in cart
    const existingIndex = cart.findIndex(item => item.id === product.id);
    
    if (existingIndex >= 0) {
      // Update quantity (don't exceed available stock)
      const newQty = cart[existingIndex].quantity + quantity;
      const maxQty = parseInt(product.qty) || 0;
      cart[existingIndex].quantity = Math.min(newQty, maxQty);
    } else {
      // Add new item
      cart.push({
        id: product.id,
        category: product.category,
        brand: product.brand || '',
        item_description: product.item_description,
        srp: parseFloat(product.srp),
        image_path: product.image_path || null,
        supplier_name: product.supplier_name || 'Unknown',
        quantity: quantity,
        maxQty: parseInt(product.qty) || 0
      });
    }
    
    saveCart(cart);
    return true;
  }

  // ── Remove item from cart ───────────────────────────────────
  function removeItem(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
  }

  // ── Update item quantity ────────────────────────────────────
  function updateQuantity(productId, quantity) {
    const cart = getCart();
    const item = cart.find(i => i.id === productId);
    
    if (item) {
      // Ensure quantity is within bounds
      item.quantity = Math.max(1, Math.min(quantity, item.maxQty));
      saveCart(cart);
      return true;
    }
    return false;
  }

  // ── Clear entire cart ───────────────────────────────────────
  function clearCart() {
    sessionStorage.removeItem(STORAGE_KEY);
    updateCartBadge();
  }

  // ── Get cart item count ─────────────────────────────────────
  function getItemCount() {
    const cart = getCart();
    return cart.reduce((sum, item) => sum + item.quantity, 0);
  }

  // ── Get cart total amount ───────────────────────────────────
  function getTotal() {
    const cart = getCart();
    return cart.reduce((sum, item) => sum + (item.srp * item.quantity), 0);
  }

  // ── Update cart badge in navbar ─────────────────────────────
  function updateCartBadge() {
    const badge = document.getElementById('cartBadge');
    if (!badge) return;
    
    const count = getItemCount();
    if (count > 0) {
      badge.textContent = count;
      badge.style.display = 'inline-block';
    } else {
      badge.style.display = 'none';
    }
  }

  // ── Group cart items by supplier ────────────────────────────
  function getCartBySupplier() {
    const cart = getCart();
    const grouped = {};
    
    cart.forEach(item => {
      const supplier = item.supplier_name || 'Unknown';
      if (!grouped[supplier]) {
        grouped[supplier] = [];
      }
      grouped[supplier].push(item);
    });
    
    return grouped;
  }

  // ── Public API ──────────────────────────────────────────────
  return {
    getCart,
    addItem,
    removeItem,
    updateQuantity,
    clearCart,
    getItemCount,
    getTotal,
    updateCartBadge,
    getCartBySupplier
  };
})();

// Initialize cart badge on page load
document.addEventListener('DOMContentLoaded', function() {
  FGCart.updateCartBadge();
});
