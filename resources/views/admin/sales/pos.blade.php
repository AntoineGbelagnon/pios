@extends('layouts.app')

@section('content')
    <style>
        .pos-container {
            display: grid;
            grid-template-columns: 1fr 380px;
            gap: 1rem;
            min-height: calc(100vh - 120px);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 0.75rem;
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }

        .product-card {
            cursor: pointer;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.2s;
            background: #fff;
        }

        .product-card:hover {
            border-color: #0d6efd;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .product-card.out-of-stock {
            opacity: 0.5;
            pointer-events: none;
        }

        .product-name {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 0.25rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .product-price {
            color: #198754;
            font-weight: 700;
            font-size: 0.95rem;
        }

        .product-stock {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .cart-panel {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 1rem;
            display: flex;
            flex-direction: column;
        }

        .cart-items {
            flex: 1;
            overflow-y: auto;
            max-height: 300px;
        }

        .cart-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .cart-item-name {
            flex: 1;
            font-size: 0.85rem;
        }

        .cart-item-qty {
            width: 60px;
        }

        .cart-item-total {
            font-weight: 600;
            min-width: 80px;
            text-align: right;
        }

        .cart-summary {
            border-top: 2px solid #dee2e6;
            padding-top: 0.75rem;
            margin-top: 0.75rem;
        }

        .search-box {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 10;
            padding-bottom: 0.75rem;
        }

        .payment-method-btn {
            flex: 1;
            padding: 0.75rem;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            background: #fff;
            cursor: pointer;
            text-align: center;
            transition: all 0.2s;
        }

        .payment-method-btn:hover,
        .payment-method-btn.active {
            border-color: #0d6efd;
            background: #e7f1ff;
        }
    </style>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <p class="text-secondary mb-1">Point de vente</p>
            <h1 class="h4 mb-0">POS - {{ $shops->first()->name ?? '' }}</h1>
        </div>
        <a href="{{ route('sales.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-list"></i> Historique ventes
        </a>
    </div>

    <div class="pos-container">
        <div>
            <div class="search-box">
                <input type="text" id="productSearch" class="form-control form-control-lg"
                    placeholder="Rechercher par nom ou SKU..." autofocus>
            </div>
            <div class="product-grid" id="productGrid">
                @foreach ($products as $product)
                    <div class="product-card {{ $product->stock_quantity <= 0 ? 'out-of-stock' : '' }}"
                        data-id="{{ $product->id }}" data-name="{{ $product->name }}" data-sku="{{ $product->sku }}"
                        data-price="{{ $product->effective_price }}" data-stock="{{ $product->stock_quantity }}">
                        <div class="product-name" title="{{ $product->name }}">{{ $product->name }}</div>
                        <div class="product-price">{{ number_format($product->effective_price, 0, ',', ' ') }} FCFA</div>
                        <div class="product-stock">Stock: {{ $product->stock_quantity }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="cart-panel">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0"><i class="bi bi-cart3"></i> Panier</h5>
                <button class="btn btn-sm btn-outline-danger" id="clearCart"><i class="bi bi-trash"></i></button>
            </div>
            <div class="row g-2 mb-3">
                <div class="col-6">
                    <select id="customerId" class="form-select form-select-sm">
                        <option value="">Client occasionnel</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6">
                    <select id="shopId" class="form-select form-select-sm">
                        @foreach ($shops as $shop)
                            <option value="{{ $shop->id }}">{{ $shop->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="cart-items" id="cartItems">
                <div class="text-center text-secondary py-4" id="emptyCart">
                    <i class="bi bi-cart-x fs-1"></i>
                    <p class="mt-2 mb-0">Panier vide</p>
                </div>
            </div>
            <div class="cart-summary">
                <div class="d-flex justify-content-between mb-1"><span>Sous-total</span><span id="subtotal">0 FCFA</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="fw-bold fs-5">TOTAL</span>
                    <span class="fw-bold fs-5 text-primary" id="total">0 FCFA</span>
                </div>
            </div>
            <div class="mt-3">
                <div class="d-flex gap-2 mb-3">
                    <button class="payment-method-btn active" data-method="cash">Especes</button>
                    <button class="payment-method-btn" data-method="card">Carte</button>
                    <button class="payment-method-btn" data-method="mobile_money">MoMo</button>
                    <button class="payment-method-btn" data-method="credit">Credit</button>
                    <button class="payment-method-btn" data-method="mixed">Mixte</button>
                </div>
                <div id="mixedPaymentSection" style="display:none;">
                    <div class="small fw-bold mb-2">Repartition du paiement :</div>
                    <div class="row g-1 mb-1"><div class="col-6 small">Especes</div><div class="col-6"><input type="number" id="mixCash" class="form-control form-control-sm" value="0" min="0"></div></div>
                    <div class="row g-1 mb-1"><div class="col-6 small">Carte</div><div class="col-6"><input type="number" id="mixCard" class="form-control form-control-sm" value="0" min="0"></div></div>
                    <div class="row g-1 mb-1"><div class="col-6 small">MoMo</div><div class="col-6"><input type="number" id="mixMomo" class="form-control form-control-sm" value="0" min="0"></div></div>
                    <div class="d-flex justify-content-between small mt-1"><span class="text-secondary">Total reparti :</span><span id="mixTotal" class="fw-bold">0 FCFA</span></div>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Montant paye (FCFA)</label>
                    <input type="number" id="amountPaid" class="form-control form-control-lg" value="0"
                        min="0">
                </div>
                <div class="d-flex justify-content-between mb-2"><span>Monnaie :</span><span id="change"
                        class="fw-bold">0 FCFA</span></div>
                <button id="completeSale" class="btn btn-success btn-lg w-100" disabled>
                    <i class="bi bi-check-circle"></i> Valider la vente
                </button>
            </div>
        </div>
    </div>
    <form id="saleForm" method="POST" action="{{ route('sales.store') }}" style="display:none">
        @csrf
        <input type="hidden" name="shop_id" id="formShopId">
        <input type="hidden" name="customer_id" id="formCustomerId">
        <input type="hidden" name="payment_method" id="formPaymentMethod">
        <input type="hidden" name="amount_paid" id="formAmountPaid">
        <div id="formItemsContainer"></div>
    </form>

    @push('scripts')
        <script>
            (function() {
                var cart = [];
                var paymentMethod = 'cash';

                document.getElementById('productSearch').addEventListener('input', function() {
                    var q = this.value.toLowerCase();
                    document.querySelectorAll('.product-card').forEach(function(card) {
                        var name = card.dataset.name.toLowerCase();
                        var sku = (card.dataset.sku || '').toLowerCase();
                        card.style.display = (name.indexOf(q) !== -1 || sku.indexOf(q) !== -1) ? '' :
                        'none';
                    });
                });

                document.querySelectorAll('.product-card').forEach(function(card) {
                    card.addEventListener('click', function() {
                        var id = parseInt(this.dataset.id);
                        var existing = cart.find(function(i) {
                            return i.id === id;
                        });
                        if (existing) {
                            if (existing.quantity < existing.stock) {
                                existing.quantity++;
                            }
                        } else {
                            if (parseInt(this.dataset.stock) > 0) {
                                cart.push({
                                    id: id,
                                    name: this.dataset.name,
                                    sku: this.dataset.sku,
                                    price: parseFloat(this.dataset.price),
                                    stock: parseInt(this.dataset.stock),
                                    quantity: 1
                                });
                            }
                        }
                        renderCart();
                    });
                });

                function renderCart() {
                    var container = document.getElementById('cartItems');
                    if (cart.length === 0) {
                        container.innerHTML =
                            '<div class="text-center text-secondary py-4"><i class="bi bi-cart-x fs-1"></i><p class="mt-2 mb-0">Panier vide</p></div>';
                        document.getElementById('completeSale').disabled = true;
                    } else {
                        var html = '';
                        cart.forEach(function(item, idx) {
                            html += '<div class="cart-item"><div class="cart-item-name">' + item.name +
                                '<br><small class="text-muted">' + item.price.toLocaleString() + ' x ' + item
                                .quantity + '</small></div>';
                            html +=
                                '<input type="number" class="form-control form-control-sm cart-item-qty" value="' +
                                item.quantity + '" min="1" max="' + item.stock + '" data-idx="' + idx + '">';
                            html += '<div class="cart-item-total">' + (item.price * item.quantity)
                            .toLocaleString() + ' F</div>';
                            html += '<button class="btn btn-sm btn-outline-danger" data-remove="' + idx +
                                '"><i class="bi bi-x"></i></button></div>';
                        });
                        container.innerHTML = html;
                        container.querySelectorAll('.cart-item-qty').forEach(function(input) {
                            input.addEventListener('change', function() {
                                cart[parseInt(this.dataset.idx)].quantity = parseInt(this.value);
                                renderCart();
                            });
                        });
                        container.querySelectorAll('[data-remove]').forEach(function(btn) {
                            btn.addEventListener('click', function() {
                                cart.splice(parseInt(this.dataset.remove), 1);
                                renderCart();
                            });
                        });
                        document.getElementById('completeSale').disabled = false;
                    }
                    updateTotals();
                }

                function updateTotals() {
                    var subtotal = cart.reduce(function(sum, item) {
                        return sum + (item.price * item.quantity);
                    }, 0);
                    var paid = parseFloat(document.getElementById('amountPaid').value) || 0;
                    document.getElementById('subtotal').textContent = subtotal.toLocaleString() + ' FCFA';
                    document.getElementById('total').textContent = subtotal.toLocaleString() + ' FCFA';
                    document.getElementById('change').textContent = Math.max(0, paid - subtotal).toLocaleString() + ' FCFA';
                }

                document.getElementById('amountPaid').addEventListener('input', updateTotals);
                document.getElementById('clearCart').addEventListener('click', function() {
                    cart = [];
                    renderCart();
                });

                document.querySelectorAll('.payment-method-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        document.querySelectorAll('.payment-method-btn').forEach(function(b) {
                            b.classList.remove('active');
                        });
                        this.classList.add('active');
                        paymentMethod = this.dataset.method;
                        var mixSection = document.getElementById('mixedPaymentSection');
                        if (paymentMethod === 'mixed') {
                            mixSection.style.display = 'block';
                            var subtotal = cart.reduce(function(s, i) { return s + (i.price * i.quantity); }, 0);
                            document.getElementById('amountPaid').value = subtotal;
                        } else {
                            mixSection.style.display = 'none';
                            document.getElementById('mixCash').value = 0;
                            document.getElementById('mixCard').value = 0;
                            document.getElementById('mixMomo').value = 0;
                        }
                        updateTotals();
                    });
                });

                // Update mixed payment total on input
                ['mixCash', 'mixCard', 'mixMomo'].forEach(function(id) {
                    document.getElementById(id).addEventListener('input', function() {
                        var total = parseFloat(document.getElementById('mixCash').value || 0)
                            + parseFloat(document.getElementById('mixCard').value || 0)
                            + parseFloat(document.getElementById('mixMomo').value || 0);
                        document.getElementById('mixTotal').textContent = total.toLocaleString() + ' FCFA';
                        document.getElementById('amountPaid').value = total;
                        updateTotals();
                    });
                });

                document.getElementById('completeSale').addEventListener('click', function() {
                    if (cart.length === 0) return;
                    var subtotal = cart.reduce(function(s, i) {
                        return s + (i.price * i.quantity);
                    }, 0);
                    var paid = parseFloat(document.getElementById('amountPaid').value) || 0;
                    if (paid < subtotal && paymentMethod !== 'credit') {
                        alert('Montant paye inferieur au total.');
                        return;
                    }
                    document.getElementById('formShopId').value = document.getElementById('shopId').value;
                    document.getElementById('formCustomerId').value = document.getElementById('customerId').value;
                    document.getElementById('formPaymentMethod').value = paymentMethod;
                    document.getElementById('formAmountPaid').value = paid;

                    // Build items
                    var html = '';
                    cart.forEach(function(item, idx) {
                        html += '<input type="hidden" name="items[' + idx + '][product_id]" value="' + item.id + '">';
                        html += '<input type="hidden" name="items[' + idx + '][quantity]" value="' + item.quantity + '">';
                        html += '<input type="hidden" name="items[' + idx + '][unit_price]" value="' + item.price + '">';
                    });

                    // Mixed payment details
                    if (paymentMethod === 'mixed') {
                        var mixItems = [];
                        var mixCash = parseFloat(document.getElementById('mixCash').value || 0);
                        var mixCard = parseFloat(document.getElementById('mixCard').value || 0);
                        var mixMomo = parseFloat(document.getElementById('mixMomo').value || 0);
                        if (mixCash > 0) mixItems.push({method:'cash', amount:mixCash});
                        if (mixCard > 0) mixItems.push({method:'card', amount:mixCard});
                        if (mixMomo > 0) mixItems.push({method:'mobile_money', amount:mixMomo});
                        html += '<input type="hidden" name="payment_details" value="' + JSON.stringify(mixItems).replace(/"/g, '&quot;') + '">';
                    }

                    document.getElementById('formItemsContainer').innerHTML = html;
                    document.getElementById('saleForm').submit();
                });
            })();
        </script>
    @endpush
@endsection
