@extends('layouts.app')

@section('title', 'Phones Store')

@section('content')

<!-- NAVBAR -->
<nav>
  <div class="logo" aria-label="شعار المتجر">
    <i class="fas fa-store"></i>
    <span>متجر إلكتروني</span>
  </div>
  <div class="search-bar">
    <input type="search" aria-label="بحث عن منتجات" placeholder="ابحث في المتجر..." />
    <i class="fas fa-search search-icon" aria-hidden="true"></i>
  </div>
  <div class="icons" aria-label="أيقونات المتجر">
    <button class="icon-btn" id="darkModeToggle" title="الوضع الليلي">
      <i class="fas fa-moon"></i>
    </button>
    <button class="icon-btn" id="favoritesBtn" title="المفضلة">
      <i class="fas fa-heart"></i>
      <span class="badge-count" id="favoritesCount">0</span>
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

<!-- BANNERS -->
<div class="container">
  <div class="banners" role="region" aria-label="الإعلانات الترويجية">
    <div class="banner" data-aos="fade-right" data-aos-duration="800">
      <div class="banner-text">
        <h3>  ألوان جذابة وأداء لا يضاهى</h3>
        <p>اطلب الآن وتمتع بعالمًا من الإمكانيات.</p>
        <button class="btn-cta" aria-label="اطلب الآن - حماية البيت">
          <span>اطلب الآن</span>
          <i class="fas fa-arrow-left"></i>
        </button>
      </div>
      <img src="https://tse4.mm.bing.net/th/id/OIP.LyiqqT1_bHMKdhA6X1oF6QHaEK?rs=1&pid=ImgDetMain&o=7&rm=3" alt="أجهزة الحماية للمنزل" loading="lazy" />
    </div>
    <div class="banner" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
      <div class="banner-text">
        <h3>جدد هاتفك الآن</h3>
        <p>اطلب الآن واستمتع بأحدث تقنيات الهواتف بين يديك.</p>
        <button class="btn-cta" aria-label="اطلب الآن - العطر">
          <span>اطلب الآن</span>
          <i class="fas fa-arrow-left"></i>
        </button>
      </div>
      <img src="https://th.bing.com/th/id/OIP.rOnKrvvUYNcqLFmTispI3gHaEK?w=287&h=180&c=7&r=0&o=7&dpr=1.3&pid=1.7&rm=3" alt="عطر فاخر" loading="lazy" />
    </div>
  </div>
</div>

<!-- PRODUCTS SECTION - مقترحة لك -->
<section class="container" aria-labelledby="recommended-title" data-aos="fade-up" data-aos-duration="1000">
  <h2 id="recommended-title" data-aos="fade-right" data-aos-duration="800">مقترحة لك</h2>

  <div class="filter-bar" aria-label="فلترة المنتجات" data-aos="fade-left" data-aos-duration="800" data-aos-delay="200">
    <label for="sortSelect">ترتيب حسب:</label>
    <select id="sortSelect" aria-controls="products-grid">
      <option value="default">الافتراضي</option>
      <option value="price-asc">السعر من الأقل للأعلى</option>
      <option value="price-desc">السعر من الأعلى للأقل</option>
    </select>
  </div>

  <div id="products-grid" class="products-grid" role="list" aria-live="polite" data-server-rendered="1">
    @forelse(($products ?? []) as $product)
      <div class="product-card" data-server-item="1" data-product-id="{{ $product->id }}">
        <div class="product-image">
          <button class="favorite-btn"
                  data-product-id="{{ $product->id }}"
                  data-product-name="{{ $product->name }}"
                  data-product-price="{{ $product->price }}"
                  data-product-img="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}"
                  data-product-desc="{{ $product->description }}"
                  title="إضافة إلى المفضلة">
            <i class="far fa-heart"></i>
          </button>
          <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}" alt="{{ $product->name }}" loading="lazy"/>
        </div>
        <div class="product-info">
          <h4>{{ $product->name }}</h4>
          <p>{{ \Illuminate\Support\Str::limit($product->description, 60) }}</p>
          <div class="price">{{ number_format($product->price, 2) }} $</div>
          <button class="btn-cta add-to-cart-btn"
                  data-id="{{ $product->id }}"
                  data-name="{{ $product->name }}"
                  data-price="{{ $product->price }}"
                  data-img="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}">
            <i class="fas fa-shopping-cart"></i>
            اضف للسلة
          </button>
        </div>
      </div>
    @empty
      <p>لا توجد منتجات حالياً</p>
    @endforelse
  </div>
</section>

<!-- CATEGORIES SECTION -->
<section class="container" aria-label="أقسام المنتجات الشائعة" data-aos="fade-up" data-aos-duration="1000">
  <div class="categories">
    <div class="category-card" tabindex="0" aria-label="أجهزة اللياقة والرياضة" data-aos="zoom-in" data-aos-duration="800">
      <i class="fas fa-dumbbell category-icon"></i>
      <h4>أجهزة اللياقة والرياضة</h4>
      <p>تسوق الآن</p>
    </div>
    <div class="category-card" tabindex="0" aria-label="إكسسوارات الألعاب" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="200">
      <i class="fas fa-gamepad category-icon"></i>
      <h4>إكسسوارات الألعاب</h4>
      <p>تسوق الآن</p>
    </div>
    <div class="category-card" tabindex="0" aria-label="إكسسوارات الإلكترونيات" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="400">
      <i class="fas fa-laptop category-icon"></i>
      <h4>إكسسوارات الإلكترونيات</h4>
      <p>تسوق الآن</p>
    </div>
  </div>
</section>

<!-- FLASH SALE -->
<section class="container" aria-label="عروض الفلاش" data-aos="fade-up" data-aos-duration="1000">
  <div class="flash-sale" role="region" aria-live="assertive">
    <div class="text">
      <i class="fas fa-bolt"></i>
      Flash Sale - لحق حالك واغتنمها الآن!
    </div>
    <button class="btn-cta" id="showDealsBtn" aria-controls="products-grid">
      <span>عرض جميع العروض</span>
      <i class="fas fa-arrow-left"></i>
    </button>
  </div>

  <div class="products-grid" role="list" aria-live="polite" data-server-rendered="1">
    @php($flash = ($products ?? collect())->where('is_flash_sale', true)->take(2))
    @forelse($flash as $product)
      <div class="product-card flash-sale" data-server-item="1" data-product-id="{{ $product->id }}">
        <span class="badge">خصم</span>
        <div class="product-image">
          <button class="favorite-btn"
                  data-product-id="{{ $product->id }}"
                  data-product-name="{{ $product->name }}"
                  data-product-price="{{ $product->price }}"
                  data-product-img="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}"
                  data-product-desc="{{ $product->description }}"
                  title="إضافة إلى المفضلة">
            <i class="far fa-heart"></i>
          </button>
          <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}" alt="{{ $product->name }}" loading="lazy">
        </div>
        <div class="product-info">
          <h3>{{ $product->name }}</h3>
          <p>{{ \Illuminate\Support\Str::limit($product->description, 60) }}</p>
          <div class="price">{{ number_format($product->price, 2) }} $</div>
          <button class="btn-cta add-to-cart-btn"
                  data-id="{{ $product->id }}"
                  data-name="{{ $product->name }}"
                  data-price="{{ $product->price }}"
                  data-img="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}">
            <i class="fas fa-shopping-cart"></i>
            اضف للسلة
          </button>
        </div>
      </div>
    @empty
      <p>لا توجد عروض فلاش حالياً</p>
    @endforelse
  </div>
</section>

<!-- BEST SELLERS SECTION -->
<section class="container" aria-labelledby="best-sellers-title" data-aos="fade-up" data-aos-duration="1000">
  <h2 id="best-sellers-title" data-aos="fade-right" data-aos-duration="800">المنتجات الأفضل مبيعًا</h2>
  <div class="products-grid" id="best-sellers" role="list" aria-live="polite" data-server-rendered="1">
    @forelse(($bestSellers ?? []) as $product)
      <div class="product-card" data-server-item="1" data-product-id="{{ $product->id }}">
        <div class="product-image">
          <button class="favorite-btn"
                  data-product-id="{{ $product->id }}"
                  data-product-name="{{ $product->name }}"
                  data-product-price="{{ $product->price }}"
                  data-product-img="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}"
                  data-product-desc="{{ $product->description }}"
                  title="إضافة إلى المفضلة">
            <i class="far fa-heart"></i>
          </button>
          <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}" alt="{{ $product->name }}" loading="lazy">
        </div>
        <div class="product-info">
          <h3>{{ $product->name }}</h3>
          <p>{{ \Illuminate\Support\Str::limit($product->description, 60) }}</p>
          <div class="price">{{ number_format($product->price, 2) }} $</div>
          <button class="btn-cta add-to-cart-btn"
                  data-id="{{ $product->id }}"
                  data-name="{{ $product->name }}"
                  data-price="{{ $product->price }}"
                  data-img="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/400x300?text=No+Image' }}">
            <i class="fas fa-shopping-cart"></i>
            اضف للسلة
          </button>
        </div>
      </div>
    @empty
      <p>لا توجد منتجات حالياً</p>
    @endforelse
  </div>
</section>


<!-- CUSTOMER REVIEWS -->
<section class="container reviews" aria-labelledby="customer-reviews-title" data-aos="fade-up" data-aos-duration="1000">
  <h2 id="customer-reviews-title" data-aos="fade-right" data-aos-duration="800">
    <i class="fas fa-heart"></i>
    ماذا يقول عملاؤنا
  </h2>
  <div class="review-cards" id="review-list" role="list">
    <!-- سيتم ملؤها بالجاڤا سكريبت -->
  </div>

  <!-- Add Review Form -->
  <form class="review-form" id="reviewForm" aria-label="نموذج إضافة تقييم" data-aos="fade-up" data-aos-duration="1000" data-aos-delay="200">
    <h3>
      <i class="fas fa-star"></i>
      أضف تقييمك
    </h3>
    <div class="form-group">
      <label for="reviewerName">الاسم</label>
      <input type="text" name="name" id="reviewerName" placeholder="اسمك" required aria-required="true" />
    </div>
    <div class="form-group">
      <label for="reviewRating">التقييم</label>
      <select name="rating" id="reviewRating" required aria-required="true" aria-label="تقييم من 1 إلى 5 نجوم">
        <option value="" disabled selected>تقييم النجوم</option>
        <option value="5">5 - ممتاز</option>
        <option value="4">4 - جيد جداً</option>
        <option value="3">3 - جيد</option>
        <option value="2">2 - مقبول</option>
        <option value="1">1 - ضعيف</option>
      </select>
    </div>
    <div class="form-group">
      <label for="reviewComment">التعليق</label>
      <textarea name="comment" id="reviewComment" placeholder="شاركنا رأيك..." required aria-required="true"></textarea>
    </div>
    <button type="submit" aria-label="إرسال التقييم">
      <i class="fas fa-paper-plane"></i>
      إرسال التقييم
    </button>
  </form>
</section>

<!-- STORE FEATURES -->
<section class="container" aria-label="ميزات المتجر" data-aos="fade-up" data-aos-duration="1000">
  <div class="features-grid">
    <div class="feature-card" data-aos="zoom-in" data-aos-duration="800">
      <i class="fas fa-certificate"></i>
      <h4>منتجات أصلية</h4>
      <p>نضمن جودة وأصالة جميع المنتجات.</p>
    </div>
    <div class="feature-card" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="200">
      <i class="fas fa-shield-alt"></i>
      <h4>كفالة سنوية</h4>
      <p>تغطية كاملة لجميع مشترياتك.</p>
    </div>
    <div class="feature-card" data-aos="zoom-in" data-aos-duration="800" data-aos-delay="400">
      <i class="fas fa-credit-card"></i>
      <h4>طرق دفع آمنة</h4>
      <p>نقدم طرق دفع موثوقة وسريعة.</p>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer data-aos="fade-up" data-aos-duration="1000">
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

@endsection
