// Toggle comments visibility
function toggleComments(postId) {
  const commentsSection = document.getElementById(`comments-${postId}`)
  const isHidden = commentsSection.style.display === "none" || commentsSection.style.display === ""

  if (isHidden) {
    commentsSection.style.display = "block"
    loadComments(postId)
  } else {
    commentsSection.style.display = "none"
  }
}

// Load comments for a post
function loadComments(postId) {
  fetch("ajax/get_comments.php?post_id=" + postId)
    .then((response) => response.json())
    .then((comments) => {
      const container = document.getElementById("comments-list-" + postId)
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
            <div><span class="comment-author">${comment.user_name}</span> ${comment.content}</div>
            <div class="comment-date">${timeAgo(comment.created_at)}</div>
          </div>
        `
        container.appendChild(commentDiv)
      })
    })
    .catch((error) => {
      console.error("Error:", error)
      const container = document.getElementById("comments-list-" + postId)
      container.innerHTML = '<div class="error-message">Failed to load comments. Please try again later.</div>'
    })
}

// Add comment function
function addComment(event, postId) {
  event.preventDefault()

  const inputElement = document.getElementById(`comment-input-${postId}`)
  const commentContent = inputElement.value.trim()

  if (!commentContent) return

  // Show loading state
  inputElement.disabled = true
  const submitButton = event.target.querySelector('button[type="submit"]')
  const originalButtonText = submitButton.innerHTML
  submitButton.innerHTML = '<div class="loading"></div>'

  // Simulate comment submission (in a real app, this would be an AJAX call to the server)
  setTimeout(() => {
    // Create new comment element
    const commentsList = document.getElementById(`comments-list-${postId}`)
    const noCommentsMsg = commentsList.querySelector(".no-comments")
    if (noCommentsMsg) {
      commentsList.innerHTML = ""
    }

    const commentDiv = document.createElement("div")
    commentDiv.className = "comment"

    // Get first letter of user name from the comment avatar
    const commentAvatar = document.querySelector(".comment-form .comment-avatar").textContent

    commentDiv.innerHTML = `
      <div class="comment-avatar">${commentAvatar}</div>
      <div class="comment-content">
        <div><span class="comment-author">You</span> ${commentContent}</div>
        <div class="comment-date">Just now</div>
      </div>
    `

    commentsList.appendChild(commentDiv)

    // Reset form
    inputElement.value = ""
    inputElement.disabled = false
    submitButton.innerHTML = originalButtonText

    // Show success message
    showNotification("Comment added successfully!", "success")
  }, 800)
}

// Toggle like function
function toggleLike(button, postId) {
  // Toggle liked class
  button.classList.toggle("liked")

  // Get heart icon and animate it
  const heartIcon = button.querySelector(".heart-icon")
  heartIcon.classList.remove("heart-animation")

  // Trigger reflow to restart animation
  void heartIcon.offsetWidth

  if (button.classList.contains("liked")) {
    heartIcon.classList.add("heart-animation")
    showNotification("Post liked!", "success")
  } else {
    showNotification("Post unliked", "info")
  }
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

// Search products
const searchProducts = () => {
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
}

// Add to cart (placeholder)
const addToCartPlaceholder = (productId) => {
  alert("Fitur keranjang belanja akan segera tersedia!")
}

// Mobile menu toggle
function toggleMobileMenu() {
  const nav = document.querySelector(".nav")
  nav.classList.toggle("mobile-active")
}

// Initialize
document.addEventListener("DOMContentLoaded", () => {
  // Add event listeners for search
  const searchInput = document.getElementById("search")
  if (searchInput) {
    searchInput.addEventListener("input", searchProducts)
  }
})

// Perbaiki fungsi addToCart
const addToCart = (productId, quantity = 1) => {
  // Periksa apakah pengguna sudah login
  const isLoggedIn = document.querySelector('.profile-dropdown') !== null;
  
  if (!isLoggedIn) {
    showNotification('Silakan login terlebih dahulu untuk menambahkan produk ke keranjang', 'error');
    setTimeout(() => {
      window.location.href = 'login.php';
    }, 2000);
    return;
  }

  // Tampilkan loading state
  showNotification('Menambahkan produk ke keranjang...', 'info');
  
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
        // Show success message
        showNotification(data.message, "success");

        // Update cart count if element exists
        const cartCount = document.querySelector(".cart-count");
        if (cartCount) {
          cartCount.textContent = data.cart_count;
        }
      } else {
        showNotification(data.message, "error");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      showNotification("Error adding product to cart", "error");
    });
}

// Show notification function - UPDATED WITH GREEN THEME
function showNotification(message, type = "info") {
  // Create notification element
  const notification = document.createElement("div")
  notification.className = `notification notification-${type}`
  notification.textContent = message

  // Add styles
  notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 8px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        animation: slideIn 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
    `

  // Set background color based on type - GREEN THEME
  switch (type) {
    case "success":
      notification.style.backgroundColor = "#22c55e" // Green
      notification.style.borderLeft = "4px solid #16a34a"
      break
    case "error":
      notification.style.backgroundColor = "#374151" // Dark gray instead of red
      notification.style.borderLeft = "4px solid #1f2937"
      break
    default:
      notification.style.backgroundColor = "#22c55e" // Green as default
      notification.style.borderLeft = "4px solid #16a34a"
  }

  // Add to page
  document.body.appendChild(notification)

  // Remove after 3 seconds
  setTimeout(() => {
    notification.style.animation = "slideOut 0.3s ease"
    setTimeout(() => {
      if (document.body.contains(notification)) {
        document.body.removeChild(notification)
      }
    }, 300)
  }, 3000)
}

// Search products function
const searchProductsFunc = () => {
  const searchInput = document.getElementById("search")
  const searchTerm = searchInput.value.toLowerCase()
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
}

// Like post function
const likePost = (postId) => {
  // Add your like functionality here
  showNotification("Post liked!", "success")
}

// Toggle comments function
function toggleCommentsFunc(postId) {
  const commentsDiv = document.getElementById(`comments-${postId}`)
  if (commentsDiv.style.display === "none" || commentsDiv.style.display === "") {
    commentsDiv.style.display = "block"
    // Load comments here if needed
  } else {
    commentsDiv.style.display = "none"
  }
}

// Add CSS animations - UPDATED WITH GREEN THEME
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
    
    /* Mobile menu styles - GREEN THEME */
    .nav.mobile-active {
        display: flex;
        flex-direction: column;
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: linear-gradient(135deg, #000 0%, #1f2937 100%);
        padding: 1rem;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        border-top: 2px solid #22c55e;
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
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    /* Hover effects for interactive elements */
    .product-card,
    .post,
    .cart-item,
    .order-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    /* Focus states for accessibility */
    .btn:focus,
    .form-control:focus,
    .search-input:focus {
        outline: 2px solid #22c55e;
        outline-offset: 2px;
    }
    
    /* Custom checkbox and radio styles */
    input[type="radio"]:checked {
        accent-color: #22c55e;
    }
    
    input[type="checkbox"]:checked {
        accent-color: #22c55e;
    }

    /* Loading indicator */
    .loading {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 1s linear infinite;
    }

    /* Error message */
    .error-message {
        padding: 1rem;
        background: #f3f4f6;
        color: #374151;
        border-radius: 8px;
        text-align: center;
        margin: 1rem 0;
    }

    /* No comments message */
    .no-comments {
        padding: 1rem;
        color: #6b7280;
        text-align: center;
        font-style: italic;
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

// Format price function
function formatPrice(price) {
  return "Rp " + new Intl.NumberFormat("id-ID").format(price)
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
        // Reload page after a short delay
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

// Smooth scroll to top
function scrollToTop() {
  window.scrollTo({
    top: 0,
    behavior: "smooth",
  })
}

// Add scroll to top button
document.addEventListener("DOMContentLoaded", () => {
  // Create scroll to top button
  const scrollBtn = document.createElement("button")
  scrollBtn.innerHTML = "↑"
  scrollBtn.className = "scroll-to-top"
  scrollBtn.onclick = scrollToTop

  // Add styles for scroll button
  scrollBtn.style.cssText = `
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    color: white;
    border: none;
    font-size: 20px;
    cursor: pointer;
    z-index: 1000;
    opacity: 0;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(34, 197, 94, 0.3);
  `

  document.body.appendChild(scrollBtn)

  // Show/hide scroll button based on scroll position
  window.addEventListener("scroll", () => {
    if (window.pageYOffset > 300) {
      scrollBtn.style.opacity = "1"
      scrollBtn.style.transform = "scale(1)"
    } else {
      scrollBtn.style.opacity = "0"
      scrollBtn.style.transform = "scale(0.8)"
    }
  })

  // Add hover effect
  scrollBtn.addEventListener("mouseenter", () => {
    scrollBtn.style.transform = "scale(1.1)"
    scrollBtn.style.boxShadow = "0 6px 25px rgba(34, 197, 94, 0.4)"
  })

  scrollBtn.addEventListener("mouseleave", () => {
    scrollBtn.style.transform = "scale(1)"
    scrollBtn.style.boxShadow = "0 4px 15px rgba(34, 197, 94, 0.3)"
  })
})

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
