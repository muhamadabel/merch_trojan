// Mobile-First Responsive JavaScript

// Toggle comments visibility
function toggleComments(postId) {
  const commentsSection = document.getElementById(`comments-${postId}`)
  const commentButton = document.querySelector(`[onclick="toggleComments(${postId})"]`)

  if (commentsSection.style.display === "none" || commentsSection.style.display === "") {
    commentsSection.style.display = "block"
    commentsSection.classList.add("show")
    commentButton.innerHTML =
      '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-2.697-.413l-3.178 1.589a.75.75 0 01-1.072-.656l.389-2.335A8 8 0 113 12z"></path></svg> Hide Comments'
    loadComments(postId)
  } else {
    commentsSection.style.display = "none"
    commentsSection.classList.remove("show")
    commentButton.innerHTML =
      '<svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-3.582 8-8 8a8.959 8.959 0 01-2.697-.413l-3.178 1.589a.75.75 0 01-1.072-.656l.389-2.335A8 8 0 113 12z"></path></svg> Comments'
  }
}

// Load comments for a post
function loadComments(postId) {
  const container = document.getElementById("comments-list-" + postId)
  container.innerHTML = '<div class="loading-comments">Loading comments...</div>'

  fetch("ajax/get_comments.php?post_id=" + postId)
    .then((response) => response.json())
    .then((comments) => {
      container.innerHTML = ""

      if (comments.length === 0) {
        container.innerHTML = '<div class="no-comments">No comments yet. Be the first to comment!</div>'
        return
      }

      comments.forEach((comment) => {
        const commentDiv = document.createElement("div")
        commentDiv.className = "comment"
        commentDiv.innerHTML = `
          <div class="comment-avatar">${comment.user_name.charAt(0).toUpperCase()}</div>
          <div class="comment-content">
            <div class="comment-text">
              <span class="comment-author">${comment.user_name}</span>
              ${comment.content}
            </div>
            <div class="comment-date">${timeAgo(comment.created_at)}</div>
          </div>
        `
        container.appendChild(commentDiv)
      })
    })
    .catch((error) => {
      console.error("Error:", error)
      container.innerHTML = '<div class="error-message">Failed to load comments. Please try again later.</div>'
    })
}

// Add comment function
function addComment(event, postId) {
  event.preventDefault()

  const inputElement = document.getElementById(`comment-input-${postId}`)
  const commentContent = inputElement.value.trim()

  if (!commentContent) {
    showNotification("Please enter a comment", "error")
    return
  }

  // Show loading state
  inputElement.disabled = true
  const submitButton = event.target.querySelector('button[type="submit"]')
  const originalButtonText = submitButton.innerHTML
  submitButton.innerHTML = '<div class="loading"></div>'
  submitButton.disabled = true

  // Send AJAX request
  fetch("ajax/add_comment.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `post_id=${postId}&content=${encodeURIComponent(commentContent)}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // Add new comment to the list
        const commentsList = document.getElementById(`comments-list-${postId}`)
        const noCommentsMsg = commentsList.querySelector(".no-comments")
        if (noCommentsMsg) {
          commentsList.innerHTML = ""
        }

        const commentDiv = document.createElement("div")
        commentDiv.className = "comment"
        commentDiv.innerHTML = `
          <div class="comment-avatar">${data.comment.user_name.charAt(0).toUpperCase()}</div>
          <div class="comment-content">
            <div class="comment-text">
              <span class="comment-author">${data.comment.user_name}</span>
              ${data.comment.content}
            </div>
            <div class="comment-date">Just now</div>
          </div>
        `

        // Insert at the beginning (newest first)
        commentsList.insertBefore(commentDiv, commentsList.firstChild)

        // Reset form
        inputElement.value = ""
        showNotification("Comment added successfully!", "success")
      } else {
        showNotification(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showNotification("Error adding comment", "error")
    })
    .finally(() => {
      // Reset button state
      inputElement.disabled = false
      submitButton.innerHTML = originalButtonText
      submitButton.disabled = false
    })
}

// Create comment element
function createCommentElement(comment) {
  const div = document.createElement("div")
  div.className = "comment"
  div.innerHTML = `
        <div class="comment-avatar">
            ${comment.user_name.charAt(0).toUpperCase()}
        </div>
        <div class="comment-content">
            <div>
                <span class="comment-author">${comment.user_name}</span>
                ${comment.content}
            </div>
            <div class="comment-date">${timeAgo(comment.created_at)}</div>
        </div>
    `
  return div
}

// Format date
function formatDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric",
  })
}

// Time ago function for JavaScript
function timeAgo(dateString) {
  const now = new Date()
  const date = new Date(dateString)
  const diff = Math.floor((now - date) / 1000)

  if (diff < 60) return "baru saja"
  if (diff < 3600) return Math.floor(diff / 60) + " menit lalu"
  if (diff < 86400) return Math.floor(diff / 3600) + " jam lalu"
  if (diff < 2592000) return Math.floor(diff / 86400) + " hari lalu"
  return date.toLocaleDateString("id-ID")
}

// Search products with debouncing
let searchTimeout
const searchProducts = () => {
  clearTimeout(searchTimeout)
  searchTimeout = setTimeout(() => {
    const searchTerm = document.getElementById("search").value.toLowerCase()
    const productCards = document.querySelectorAll(".product-card")

    productCards.forEach((card) => {
      const productName = card.querySelector(".product-name").textContent.toLowerCase()
      const productDescription = card.querySelector(".product-description").textContent.toLowerCase()

      if (productName.includes(searchTerm) || productDescription.includes(searchTerm)) {
        card.style.display = "block"
      } else {
        card.style.display = "none"
      }
    })
  }, 300)
}

// Add to cart with debouncing and better error handling
let isAddingToCart = false

const addToCart = (productId, quantity = 1) => {
  // Prevent multiple simultaneous calls
  if (isAddingToCart) {
    return
  }

  isAddingToCart = true

  // Check if user is logged in
  const isLoggedIn = document.querySelector(".profile-dropdown") !== null

  if (!isLoggedIn) {
    showNotification("Silakan login terlebih dahulu untuk menambahkan produk ke keranjang", "error")
    setTimeout(() => {
      window.location.href = "login.php"
    }, 2000)
    isAddingToCart = false
    return
  }

  // Show loading state
  showNotification("Menambahkan produk ke keranjang...", "info")

  fetch("add-to-cart.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `product_id=${productId}&quantity=${quantity}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success")

        // Update cart count if element exists
        const cartCount = document.querySelector(".cart-count")
        if (cartCount) {
          cartCount.textContent = data.cart_count
        }
      } else {
        showNotification(data.message, "error")
      }
    })
    .catch((error) => {
      console.error("Error:", error)
      showNotification("Error adding product to cart", "error")
    })
    .finally(() => {
      setTimeout(() => {
        isAddingToCart = false
      }, 1000)
    })
}

// Enhanced notification system with responsive positioning
let notificationCount = 0
const activeNotifications = new Set()

function showNotification(message, type = "info") {
  if (activeNotifications.has(message)) {
    return
  }

  activeNotifications.add(message)

  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.textContent = message

  // Responsive positioning
  const isMobile = window.innerWidth <= 768
  const topPosition = isMobile ? 20 + notificationCount * 60 : 20 + notificationCount * 70
  notificationCount++

  notification.style.cssText = `
    position: fixed;
    top: ${topPosition}px;
    right: ${isMobile ? "10px" : "20px"};
    left: ${isMobile ? "10px" : "auto"};
    padding: ${isMobile ? "12px 16px" : "15px 20px"};
    border-radius: 8px;
    color: white;
    font-weight: bold;
    z-index: 1000;
    animation: slideIn 0.3s ease;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    max-width: ${isMobile ? "calc(100vw - 20px)" : "300px"};
    word-wrap: break-word;
    font-size: ${isMobile ? "0.9rem" : "1rem"};
  `

  // Set background color based on type
  switch (type) {
    case "success":
      notification.style.backgroundColor = "#22c55e"
      notification.style.borderLeft = "4px solid #16a34a"
      break
    case "error":
      notification.style.backgroundColor = "#374151"
      notification.style.borderLeft = "4px solid #1f2937"
      break
    default:
      notification.style.backgroundColor = "#22c55e"
      notification.style.borderLeft = "4px solid #16a34a"
  }

  document.body.appendChild(notification)

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease"
    setTimeout(() => {
      if (document.body.contains(notification)) {
        document.body.removeChild(notification)
        notificationCount--
        activeNotifications.delete(message)
      }
    }, 300)
  }, 3000)
}

// Mobile menu toggle
function toggleMobileMenu() {
  const nav = document.querySelector(".nav")
  nav.classList.toggle("mobile-active")
}

// Close mobile menu
function closeMobileMenu() {
  const nav = document.querySelector(".nav")
  nav.classList.remove("mobile-active")
}

// Cancel order function
function cancelOrder(orderId) {
  if (!confirm("Are you sure you want to cancel this order?")) {
    return
  }

  fetch("cancel-order.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: `order_id=${orderId}`,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showNotification(data.message, "success")
        setTimeout(() => {
          window.location.reload()
        }, 1500)
      } else {
        showNotification(data.message, "error")
      }
    })
    .catch((error) => {
      showNotification("Error cancelling order", "error")
    })
}

// Format price function
function formatPrice(price) {
  return "Rp " + new Intl.NumberFormat("id-ID").format(price)
}

// Smooth scroll to top with responsive button
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  })
}

// Responsive scroll to top button
function createScrollToTopButton() {
  const scrollBtn = document.createElement("button")
  scrollBtn.innerHTML = "↑"
  scrollBtn.className = "scroll-to-top"
  scrollBtn.onclick = scrollToTop
  scrollBtn.setAttribute("aria-label", "Scroll to top")

  const isMobile = window.innerWidth <= 768

  scrollBtn.style.cssText = `
    position: fixed;
    bottom: ${isMobile ? "15px" : "20px"};
    right: ${isMobile ? "15px" : "20px"};
    width: ${isMobile ? "45px" : "50px"};
    height: ${isMobile ? "45px" : "50px"};
    border-radius: 50%;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    font-size: ${isMobile ? "18px" : "20px"};
    cursor: pointer;
    z-index: 1000;
    opacity: 0;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
    display: flex;
    align-items: center;
    justify-content: center;
  `

  document.body.appendChild(scrollBtn)

  // Show/hide based on scroll position
  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      scrollBtn.style.opacity = "1"
      scrollBtn.style.transform = "scale(1)"
    } else {
      scrollBtn.style.opacity = "0"
      scrollBtn.style.transform = "scale(0.8)"
    }
  })

  // Touch-friendly hover effects
  if ("ontouchstart" in window) {
    scrollBtn.addEventListener("touchstart", () => {
      scrollBtn.style.transform = "scale(1.1)"
    })

    scrollBtn.addEventListener("touchend", () => {
      scrollBtn.style.transform = "scale(1)"
    })
  } else {
    scrollBtn.addEventListener("mouseenter", () => {
      scrollBtn.style.transform = "scale(1.1)"
      scrollBtn.style.boxShadow = "0 6px 25px rgba(34, 197, 94, 0.4)"
    })

    scrollBtn.addEventListener("mouseleave", () => {
      scrollBtn.style.transform = "scale(1)"
      scrollBtn.style.boxShadow = "0 4px 15px rgba(34, 197, 94, 0.3)"
    })
  }

  return scrollBtn
}

// Responsive image lazy loading
function setupLazyLoading() {
  if ("IntersectionObserver" in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          const img = entry.target
          img.src = img.dataset.src
          img.classList.remove("lazy")
          imageObserver.unobserve(img)
        }
      })
    })

    const lazyImages = document.querySelectorAll("img[data-src]")
    lazyImages.forEach((img) => imageObserver.observe(img))
  }
}

// Touch gesture support for mobile
function setupTouchGestures() {
  let startX, startY, distX, distY
  const threshold = 150
  const restraint = 100
  const allowedTime = 300
  let startTime

  document.addEventListener(
    "touchstart",
    (e) => {
      const touchobj = e.changedTouches[0]
      startX = touchobj.pageX
      startY = touchobj.pageY
      startTime = new Date().getTime()
    },
    false,
  )

  document.addEventListener(
    "touchend",
    (e) => {
      const touchobj = e.changedTouches[0]
      distX = touchobj.pageX - startX
      distY = touchobj.pageY - startY
      const elapsedTime = new Date().getTime() - startTime

      if (elapsedTime <= allowedTime) {
        if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint) {
          // Horizontal swipe detected
          if (distX > 0) {
            // Swipe right - could open mobile menu
            const hamburger = document.querySelector(".hamburger")
            const mainNav = document.querySelector(".main-nav")
            if (hamburger && !mainNav.classList.contains("active")) {
              toggleMobileMenu()
            }
          } else {
            // Swipe left - could close mobile menu
            const mainNav = document.querySelector(".main-nav")
            if (mainNav && mainNav.classList.contains("active")) {
              closeMobileMenu()
            }
          }
        }
      }
    },
    false,
  )
}

// Responsive viewport height fix for mobile browsers
function setViewportHeight() {
  const vh = window.innerHeight * 0.01
  document.documentElement.style.setProperty("--vh", `${vh}px`)
}

// Handle orientation change
function handleOrientationChange() {
  setTimeout(() => {
    setViewportHeight()

    // Close mobile menu on orientation change
    const mainNav = document.querySelector(".main-nav")
    if (mainNav && mainNav.classList.contains("active")) {
      closeMobileMenu()
    }
  }, 100)
}

// Initialize responsive features
document.addEventListener("DOMContentLoaded", () => {
  // Set initial viewport height
  setViewportHeight()

  // Create scroll to top button
  createScrollToTopButton()

  // Setup lazy loading
  setupLazyLoading()

  // Setup touch gestures for mobile
  if ("ontouchstart" in window) {
    setupTouchGestures()
  }

  // Add event listeners for search
  const searchInput = document.getElementById("search")
  if (searchInput) {
    searchInput.addEventListener("input", searchProducts)
  }

  // Close mobile menu when clicking nav links
  const navLinks = document.querySelectorAll(".nav a")
  navLinks.forEach((link) => {
    link.addEventListener("click", () => {
      if (window.innerWidth <= 768) {
        closeMobileMenu()
      }
    })
  })

  // Handle form submissions on mobile
  const forms = document.querySelectorAll("form")
  forms.forEach((form) => {
    form.addEventListener("submit", (e) => {
      const submitBtn = form.querySelector('button[type="submit"]')
      if (submitBtn) {
        submitBtn.style.opacity = "0.7"
        submitBtn.disabled = true

        setTimeout(() => {
          submitBtn.style.opacity = "1"
          submitBtn.disabled = false
        }, 2000)
      }
    })
  })
})

// Handle window resize
window.addEventListener("resize", () => {
  setViewportHeight()

  // Close mobile menu if window becomes desktop size
  if (window.innerWidth > 768) {
    closeMobileMenu()
  }

  // Update notification positioning
  const notifications = document.querySelectorAll(".notification")
  const isMobile = window.innerWidth <= 768

  notifications.forEach((notification, index) => {
    const topPosition = isMobile ? 20 + index * 60 : 20 + index * 70
    notification.style.top = `${topPosition}px`
    notification.style.right = isMobile ? "10px" : "20px"
    notification.style.left = isMobile ? "10px" : "auto"
  })
})

// Handle orientation change
window.addEventListener("orientationchange", handleOrientationChange)

// Add CSS animations for mobile
const style = document.createElement("style")
style.textContent = `
  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
  
  /* Mobile-specific animations */
  @media (max-width: 768px) {
    @keyframes slideIn {
      from {
        transform: translateY(-20px);
        opacity: 0;
      }
      to {
        transform: translateY(0);
        opacity: 1;
      }
    }
    
    @keyframes slideOut {
      from {
        transform: translateY(0);
        opacity: 1;
      }
      to {
        transform: translateY(-20px);
        opacity: 0;
      }
    }
  }
  
  /* Loading states */
  .btn.loading {
    position: relative;
    color: transparent;
  }
  
  .btn.loading::after {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid transparent;
    border-top-color: #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
  }
  
  /* Touch feedback */
  @media (hover: none) and (pointer: coarse) {
    .btn:active {
      transform: scale(0.98);
      transition: transform 0.1s ease;
    }
    
    .product-card:active {
      transform: scale(0.98);
      transition: transform 0.1s ease;
    }
  }
  
  /* Improved focus states for mobile */
  @media (max-width: 768px) {
    *:focus {
      outline: 3px solid #22c55e;
      outline-offset: 3px;
    }
  }
`
document.head.appendChild(style)

// Update total function for cart
function updateTotal(quantityInput, price) {
  const quantity = Number.parseInt(quantityInput.value)
  const totalInput = quantityInput.parentNode.querySelector('input[name="total_price"]')
  const newTotal = quantity * price
  totalInput.value = newTotal

  // Update display
  const cartItem = quantityInput.closest(".cart-item")
  const subtotalElement = cartItem.querySelector(".cart-item-subtotal")
  if (subtotalElement) {
    subtotalElement.textContent = formatPrice(newTotal)
  }
}

// Profile Dropdown Functions
function toggleProfileMenu() {
  const dropdown = document.getElementById("profileDropdown")
  const trigger = document.querySelector(".profile-trigger")
  const overlay = document.querySelector(".dropdown-overlay") || createOverlay()

  if (dropdown.classList.contains("show")) {
    closeProfileMenu()
  } else {
    openProfileMenu()
  }
}

function openProfileMenu() {
  const dropdown = document.getElementById("profileDropdown")
  const trigger = document.querySelector(".profile-trigger")
  const overlay = document.querySelector(".dropdown-overlay") || createOverlay()

  dropdown.classList.add("show")
  trigger.classList.add("active")
  overlay.classList.add("show")

  // Close on outside click
  setTimeout(() => {
    document.addEventListener("click", handleOutsideClick)
  }, 100)
}

function closeProfileMenu() {
  const dropdown = document.getElementById("profileDropdown")
  const trigger = document.querySelector(".profile-trigger")
  const overlay = document.querySelector(".dropdown-overlay")

  dropdown.classList.remove("show")
  trigger.classList.remove("active")
  if (overlay) overlay.classList.remove("show")

  document.removeEventListener("click", handleOutsideClick)
}

function createOverlay() {
  const overlay = document.createElement("div")
  overlay.className = "dropdown-overlay"
  overlay.onclick = closeProfileMenu
  document.body.appendChild(overlay)
  return overlay
}

function handleOutsideClick(event) {
  const dropdown = document.querySelector(".profile-dropdown")

  if (!dropdown.contains(event.target)) {
    closeProfileMenu()
  }
}

// Close dropdown on escape key
document.addEventListener("keydown", (event) => {
  if (event.key === "Escape") {
    closeProfileMenu()
  }
})

// Close dropdown when navigating
window.addEventListener("beforeunload", () => {
  closeProfileMenu()
})

// Initialize dropdown on page load
document.addEventListener("DOMContentLoaded", () => {
  // Ensure dropdown is closed on page load
  closeProfileMenu()

  // Add smooth transitions
  const dropdown = document.getElementById("profileDropdown")
  if (dropdown) {
    dropdown.style.transition = "all 0.3s cubic-bezier(0.4, 0, 0.2, 1)"
  }
})

// Export functions for global use
window.toggleComments = toggleComments
window.addComment = addComment
window.addToCart = addToCart
window.cancelOrder = cancelOrder
window.searchProducts = searchProducts
window.showNotification = showNotification
window.toggleMobileMenu = toggleMobileMenu
window.closeMobileMenu = closeMobileMenu
window.toggleProfileMenu = toggleProfileMenu
