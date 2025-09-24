

  let cart = JSON.parse(localStorage.getItem('cart')) || [];
  let favorites = JSON.parse(localStorage.getItem('favorites')) || [];
  let isDarkMode = localStorage.getItem('darkMode') === 'true';

  document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
  });

  function initializeApp() {

    AOS.init({
      duration: 800,
      easing: 'ease-in-out',
      once: true,
      offset: 100
    });


    initializeDarkMode();


    updateCartUI();

    syncCartFromServer();


    updateFavoritesUI();


    initializeEventListeners();
  }


  function initializeDarkMode() {
    const darkModeToggle = document.getElementById('darkModeToggle');
    const body = document.body;

    if (isDarkMode) {
      body.setAttribute('data-theme', 'dark');
      darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
    }

    darkModeToggle.addEventListener('click', function() {
      isDarkMode = !isDarkMode;
      localStorage.setItem('darkMode', isDarkMode);

      if (isDarkMode) {
        body.setAttribute('data-theme', 'dark');
        darkModeToggle.innerHTML = '<i class="fas fa-sun"></i>';
        showToast('تم تفعيل الوضع الليلي', 'success');
      } else {
        body.removeAttribute('data-theme');
        darkModeToggle.innerHTML = '<i class="fas fa-moon"></i>';
        showToast('تم إلغاء الوضع الليلي', 'success');
      }
    });
  }


  function showToast(message, type = 'success') {
    const toastContainer = document.getElementById('toast-container');
    const toast = document.createElement('div');

    const icons = {
      success: 'fas fa-check-circle',
      error: 'fas fa-exclamation-circle',
      warning: 'fas fa-exclamation-triangle'
    };

    toast.className = `toast ${type}`;
    toast.innerHTML = `
      <i class="toast-icon ${icons[type]}"></i>
      <span>${message}</span>
    `;

    toastContainer.appendChild(toast);


    setTimeout(() => toast.classList.add('show'), 100);


    setTimeout(() => {
      toast.classList.remove('show');
      setTimeout(() => toastContainer.removeChild(toast), 300);
    }, 4000);
  }


  function addToCart(product, cartItemId = null) {
    const existingItem = cart.find(item => item.id === product.id);

    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      cart.push({
        id: product.id,
        name: product.name,
        price: product.price,
        img: product.img,
        quantity: 1,
        cartItemId: cartItemId
      });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    showToast(`تم إضافة ${product.name} إلى السلة`, 'success');
  }

  async function removeFromCart(productId, cartItemId = null) {
    if (cartItemId) {
      try {
        await fetch(`/cart/${cartItemId}`, {
          method: 'DELETE',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
      } catch (_) {}
    }
    cart = cart.filter(item => item.id !== productId);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();

    syncCartFromServer();
    showToast('تم حذف المنتج من السلة', 'success');
  }

  async function updateCartQuantity(productId, newQuantity) {
    const item = cart.find(item => item.id === productId);
    if (item) {
      if (newQuantity <= 0) {
        await removeFromCart(productId, item.cartItemId || null);
      } else {
        if (item.cartItemId) {
          try {
            await fetch(`/cart/${item.cartItemId}`, {
              method: 'PATCH',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
              },
              body: JSON.stringify({ quantity: newQuantity })
            });
          } catch (_) {}
        }
        item.quantity = newQuantity;
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();

        syncCartFromServer();
      }
    }
  }

  function updateCartUI() {
    const cartCount = document.getElementById('cartCount');
    const cartItems = document.getElementById('cartItems');
    const cartTotal = document.getElementById('cartTotal');


    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    cartCount.textContent = totalItems;


    if (cart.length === 0) {
      cartItems.innerHTML = '<div class="empty-cart">السلة فارغة</div>';
      cartTotal.textContent = '0.00 $';
    } else {
      cartItems.innerHTML = cart.map(item => `
        <div class="cart-item">
          <img src="${item.img}" alt="${item.name}" />
          <div class="cart-item-info">
            <div class="cart-item-name">${item.name}</div>
            <div class="cart-item-price">${item.price.toLocaleString()} $</div>
            <div class="cart-item-controls">
              <button class="quantity-btn" onclick="updateCartQuantity(${item.id}, ${item.quantity - 1})">-</button>
              <span class="quantity-display">${item.quantity}</span>
              <button class="quantity-btn" onclick="updateCartQuantity(${item.id}, ${item.quantity + 1})">+</button>
              <button class="remove-item" onclick="removeFromCart(${item.id}, ${item.cartItemId ? item.cartItemId : 'null'})">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      `).join('');


      const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
      cartTotal.textContent = `${total.toLocaleString()} $`;
    }
  }


  async function syncCartFromServer() {
    try {
      const res = await fetch('/api/cart', { headers: { 'Accept': 'application/json' } });
      if (!res.ok) return;
      const data = await res.json();
      cart = (data.items || []).filter(i => i.product).map(i => ({
        id: i.product.id,
        name: i.product.name,
        price: Number(i.product.price),
        img: i.product.image ? `/storage/${i.product.image}` : 'https://via.placeholder.com/400x300?text=No+Image',
        quantity: i.quantity
      }));
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartUI();
    } catch (e) {

    }
  }

  function initializeEventListeners() {

    const cartBtn = document.getElementById('cartBtn');
    const cartSidebar = document.getElementById('cartSidebar');
    const closeCart = document.getElementById('closeCart');

    cartBtn.addEventListener('click', () => {
      cartSidebar.classList.add('open');
    });

    closeCart.addEventListener('click', () => {
      cartSidebar.classList.remove('open');
    });


    document.addEventListener('click', (e) => {
      if (!cartSidebar.contains(e.target) && !cartBtn.contains(e.target)) {
        cartSidebar.classList.remove('open');
      }
    });


    const checkoutBtn = document.getElementById('checkoutBtn');
    checkoutBtn.addEventListener('click', () => {
      if (cart.length === 0) {
        showToast('السلة فارغة', 'warning');
        return;
      }


      showToast('جاري معالجة الطلب...', 'success');
      setTimeout(() => {
        cart = [];
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
        cartSidebar.classList.remove('open');
        showToast('تم تنفيذ الطلب بنجاح!', 'success');
      }, 2000);
    });


    document.addEventListener('click', async (e) => {
      const btn = e.target.closest('.add-to-cart-btn');
      if (!btn) return;
      e.preventDefault();
      const product = {
        id: Number(btn.getAttribute('data-id')),
        name: btn.getAttribute('data-name'),
        price: Number(btn.getAttribute('data-price')),
        img: btn.getAttribute('data-img')
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
        let cartItemId = null;
        if (res.ok) {
          const data = await res.json();
          cartItemId = data?.item?.id ?? null;
        }

        addToCart(product, cartItemId);
        syncCartFromServer();
      } catch (err) {
        console.error(err);
        showToast('تعذر إضافة المنتج للسلة', 'error');
      }
    });
  }


  function toggleFavorite(productId) {
    const product = products.find(p => p.id === productId);
    const existingIndex = favorites.findIndex(fav => fav.id === productId);

    if (existingIndex > -1) {
      favorites.splice(existingIndex, 1);
      showToast('تم إزالة المنتج من المفضلة', 'success');
    } else {
      favorites.push(product);
      showToast('تم إضافة المنتج إلى المفضلة', 'success');
    }

    localStorage.setItem('favorites', JSON.stringify(favorites));
    updateFavoritesUI();
  }

  function updateFavoritesUI() {
    const favoriteCount = document.querySelector('.icon-btn[title="المفضلة"] .badge-count');
    favoriteCount.textContent = favorites.length;
  }


  const products = [
    {
      id: 1,
      name: "Samsung Galaxy S23 Ultra 256 GB",
      price: 1780.0,
      img: "https://images.unsplash.com/photo-1672702281813-2737a5e33016?auto=format&fit=crop&w=400&q=80",
      description: "هاتف ذكي حديث مع شاشة كبيرة وكاميرا فائقة.",
      badge: "جديد"
    },
    {
      id: 2,
      name: "Samsung Galaxy S22+ Ultra 256 GB",
      price: 1780.0,
      img: "https://images.unsplash.com/photo-1665684874166-afc7ec0cd944?auto=format&fit=crop&w=400&q=80",
      description: "هاتف قوي مع أداء فائق وتحديثات مستمرة.",
    },
    {
      id: 3,
      name: "Samsung Galaxy S22 Ultra 256 GB",
      price: 1780.0,
      img: "https://images.unsplash.com/photo-1649787306512-55d9e74194f5?auto=format&fit=crop&w=400&q=80",
      description: "تصميم أنيق مع كاميرا بدقة عالية.",
      badge: "جديد"
    },
    {
      id: 4,
      name: "Eufy stum LED standby SES",
      price: 1780.0,
      img: "https://images.unsplash.com/photo-1609784459397-7e20856f9a54?auto=format&fit=crop&w=400&q=80",
      description: "جهاز تحكم ذكي لجهاز الإضاءة.",
      badge: "خصم"
    },
    {
      id: 5,
      name: "Samsung Galaxy S22 Ultra 256 GB",
      price: 1780.0,
      img: "https://images.unsplash.com/photo-1649787306512-55d9e74194f5?auto=format&fit=crop&w=400&q=80",
      description: "تصميم أنيق مع كاميرا بدقة عالية.",
    },
    {
      id: 6,
      name: "Samsung 3D printer Sola S12",
      price: 1860.0,
      img: "https://images.unsplash.com/photo-1637883277455-430185d7ca1a?auto=format&fit=crop&w=400&q=80",
      description: "طابعة ثلاثية الأبعاد عالية الأداء.",
    },
  ];


  const reviews = [
    {
      id: 1,
      name: "باسم",
      rating: 5,
      comment: "منتجات عالية الجودة وخدمة سريعة.",
      date: "2025, Feb Sat",
      avatar: "https://randomuser.me/api/portraits/men/32.jpg"
    },
    {
      id: 2,
      name: "أحمد",
      rating: 5,
      comment: "تجربة شراء ممتازة، أوصي به بشدة.",
      date: "2025, Feb Sat",
      avatar: "https://randomuser.me/api/portraits/men/75.jpg"
    },
    {
      id: 3,
      name: "أنوم",
      rating: 5,
      comment: "good and fast",
      date: "2025, Feb Sat",
      avatar: "https://randomuser.me/api/portraits/women/44.jpg"
    },
    {
      id: 4,
      name: "نكت",
      rating: 5,
      comment: "أداء ممتاز وخدمة الدعم رائعة.",
      date: "2025, Feb Sat",
      avatar: "https://randomuser.me/api/portraits/women/68.jpg"
    },
  ];


  function generateProductCard(product) {
    const badgeHtml = product.badge
      ? `<div class="badge" aria-label="ملصق المنتج">${product.badge}</div>`
      : "";

    const isFavorite = favorites.some(fav => fav.id === product.id);
    const favoriteIcon = isFavorite ? 'fas fa-heart' : 'far fa-heart';

    return `
      <div class="product-card" role="listitem" tabindex="0" aria-label="منتج: ${product.name}, السعر: ${product.price} دولار">
        <div class="product-image">
          ${badgeHtml}
          <button class="favorite-btn" onclick="toggleFavorite(${product.id})" title="${isFavorite ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة'}">
            <i class="${favoriteIcon}"></i>
          </button>
          <img src="${product.img}" alt="${product.name}" loading="lazy"/>
        </div>
        <div class="product-info">
          <h4>${product.name}</h4>
          <p>${product.description || ""}</p>
          <div class="price">${product.price.toLocaleString()} $</div>
          <button class="btn-cta" aria-label="أضف ${product.name} إلى السلة" onclick="addToCart(${JSON.stringify(product).replace(/"/g, '&quot;')})">
            <i class="fas fa-shopping-cart"></i>
            اضف للسلة
          </button>
        </div>
      </div>`;
  }


  const productsGrid = document.getElementById("products-grid");
  const bestSellersGrid = document.getElementById("best-sellers");


  function displayProducts(productList, container) {
    const serverRendered = container && container.getAttribute('data-server-rendered') === '1';
    if (serverRendered && container.querySelector('[data-server-item]')) {
      return;
    }
    container.innerHTML = productList.map(generateProductCard).join("");
  }

  if (productsGrid) displayProducts(products, productsGrid);
  if (bestSellersGrid) displayProducts(products.slice(0, 6), bestSellersGrid);


  document.getElementById("sortSelect").addEventListener("change", function () {
    let sortedProducts = [...products];
    if (this.value === "price-asc") {
      sortedProducts.sort((a, b) => a.price - b.price);
    } else if (this.value === "price-desc") {
      sortedProducts.sort((a, b) => b.price - a.price);
    }
    displayProducts(sortedProducts, productsGrid);
  });

  document.getElementById("showDealsBtn").addEventListener("click", () => {

    displayProducts(products, productsGrid);
    window.scrollTo({ top: productsGrid.offsetTop - 70, behavior: "smooth" });
  });


  const reviewList = document.getElementById("review-list");
  function generateReviewCard(review) {
    return `
      <article class="review-card" role="listitem" tabindex="0" aria-label="مراجعة من ${review.name}: تقييم ${review.rating} نجوم">
        <div class="avatar"><img src="${review.avatar}" alt="صورة ${review.name}" loading="lazy" /></div>
        <div class="name">${review.name}</div>
        <div class="rating">${"★".repeat(review.rating)}${"☆".repeat(5 - review.rating)}</div>
        <p class="comment">${review.comment}</p>
        <div class="date">${review.date}</div>
      </article>`;
  }

  function displayReviews() {
    reviewList.innerHTML = reviews.map(generateReviewCard).join("");
  }
  displayReviews();


  const reviewForm = document.getElementById("reviewForm");
  reviewForm.addEventListener("submit", e => {
    e.preventDefault();
    const name = reviewForm.reviewerName.value.trim();
    const rating = parseInt(reviewForm.reviewRating.value);
    const comment = reviewForm.reviewComment.value.trim();

    if (name && rating && comment) {
      const newReview = {
        id: Date.now(),
        name,
        rating,
        comment,
        date: new Date().toLocaleDateString("ar-EG", { year: 'numeric', month: 'short', day: 'numeric' }),
        avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=61b64d&color=fff&size=128`
      };
      reviews.unshift(newReview);
      displayReviews();
      reviewForm.reset();
      showToast("تم إضافة تقييمك بنجاح، شكراً لك!", 'success');
    } else {
      showToast("يرجى تعبئة جميع الحقول بشكل صحيح.", 'error');
    }
  });


  const newsletterForm = document.getElementById("newsletterForm");
  newsletterForm.addEventListener("submit", e => {
    e.preventDefault();
    const emailInput = document.getElementById("subscriberEmail");
    const email = emailInput.value.trim();
    if (email && validateEmail(email)) {
      showToast("شكراً لاشتراكك في نشرتنا البريدية!", 'success');
      emailInput.value = "";
    } else {
      showToast("يرجى إدخال بريد إلكتروني صالح.", 'error');
    }
  });

  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
