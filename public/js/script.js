

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
      // touch timestamp so recently-updated items move to top
      existingItem.addedAt = Date.now();
    } else {
      // Insert new items at the beginning so recently-added products appear at the top
      cart.unshift({
        id: product.id,
        name: product.name,
        price: product.price,
        img: product.img,
        quantity: 1,
        addedAt: Date.now(),
        cartItemId: cartItemId
      });
    }

    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    showToast(`تم إضافة ${product.name} إلى السلة`, 'success');
  }

  async function removeFromCart(productId, cartItemId = null) {
    try {
      // If we don't have cartItemId, try to discover it from the server
      if (!cartItemId) {
        try {
          const res = await fetch('/api/cart', { headers: { 'Accept': 'application/json' } });
          if (res.ok) {
            const data = await res.json();
            const found = (data.items || []).find(i => i.product && i.product.id === productId);
            if (found) cartItemId = found.id;
          }
        } catch (_) {}
      }

      if (cartItemId) {
        const res = await fetch(`/cart/${cartItemId}`, {
          method: 'DELETE',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          }
        });
        if (!res.ok) {
          showToast('تعذر حذف المنتج من السلة', 'error');
          return;
        }
      }

      // Optimistically update local cart and UI
      cart = cart.filter(item => item.id !== productId);
      localStorage.setItem('cart', JSON.stringify(cart));
      updateCartUI();
      showToast('تم حذف المنتج من السلة', 'success');

      // Refresh from server to keep in sync (sidebar remains open)
      syncCartFromServer();
    } catch (e) {
      showToast('حدث خطأ أثناء الحذف', 'error');
    }
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
                'Accept': 'application/json',
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
      // Render newest items first based on addedAt timestamp
      const sorted = [...cart].sort((a, b) => (b.addedAt || 0) - (a.addedAt || 0));
      cartItems.innerHTML = sorted.map(item => `
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
        // Preserve any existing addedAt timestamps for matching products
        const previous = [...cart];
        cart = (data.items || []).filter(i => i.product).map(i => {
          const pid = i.product.id;
          const prev = previous.find(p => p.id === pid);
          return {
            id: pid,
            name: i.product.name,
            price: Number(i.product.price),
            img: i.product.image ? `/storage/${i.product.image}` : 'https://via.placeholder.com/400x300?text=No+Image',
            quantity: i.quantity,
            cartItemId: i.id,
            addedAt: prev ? prev.addedAt : Date.now()
          };
        });
        // Ensure a deterministic display order (newest first by addedAt)
        cart.sort((a, b) => (b.addedAt || 0) - (a.addedAt || 0));
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
    } catch (e) {

    }
  }

  function initializeEventListeners() {
    // Initialize search functionality
    initializeSearch();

    const cartBtn = document.getElementById('cartBtn');
    const cartSidebar = document.getElementById('cartSidebar');
    const closeCart = document.getElementById('closeCart');

    cartBtn.addEventListener('click', () => {
      cartSidebar.classList.add('open');
    });

    closeCart.addEventListener('click', () => {
      cartSidebar.classList.remove('open');
    });

    // Favorites button - Navigate to favorites page
    const favoritesBtn = document.getElementById('favoritesBtn');
    if (favoritesBtn) {
      favoritesBtn.addEventListener('click', () => {
        // Navigate to favorites page
        window.location.href = '/favorites';
      });
    }

    // Banner CTA buttons -> scroll to products or best-sellers
    document.querySelectorAll('.banners .btn-cta').forEach((btn, idx) => {
      btn.addEventListener('click', (e) => {
        e.preventDefault();
        const productsContainer = document.getElementById('products-grid') || document.getElementById('best-sellers');
        if (productsContainer) {
          productsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
          showToast('تصفح المنتجات المقترحة لك', 'success');
        }
      });
    });

    // Category cards -> scroll to products and hint
    document.querySelectorAll('.category-card').forEach(card => {
      card.addEventListener('click', (e) => {
        e.preventDefault();
        const productsContainer = document.getElementById('products-grid');
        if (productsContainer) {
          productsContainer.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
        const titleEl = card.querySelector('h4');
        const title = titleEl ? titleEl.textContent.trim() : 'القسم';
        showToast(`سيتم عرض قسم "${title}" قريبًا`, 'warning');
      });
    });

    // Account icon -> navigate to account page
    const accountBtn = document.querySelector('.icons .icon-btn[title="الحساب"]');
    if (accountBtn) {
      accountBtn.addEventListener('click', (e) => {
        e.preventDefault();
        window.location.href = '/account';
      });
    }

    // Footer placeholder links (#) -> prevent jump and show toast
    document.querySelectorAll('footer a[href="#"]').forEach(a => {
      a.addEventListener('click', (e) => {
        e.preventDefault();
        const text = a.textContent.trim() || 'الرابط';
        showToast(`"${text}" سيكون متاحًا قريبًا`, 'warning');
      });
    });

    // Keep sidebar functionality for backward compatibility
    const favoritesSidebar = document.getElementById('favoritesSidebar');
    const closeFavorites = document.getElementById('closeFavorites');

    if (closeFavorites && favoritesSidebar) {
      closeFavorites.addEventListener('click', () => {
        favoritesSidebar.classList.remove('open');
      });
    }

    // Clear favorites button
    const clearFavoritesBtn = document.getElementById('clearFavoritesBtn');
    if (clearFavoritesBtn) {
      clearFavoritesBtn.addEventListener('click', () => {
        if (confirm('هل أنت متأكد من حذف جميع المفضلة؟')) {
          favorites = [];
          localStorage.setItem('favorites', JSON.stringify(favorites));
          updateFavoritesUI();
          updateAllFavoriteButtons();
          showToast('تم حذف جميع المفضلة', 'success');
        }
      });
    }


    document.addEventListener('click', (e) => {
      if (!cartSidebar.contains(e.target) && !cartBtn.contains(e.target)) {
        cartSidebar.classList.remove('open');
      }
      if (favoritesSidebar && !favoritesSidebar.contains(e.target)) {
        favoritesSidebar.classList.remove('open');
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

    // Favorite buttons event listener
    document.addEventListener('click', (e) => {
      const favoriteBtn = e.target.closest('.favorite-btn');
      if (!favoriteBtn) return;

      e.preventDefault();
      e.stopPropagation();

      const productId = parseInt(favoriteBtn.getAttribute('data-product-id'));
      const productData = {
        name: favoriteBtn.getAttribute('data-product-name'),
        price: favoriteBtn.getAttribute('data-product-price'),
        img: favoriteBtn.getAttribute('data-product-img'),
        description: favoriteBtn.getAttribute('data-product-desc') || ''
      };

      toggleFavorite(productId, productData);
    });

    // Initialize favorites UI on load
    updateFavoritesUI();
    updateAllFavoriteButtons();
  }


  function toggleFavorite(productId, productData = null) {
    // Try to find product in products array or use provided data
    let product = products.find(p => p.id === productId);

    if (!product && productData) {
      product = {
        id: productId,
        name: productData.name,
        price: parseFloat(productData.price),
        img: productData.img,
        description: productData.description || ''
      };
    }

    if (!product) {
      showToast('خطأ في تحديد المنتج', 'error');
      return;
    }

    // If user is authenticated, persist favorites on server
    if (window.isAuthenticated) {
      const exists = favorites.some(f => f.id === productId);
      if (exists) {
        // remove on server
        fetch(`/api/user/favorites/${productId}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }})
          .then(r => {
            if (r.ok) {
              favorites = favorites.filter(f => f.id !== productId);
              showToast('تم إزالة المنتج من المفضلة', 'success');
              updateFavoritesUI();
              updateAllFavoriteButtons();
            }
          }).catch(() => showToast('حدث خطأ', 'error'));
      } else {
        // add on server
        fetch('/api/user/favorites', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' }, body: JSON.stringify({ product_id: productId }) })
          .then(r => {
            if (r.ok) {
              favorites.push(product);
              showToast('تم إضافة المنتج إلى المفضلة', 'success');
              updateFavoritesUI();
              updateAllFavoriteButtons();
            }
          }).catch(() => showToast('حدث خطأ', 'error'));
      }
    } else {
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
      updateAllFavoriteButtons();
    }
  }

  function updateFavoritesUI() {
    const favoriteCount = document.getElementById('favoritesCount');
    const favoritesItems = document.getElementById('favoritesItems');
    const favoritesFooter = document.getElementById('favoritesFooter');

    if (favoriteCount) {
      favoriteCount.textContent = favorites.length;
    }

    if (!favoritesItems) return;

    // If user is authenticated, try to load server favorites
    if (window.isAuthenticated) {
      fetch('/api/user/favorites', { headers: { 'Accept': 'application/json' } })
        .then(r => r.ok ? r.json() : null)
        .then(data => {
          if (data && Array.isArray(data.favorites)) {
            favorites = data.favorites.map(p => ({ id: p.id, name: p.name, price: p.price, img: p.img, description: p.description }));
            if (favoriteCount) favoriteCount.textContent = favorites.length;
            renderFavoritesItems();
            updateAllFavoriteButtons();
          } else {
            renderFavoritesItems();
          }
        }).catch(() => { renderFavoritesItems(); });
      return;
    }

    if (favorites.length === 0) {
      favoritesItems.innerHTML = '<div class="empty-cart">لا توجد منتجات مفضلة</div>';
      if (favoritesFooter) favoritesFooter.style.display = 'none';
    } else {
      favoritesItems.innerHTML = favorites.map(item => `
        <div class="cart-item">
          <img src="${item.img}" alt="${item.name}" />
          <div class="cart-item-info">
            <div class="cart-item-name">${item.name}</div>
            <div class="cart-item-price">${parseFloat(item.price).toLocaleString()} $</div>
            <div class="cart-item-controls">
              <button class="btn-cta add-to-cart-from-fav"
                      data-id="${item.id}"
                      data-name="${item.name}"
                      data-price="${item.price}"
                      data-img="${item.img}"
                      style="padding: 0.5rem 1rem; font-size: 0.85rem; margin-top: 0.5rem;">
                <i class="fas fa-shopping-cart"></i>
                أضف للسلة
              </button>
              <button class="remove-item remove-fav-btn" data-id="${item.id}" style="margin-top: 0.5rem;">
                <i class="fas fa-trash"></i>
              </button>
            </div>
          </div>
        </div>
      `).join('');

      if (favoritesFooter) favoritesFooter.style.display = 'block';

      // Add event listeners for add to cart buttons in favorites
      document.querySelectorAll('.add-to-cart-from-fav').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
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
      });
      // Add event listeners for remove favorite buttons (server/local-aware)
      document.querySelectorAll('.remove-fav-btn').forEach(btn => {
        btn.addEventListener('click', async function(e) {
          e.preventDefault();
          const pid = Number(this.getAttribute('data-id'));
          if (window.isAuthenticated) {
            try {
              const res = await fetch(`/api/user/favorites/${pid}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'), 'Accept': 'application/json' } });
              if (res.ok) {
                favorites = favorites.filter(f => f.id !== pid);
                updateFavoritesUI();
                updateAllFavoriteButtons();
                showToast('تم إزالة المنتج من المفضلة', 'success');
              }
            } catch (_) { showToast('حدث خطأ', 'error'); }
          } else {
            removeFromFavorites(pid);
          }
        });
      });
    }
  }

  function removeFromFavorites(productId) {
    favorites = favorites.filter(item => item.id !== productId);
    localStorage.setItem('favorites', JSON.stringify(favorites));
    updateFavoritesUI();
    updateAllFavoriteButtons();
    showToast('تم إزالة المنتج من المفضلة', 'success');
  }

  function updateAllFavoriteButtons() {
    document.querySelectorAll('.favorite-btn').forEach(btn => {
      const productId = parseInt(btn.getAttribute('data-product-id'));
      const isFavorite = favorites.some(fav => fav.id === productId);
      const icon = btn.querySelector('i');

      if (isFavorite) {
        icon.className = 'fas fa-heart';
        btn.title = 'إزالة من المفضلة';
        btn.classList.add('active');
      } else {
        icon.className = 'far fa-heart';
        btn.title = 'إضافة إلى المفضلة';
        btn.classList.remove('active');
      }
    });
  }

  // ======= Search Functionality =======
  function initializeSearch() {
    const searchInput = document.querySelector('.search-bar input[type="search"]');
    const searchIcon = document.querySelector('.search-icon');

    if (!searchInput) return;

    // Search on input event (real-time search)
    searchInput.addEventListener('input', debounce(function() {
      performSearch(this.value);
    }, 300));

    // Search on icon click
    if (searchIcon) {
      searchIcon.addEventListener('click', function() {
        performSearch(searchInput.value);
      });
    }

    // Search on Enter key
    searchInput.addEventListener('keypress', function(e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        performSearch(this.value);
      }
    });
  }

  function performSearch(query) {
    const searchTerm = query.trim().toLowerCase();
    const productsContainer = document.getElementById('products-grid');

    if (!productsContainer) return;

    // Get all products from data
    let productsToSearch = products;

    // Also try to get server-rendered products
    const serverProducts = Array.from(productsContainer.querySelectorAll('[data-server-item]')).map(card => {
      const nameEl = card.querySelector('h4, h3');
      const priceEl = card.querySelector('.price');
      const imgEl = card.querySelector('img');
      const descEl = card.querySelector('p');

      if (!nameEl || !priceEl) return null;

      return {
        name: nameEl.textContent.trim(),
        price: parseFloat(priceEl.textContent.replace(/[^0-9.]/g, '')),
        img: imgEl ? imgEl.src : '',
        description: descEl ? descEl.textContent.trim() : '',
        element: card
      };
    }).filter(p => p !== null);

    if (serverProducts.length > 0) {
      productsToSearch = serverProducts;
    }

    if (!searchTerm) {
      // Show all products if search is empty
      if (serverProducts.length > 0) {
        serverProducts.forEach(p => p.element.style.display = '');
      } else {
        displayProducts(products, productsContainer);
      }
      return;
    }

    // Filter products based on search term
    if (serverProducts.length > 0) {
      // Hide/show server-rendered products
      let visibleCount = 0;
      serverProducts.forEach(product => {
        const matchesSearch =
          product.name.toLowerCase().includes(searchTerm) ||
          (product.description && product.description.toLowerCase().includes(searchTerm));

        if (matchesSearch) {
          product.element.style.display = '';
          visibleCount++;
        } else {
          product.element.style.display = 'none';
        }
      });

      if (visibleCount === 0) {
        productsContainer.innerHTML = '<p style="text-align: center; width: 100%; padding: 2rem;">لا توجد نتائج للبحث</p>';
      }
    } else {
      // Filter JS products array
      const filteredProducts = productsToSearch.filter(product =>
        product.name.toLowerCase().includes(searchTerm) ||
        (product.description && product.description.toLowerCase().includes(searchTerm))
      );

      if (filteredProducts.length === 0) {
        productsContainer.innerHTML = '<p style="text-align: center; width: 100%; padding: 2rem;">لا توجد نتائج للبحث</p>';
      } else {
        displayProducts(filteredProducts, productsContainer);
      }
    }

    // Scroll to products
    window.scrollTo({ top: productsContainer.offsetTop - 100, behavior: 'smooth' });
  }

  // Debounce helper function
  function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
      const later = () => {
        clearTimeout(timeout);
        func.apply(this, args);
      };
      clearTimeout(timeout);
      timeout = setTimeout(later, wait);
    };
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
          <button class="favorite-btn"
                  data-product-id="${product.id}"
                  data-product-name="${product.name}"
                  data-product-price="${product.price}"
                  data-product-img="${product.img}"
                  data-product-desc="${product.description || ''}"
                  title="${isFavorite ? 'إزالة من المفضلة' : 'إضافة إلى المفضلة'}">
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


  // ======= فرز المنتجات حسب خيار المستخدم =======
  const sortSelect = document.getElementById("sortSelect");
  if (sortSelect) {
    sortSelect.addEventListener("change", function () {
      const productsContainer = document.getElementById("products-grid");
      if (!productsContainer) return;

      // Check if we have server-rendered products
      const serverProducts = Array.from(productsContainer.querySelectorAll('[data-server-item]')).map(card => {
        const nameEl = card.querySelector('h4, h3');
        const priceEl = card.querySelector('.price');
        const imgEl = card.querySelector('img');
        const descEl = card.querySelector('p');
        const btnEl = card.querySelector('.add-to-cart-btn');

        if (!nameEl || !priceEl) return null;

        return {
          id: btnEl ? parseInt(btnEl.getAttribute('data-id')) : null,
          name: nameEl.textContent.trim(),
          price: parseFloat(priceEl.textContent.replace(/[^0-9.]/g, '')),
          img: imgEl ? imgEl.src : '',
          description: descEl ? descEl.textContent.trim() : '',
          element: card
        };
      }).filter(p => p !== null);

      let sortedProducts = serverProducts.length > 0 ? [...serverProducts] : [...products];

      if (this.value === "price-asc") {
        sortedProducts.sort((a, b) => a.price - b.price);
      } else if (this.value === "price-desc") {
        sortedProducts.sort((a, b) => b.price - a.price);
      }

      // Re-render products in sorted order
      if (serverProducts.length > 0) {
        // Reorder DOM elements
        sortedProducts.forEach(product => {
          productsContainer.appendChild(product.element);
        });
      } else {
        displayProducts(sortedProducts, productsContainer);
      }

      showToast(`تم الترتيب ${this.value === 'price-asc' ? 'من الأقل للأعلى' : this.value === 'price-desc' ? 'من الأعلى للأقل' : 'بشكل افتراضي'}`, 'success');
    });
  }

  const showDealsBtn = document.getElementById("showDealsBtn");
  if (showDealsBtn) {
    showDealsBtn.addEventListener("click", () => {
      if (productsGrid) {
        displayProducts(products, productsGrid);
        window.scrollTo({ top: productsGrid.offsetTop - 70, behavior: "smooth" });
      }
    });
  }


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
    if (!reviewList) return;
    reviewList.innerHTML = reviews.map(generateReviewCard).join("");
  }
  displayReviews();


  // Review form submission is handled later with backend integration


  const newsletterForm = document.getElementById("newsletterForm");
  if (newsletterForm) {
    newsletterForm.addEventListener("submit", async (e) => {
      e.preventDefault();
      const emailInput = document.getElementById("subscriberEmail");
      const email = emailInput ? emailInput.value.trim() : '';
      if (!(email && validateEmail(email))) {
        showToast("يرجى إدخال بريد إلكتروني صالح.", 'error');
        return;
      }

      try {
        const res = await fetch('/newsletter/subscribe', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify({ email })
        });
        if (res.ok) {
          showToast("شكراً لاشتراكك في نشرتنا البريدية!", 'success');
          if (emailInput) emailInput.value = "";
        } else {
          // Fallback toast if backend not ready yet
          showToast("تم استلام طلب الاشتراك (وضع تجريبي).", 'success');
          if (emailInput) emailInput.value = "";
        }
      } catch (err) {
        // Network/back-end unavailable -> fallback UX
        showToast("تم استلام طلب الاشتراك (وضع غير متصل).", 'success');
        if (emailInput) emailInput.value = "";
      }
    });
  }

  function validateEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }

  // ======= Reviews: load from API with graceful fallback =======
  (async function initReviews() {
    try {
      const res = await fetch('/api/reviews', { headers: { 'Accept': 'application/json' } });
      if (res.ok) {
        const data = await res.json();
        if (Array.isArray(data.reviews)) {
          // Replace static reviews with server data
          reviews.length = 0;
          data.reviews.forEach(r => reviews.push({
            id: r.id,
            name: r.name,
            rating: Number(r.rating) || 5,
            comment: r.comment || '',
            date: r.created_at ? new Date(r.created_at).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' }) : '',
            avatar: r.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(r.name)}&background=61b64d&color=fff&size=128`
          }));
          displayReviews();
        }
      }
    } catch (_) { /* silent fallback */ }

    // Hook submit to backend
    const reviewFormEl = document.getElementById('reviewForm');
    if (reviewFormEl) {
      reviewFormEl.addEventListener('submit', async (e) => {
        e.preventDefault();
        const name = reviewFormEl.reviewerName.value.trim();
        const rating = parseInt(reviewFormEl.reviewRating.value);
        const comment = reviewFormEl.reviewComment.value.trim();
        if (!(name && rating && comment)) {
          showToast("يرجى تعبئة جميع الحقول بشكل صحيح.", 'error');
          return;
        }
        try {
          const res = await fetch('/reviews', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name, rating, comment })
          });
          if (res.ok) {
            const data = await res.json();
            const saved = data.review || { id: Date.now(), name, rating, comment, created_at: new Date().toISOString() };
            reviews.unshift({
              id: saved.id,
              name: saved.name,
              rating: Number(saved.rating),
              comment: saved.comment,
              date: new Date(saved.created_at).toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' }),
              avatar: saved.avatar || `https://ui-avatars.com/api/?name=${encodeURIComponent(saved.name)}&background=61b64d&color=fff&size=128`
            });
            displayReviews();
            reviewFormEl.reset();
            showToast("تم إضافة تقييمك بنجاح، شكراً لك!", 'success');
          } else {
            // Fallback to local add
            const newReview = {
              id: Date.now(), name, rating, comment,
              date: new Date().toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' }),
              avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=61b64d&color=fff&size=128`
            };
            reviews.unshift(newReview);
            displayReviews();
            reviewFormEl.reset();
            showToast("تم إضافة تقييمك (وضع تجريبي)", 'success');
          }
        } catch (_) {
          const newReview = {
            id: Date.now(), name, rating, comment,
            date: new Date().toLocaleDateString('ar-EG', { year: 'numeric', month: 'short', day: 'numeric' }),
            avatar: `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=61b64d&color=fff&size=128`
          };
          reviews.unshift(newReview);
          displayReviews();
          reviewFormEl.reset();
          showToast("تم إضافة تقييمك (وضع غير متصل)", 'success');
        }
      }, { once: true });
    }
  })();
