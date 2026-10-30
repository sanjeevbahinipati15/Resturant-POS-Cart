<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant POS Cart</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f8fafc; margin: 0; padding: 24px; color: #1e293b; }
        .container { max-width: 900px; margin: 0 auto; display: grid; grid-template-columns: 1fr 1fr; gap: 24px; }
        .card { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        h2 { margin-top: 0; font-size: 1.25rem; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px; }
        .menu-item { display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid #f1f5f9; }
        .btn { background: #2563eb; color: #fff; border: none; padding: 6px 14px; border-radius: 4px; cursor: pointer; font-weight: 500; }
        .btn:hover { background: #1d4ed8; }
        .btn-qty { background: #e2e8f0; color: #1e293b; width: 28px; height: 28px; border-radius: 4px; border: none; font-weight: bold; cursor: pointer; }
        .btn-qty:hover { background: #cbd5e1; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { text-align: left; padding: 8px; font-size: 0.9rem; }
        th { border-bottom: 2px solid #e2e8f0; }
        td { border-bottom: 1px solid #f1f5f9; }
        .qty-controls { display: flex; align-items: center; gap: 8px; }
        .summary-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 0.95rem; }
        .summary-total { font-weight: bold; font-size: 1.15rem; color: #0f172a; border-top: 2px solid #e2e8f0; padding-top: 10px; margin-top: 8px; }
    </style>
</head>
<body>

<div class="container">
    <!-- Available Items -->
    <div class="card">
        <h2>Menu Items</h2>
        <?php foreach ($available_items as $item): ?>
            <div class="menu-item">
                <div>
                    <strong><?= esc($item['name']) ?></strong>
                    <div style="color: #64748b; font-size: 0.85rem;">£<?= number_format($item['price'], 2) ?> (Incl. 12.5% Tax)</div>
                </div>
                <button class="btn" onclick="addToCart(<?= $item['id'] ?>, '<?= esc($item['name']) ?>', <?= $item['price'] ?>)">Add</button>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Cart Summary -->
    <div class="card">
        <h2>Order Summary</h2>
        <table id="cart-table">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Unit</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="cart-body">
                <tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">Cart is empty</td></tr>
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            <div class="summary-row">
                <span>Net Total (Excl. Tax):</span>
                <span id="net-total">£0.00</span>
            </div>
            <div class="summary-row">
                <span>Tax (12.5%):</span>
                <span id="tax-amount">£0.00</span>
            </div>
            <div class="summary-row summary-total">
                <span>Total (Incl. Tax):</span>
                <span id="gross-total">£0.00</span>
            </div>
        </div>
    </div>
</div>

<script>
    const TAX_RATE = 0.125;
    let cart = {};

    function addToCart(id, name, price) {
        if (cart[id]) {
            cart[id].qty += 1;
        } else {
            cart[id] = { id, name, price, qty: 1 };
        }
        renderCart();
    }

    function updateQty(id, change) {
        if (!cart[id]) return;
        cart[id].qty += change;
        if (cart[id].qty <= 0) {
            delete cart[id];
        }
        renderCart();
    }

    function renderCart() {
        const tbody = document.getElementById('cart-body');
        const items = Object.values(cart);

        if (items.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4" style="text-align: center; color: #94a3b8; padding: 24px;">Cart is empty</td></tr>';
            document.getElementById('net-total').innerText = '£0.00';
            document.getElementById('tax-amount').innerText = '£0.00';
            document.getElementById('gross-total').innerText = '£0.00';
            return;
        }

        let grossTotal = 0;
        tbody.innerHTML = items.map(item => {
            const subtotal = item.price * item.qty;
            grossTotal += subtotal;
            return `
                <tr>
                    <td><strong>${item.name}</strong></td>
                    <td>£${item.price.toFixed(2)}</td>
                    <td>
                        <div class="qty-controls">
                            <button class="btn-qty" onclick="updateQty(${item.id}, -1)">-</button>
                            <span>${item.qty}</span>
                            <button class="btn-qty" onclick="updateQty(${item.id}, 1)">+</button>
                        </div>
                    </td>
                    <td>£${subtotal.toFixed(2)}</td>
                </tr>
            `;
        }).join('');

        // Inclusive tax calculations
        const netTotal = grossTotal / (1 + TAX_RATE);
        const taxAmount = grossTotal - netTotal;

        document.getElementById('net-total').innerText = '£' + netTotal.toFixed(2);
        document.getElementById('tax-amount').innerText = '£' + taxAmount.toFixed(2);
        document.getElementById('gross-total').innerText = '£' + grossTotal.toFixed(2);
    }
</script>
</body>
</html>