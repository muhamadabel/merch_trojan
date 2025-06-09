/* Product Grid - Ukuran lebih kecil */
.products-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-top: 2rem;
}

.product-card {
  background: white;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
  transition: all 0.3s ease;
  height: fit-content;
}

.product-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.product-image {
  width: 100%;
  height: 200px;
  object-fit: cover;
  background: #f8f9fa;
}

.product-info {
  padding: 1rem;
}

.product-name {
  font-size: 1.1rem;
  font-weight: 600;
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.product-description {
  font-size: 0.9rem;
  color: #6b7280;
  margin-bottom: 0.75rem;
  line-height: 1.4;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.product-price {
  font-size: 1.2rem;
  font-weight: 700;
  color: #22c55e;
  margin-bottom: 0.75rem;
}

.product-stock {
  font-size: 0.85rem;
  color: #6b7280;
  margin-bottom: 1rem;
}

/* Cart Container - Layout lebih baik */
.cart-container {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
  margin-top: 2rem;
}

.cart-item {
  display: grid;
  grid-template-columns: 80px 1fr auto auto;
  gap: 1rem;
  align-items: center;
  padding: 1rem;
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  margin-bottom: 1rem;
}

.cart-item-image {
  width: 80px;
  height: 80px;
  object-fit: cover;
  border-radius: 6px;
}

.cart-item-details h3 {
  font-size: 1rem;
  margin-bottom: 0.25rem;
}

.cart-item-price {
  font-size: 0.9rem;
  color: #22c55e;
  font-weight: 600;
}

.cart-item-stock {
  font-size: 0.8rem;
  color: #6b7280;
}

.quantity-controls {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.quantity-input {
  width: 60px;
  padding: 0.25rem;
  border: 1px solid #d1d5db;
  border-radius: 4px;
  text-align: center;
}

.cart-item-subtotal {
  font-weight: 600;
  color: #1f2937;
}

.cart-summary {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: fit-content;
  position: sticky;
  top: 2rem;
}

/* Checkout Container */
.checkout-container {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 2rem;
  margin-top: 2rem;
}

.checkout-form {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.payment-methods {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1rem;
}

.payment-method {
  display: flex;
  align-items: center;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 8px;
  cursor: pointer;
  transition: all 0.3s ease;
}

.payment-method:hover {
  border-color: #22c55e;
  background: #f0fdf4;
}

.payment-method input[type="radio"] {
  margin-right: 1rem;
}

.payment-option strong {
  display: block;
  margin-bottom: 0.25rem;
}

.payment-option p {
  font-size: 0.9rem;
  color: #6b7280;
  margin: 0;
}

.order-summary {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  height: fit-content;
  position: sticky;
  top: 2rem;
}

.order-items {
  margin-bottom: 1rem;
}

.order-item {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
  border-bottom: 1px solid #e5e7eb;
}

.summary-row {
  display: flex;
  justify-content: space-between;
  padding: 0.5rem 0;
}

.summary-row.total {
  border-top: 2px solid #e5e7eb;
  margin-top: 1rem;
  padding-top: 1rem;
  font-weight: 600;
  font-size: 1.1rem;
}

/* Orders Page */
.orders-container {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 2rem;
}

.order-card {
  background: white;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
}

.order-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem;
  background: #f8f9fa;
  border-bottom: 1px solid #e5e7eb;
}

.order-content {
  padding: 1rem;
}

.order-item {
  display: flex;
  align-items: center;
  gap: 1rem;
  margin-bottom: 1rem;
}

.order-item-image {
  width: 60px;
  height: 60px;
  object-fit: cover;
  border-radius: 6px;
}

.order-actions {
  padding: 1rem;
  background: #f8f9fa;
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

.status-badge {
  padding: 0.25rem 0.75rem;
  border-radius: 20px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.status-pending {
  background: #fef3c7;
  color: #92400e;
}

.status-paid {
  background: #d1fae5;
  color: #065f46;
}

.status-shipped {
  background: #dbeafe;
  color: #1e40af;
}

/* Order Detail Page */
.order-detail-container {
  display: flex;
  flex-direction: column;
  gap: 1.5rem;
  margin-top: 2rem;
}

.order-summary-card,
.product-details-card,
.shipping-details-card,
.order-actions-card {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.order-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 1rem;
  margin-top: 1rem;
}

.info-item {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.info-item label {
  font-weight: 600;
  color: #6b7280;
  font-size: 0.9rem;
}

.info-item span {
  color: #1f2937;
}

.total-amount {
  font-size: 1.2rem;
  font-weight: 700;
  color: #22c55e;
}

.product-detail-item {
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.product-detail-image {
  width: 100px;
  height: 100px;
  object-fit: cover;
  border-radius: 8px;
}

.product-detail-info h4 {
  margin-bottom: 0.5rem;
  color: #1f2937;
}

.product-description {
  color: #6b7280;
  margin-bottom: 1rem;
  line-height: 1.5;
}

.product-quantity {
  color: #22c55e;
}

.shipping-info {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.order-actions-card {
  display: flex;
  gap: 1rem;
  justify-content: flex-end;
}

/* Responsive Design */
@media (max-width: 768px) {
  .products-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }

  .cart-container,
  .checkout-container {
    grid-template-columns: 1fr;
    gap: 1rem;
  }

  .cart-item {
    grid-template-columns: 60px 1fr;
    gap: 0.75rem;
  }

  .cart-item-actions {
    grid-column: 1 / -1;
    margin-top: 0.5rem;
  }

  .cart-item-subtotal {
    grid-column: 1 / -1;
    text-align: right;
    margin-top: 0.5rem;
  }

  .order-header {
    flex-direction: column;
    align-items: flex-start;
    gap: 0.5rem;
  }

  .order-actions {
    flex-direction: column;
  }

  .order-actions-card {
    flex-direction: column;
  }

  .product-detail-item {
    flex-direction: column;
  }

  .order-info-grid {
    grid-template-columns: 1fr;
  }
}
