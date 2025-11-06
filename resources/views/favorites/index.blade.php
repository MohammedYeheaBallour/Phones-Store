@extends('layouts.app')

@section('title', 'المفضلة - Phones Store')

@section('content')

<!-- NAVBAR -->
<nav>
  <div class="logo" aria-label="شعار المتجر" onclick="window.location.href='/'" style="cursor: pointer;">
    <i class="fas fa-store"></i>
    <span>متجر إلكتروني</span>
  </div>
  <div class="search-bar">
    <a href="/" class="btn-cta" style="padding: 0.7rem 1.5rem; text-decoration: none; white-space: nowrap;">
      <i class="fas fa-home"></i>
      العودة للرئيسية
    </a>
  </div>
  <div class="icons" aria-label="أيقونات المتجر">
    <button class="icon-btn" id="darkModeToggle" title="الوضع الليلي">
      <i class="fas fa-moon"></i>
    </button>
    <button class="icon-btn" style="background: linear-gradient(135deg, #f43f5e 0%, #ec4899 100%); color: white;" title="المفضلة - أنت هنا">
      <i class="fas fa-heart"></i>
      <span class="badge-count" id="favoritesCount" style="background: white; color: #f43f5e;">0</span>
    </button>
    <button class="icon-btn cart-btn" title="عربة التسوق" id="cartBtn">
      <i class="fas fa-shopping-cart"></i>
      <span class="badge-count" id="cartCount">0</span>
    </button>
    <button class="icon-btn" title="الحساب">
      <i class="fas fa-user"></i>
    </button>
  </div>
</nav>

<!-- FAVORITES PAGE CONTENT -->
<div class="container" style="margin-top: 6rem; min-height: 70vh;">

  <!-- Page Header -->
  <div class="page-header" style="text-align: center; margin-bottom: 3rem; padding-top: 1rem;" data-aos="fade-down">
    <h1 style="font-size: 2.5rem; color: var(--primary-dark); margin-bottom: 0.5rem; line-height: 1.4;">
      <i class="fas fa-heart" style="color: #f43f5e;"></i>
      منتجاتي المفضلة
    </h1>
    <p style="color: var(--text-light); font-size: 1.1rem;">
      هنا ستجد جميع المنتجات التي أضفتها إلى قائمة المفضلة
    </p>
  </div>

  <!-- Favorites Counter & Actions -->
  <div class="favorites-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;" data-aos="fade-up">
    <div class="favorites-count">
      <span style="font-size: 1.2rem; color: var(--text-dark);">
        لديك <strong id="favoritesPageCount" style="color: var(--primary-color);">0</strong> منتج في المفضلة
      </span>
    </div>
    <div class="favorites-actions" style="display: flex; gap: 1rem;">
      <button class="btn-cta" id="addAllToCartBtn" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
        <i class="fas fa-shopping-cart"></i>
        أضف الكل للسلة
      </button>
      <button class="btn-cta" id="clearAllFavoritesBtn" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
        <i class="fas fa-trash"></i>
        مسح الكل
      </button>
    </div>
  </div>

  <!-- Empty State -->
  <div id="emptyFavoritesState" style="display: none; text-align: center; padding: 4rem 2rem;" data-aos="fade-up">
    <div style="font-size: 5rem; color: #e5e7eb; margin-bottom: 1rem;">
      <i class="far fa-heart"></i>
    </div>
    <h2 style="color: var(--text-dark); margin-bottom: 1rem;">لا توجد منتجات مفضلة</h2>
    <p style="color: var(--text-light); margin-bottom: 2rem; font-size: 1.1rem;">
      ابدأ بإضافة منتجاتك المفضلة لتجدها هنا
    </p>
    <a href="/" class="btn-cta">
      <i class="fas fa-arrow-right"></i>
      تصفح المنتجات
    </a>
  </div>

  <!-- Favorites Products Grid -->
  <div id="favoritesProductsGrid" class="products-grid" role="list" aria-live="polite">
    <!-- Products will be populated by JavaScript -->
  </div>

</div>

<!-- FOOTER -->
<footer data-aos="fade-up" data-aos-duration="1000" style="margin-top: 5rem;">
  <div class="footer-container" role="contentinfo" aria-label="تذييل الصفحة">
    <div class="footer-group" data-aos="fade-right" data-aos-duration="800">
      <h4>
        <i class="fas fa-store"></i>
        عن المتجر
      </h4>
      <ul>
        <li><a href="#" tabindex="0"><i class="fas fa-info-circle"></i> من نحن</a></li>
        <li><a href="#" tabindex="0"><i class="fas fa-phone"></i> اتصل بنا</a></li>
        <li><a href="#" tabindex="0"><i class="fas fa-newspaper"></i> مقالات</a></li>
      </ul>
    </div>
    <div class="footer-group" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
      <h4>
        <i class="fas fa-file-contract"></i>
        سياسات المتجر
      </h4>
      <ul>
        <li><a href="#" tabindex="0"><i class="fas fa-undo"></i> سياسة الإرجاع</a></li>
        <li><a href="#" tabindex="0"><i class="fas fa-gavel"></i> الشروط و الأحكام</a></li>
        <li><a href="#" tabindex="0"><i class="fas fa-user-shield"></i> سياسة الاستخدام</a></li>
      </ul>
    </div>
    <div class="footer-group newsletter" aria-label="الاشتراك في النشرة البريدية" data-aos="fade-left" data-aos-duration="800" data-aos-delay="400">
      <h4>
        <i class="fas fa-envelope"></i>
        اشترك في نشرتنا البريدية
      </h4>
      <form id="newsletterForm">
        <div class="newsletter-input-group">
          <input type="email" id="subscriberEmail" placeholder="أدخل بريدك الإلكتروني" aria-label="أدخل بريدك الإلكتروني" required />
          <button type="submit" aria-label="اشترك الآن">
            <i class="fas fa-paper-plane"></i>
            اشترك الآن
          </button>
        </div>
      </form>
      <p>
        <i class="fas fa-leaf"></i>
        سوف نصلكم بأحدث العروض والمنتجات
      </p>
    </div>
  </div>
  <div class="footer-bottom" data-aos="fade-up" data-aos-duration="800" data-aos-delay="600">
    <p>&copy; 2025  متجر إلكتروني لبيع الهواتف. جميع الحقوق محفوظة لفريق Webura.</p>
  </div>
</footer>

<!-- Toast Notification Container -->
<div id="toast-container" class="toast-container"></div>

<!-- Cart Sidebar -->
<div id="cartSidebar" class="cart-sidebar">
  <div class="cart-header">
    <h3>
      <i class="fas fa-shopping-cart"></i>
      سلة التسوق
    </h3>
    <button class="close-cart" id="closeCart">
      <i class="fas fa-times"></i>
    </button>
  </div>
  <div class="cart-items" id="cartItems">

  </div>
  <div class="cart-footer">
    <div class="cart-total">
      <span>المجموع: </span>
      <span id="cartTotal">0.00 $</span>
    </div>
    <button class="btn-cta checkout-btn" id="checkoutBtn">
      <i class="fas fa-credit-card"></i>
      تنفيذ الشراء
    </button>
  </div>
</div>

<script>
  // Load favorites page specific script
  document.addEventListener('DOMContentLoaded', function() {
    loadFavoritesPage();
  });

  function loadFavoritesPage() {
    const favorites = JSON.parse(localStorage.getItem('favorites')) || [];
    const favoritesGrid = document.getElementById('favoritesProductsGrid');
    const emptyState = document.getElementById('emptyFavoritesState');
    const pageCount = document.getElementById('favoritesPageCount');
    const favoritesCount = document.getElementById('favoritesCount');

    // Update counts
    if (pageCount) pageCount.textContent = favorites.length;
    if (favoritesCount) favoritesCount.textContent = favorites.length;

    if (favorites.length === 0) {
      // Show empty state
      if (emptyState) emptyState.style.display = 'block';
      if (favoritesGrid) favoritesGrid.style.display = 'none';
      document.querySelector('.favorites-header').style.display = 'none';
    } else {
      // Show products
      if (emptyState) emptyState.style.display = 'none';
      if (favoritesGrid) {
        favoritesGrid.style.display = 'grid';
        favoritesGrid.innerHTML = favorites.map(product => `
          <div class="product-card" data-aos="fade-up" data-product-id="${product.id}">
            <div class="product-image">
              <button class="favorite-btn active"
                      data-product-id="${product.id}"
                      data-product-name="${product.name}"
                      data-product-price="${product.price}"
                      data-product-img="${product.img}"
                      data-product-desc="${product.description || ''}"
                      title="إزالة من المفضلة">
                <i class="fas fa-heart"></i>
              </button>
              <img src="${product.img}" alt="${product.name}" loading="lazy"/>
            </div>
            <div class="product-info">
              <h4>${product.name}</h4>
              <p>${product.description || ''}</p>
              <div class="price">${parseFloat(product.price).toLocaleString()} $</div>
              <button class="btn-cta add-to-cart-single"
                      data-id="${product.id}"
                      data-name="${product.name}"
                      data-price="${product.price}"
                      data-img="${product.img}">
                <i class="fas fa-shopping-cart"></i>
                أضف للسلة
              </button>
            </div>
          </div>
        `).join('');
      }
    }

    // Add to cart single product
    document.querySelectorAll('.add-to-cart-single').forEach(btn => {
      btn.addEventListener('click', async function() {
        const product = {
          id: Number(this.getAttribute('data-id')),
          name: this.getAttribute('data-name'),
          price: Number(this.getAttribute('data-price')),
          img: this.getAttribute('data-img')
        };

        try {
          const res = await fetch(`/cart/add/${product.id}`, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ quantity: 1 })
          });

          if (typeof addToCart === 'function') {
            addToCart(product);
          }
        } catch (err) {
          console.error(err);
        }
      });
    });

    // Add all to cart
    const addAllBtn = document.getElementById('addAllToCartBtn');
    if (addAllBtn) {
      addAllBtn.addEventListener('click', async function() {
        if (favorites.length === 0) return;

        for (const product of favorites) {
          try {
            await fetch(`/cart/add/${product.id}`, {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({ quantity: 1 })
            });

            if (typeof addToCart === 'function') {
              addToCart(product);
            }
          } catch (err) {
            console.error(err);
          }
        }

        if (typeof showToast === 'function') {
          showToast('تم إضافة جميع المنتجات للسلة!', 'success');
        }
      });
    }

    // Clear all favorites
    const clearAllBtn = document.getElementById('clearAllFavoritesBtn');
    if (clearAllBtn) {
      clearAllBtn.addEventListener('click', function() {
        if (confirm('هل أنت متأكد من حذف جميع المنتجات المفضلة؟')) {
          localStorage.setItem('favorites', JSON.stringify([]));
          location.reload();
        }
      });
    }
  }
</script>

@endsection
