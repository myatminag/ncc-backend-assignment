// Carousel functionality
let currentSlideIndex = 0;
const slides = document.querySelectorAll(".carousel-slide");
const dots = document.querySelectorAll(".dot");

function showSlide(index) {
  const carousel = document.getElementById("carousel");
  carousel.style.transform = `translateX(-${index * 100}%)`;

  dots.forEach((dot) => dot.classList.remove("active"));
  dots[index].classList.add("active");
}

function nextSlide() {
  currentSlideIndex = (currentSlideIndex + 1) % slides.length;
  showSlide(currentSlideIndex);
}

function prevSlide() {
  currentSlideIndex = (currentSlideIndex - 1 + slides.length) % slides.length;
  showSlide(currentSlideIndex);
}

function currentSlide(index) {
  currentSlideIndex = index - 1;
  showSlide(currentSlideIndex);
}

// Auto-advance carousel
setInterval(nextSlide, 5000);

// Modal functionality
function openModal() {
  document.getElementById("joinModal").style.display = "block";
}

function closeModal() {
  document.getElementById("joinModal").style.display = "none";
}

// Close modal when clicking outside
window.onclick = function (event) {
  const modal = document.getElementById("joinModal");
  if (event.target === modal) {
    closeModal();
  }
};

// Form submission
document.getElementById("joinForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const userData = {
    firstName: formData.get("firstName"),
    lastName: formData.get("lastName"),
    email: formData.get("email"),
    password: formData.get("password"),
  };

  // Simulate form submission
  alert(
    `Welcome to FoodFusion, ${userData.firstName}! Your account has been created successfully.`
  );
  closeModal();
  this.reset();
});

// Add animation on scroll
const observerOptions = {
  threshold: 0.1,
  rootMargin: "0px 0px -50px 0px",
};

const observer = new IntersectionObserver(function (entries) {
  entries.forEach((entry) => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = "1";
      entry.target.style.transform = "translateY(0)";
    }
  });
}, observerOptions);

// Observe news cards for animation
document.querySelectorAll(".news-card").forEach((card) => {
  card.style.opacity = "0";
  card.style.transform = "translateY(30px)";
  card.style.transition = "opacity 0.6s ease, transform 0.6s ease";
  observer.observe(card);
});

// Form submission handling
document.getElementById("contactForm").addEventListener("submit", function (e) {
  e.preventDefault();

  const submitBtn = document.getElementById("submitBtn");
  const successMessage = document.getElementById("successMessage");
  const errorMessage = document.getElementById("errorMessage");

  // Hide previous messages
  successMessage.style.display = "none";
  errorMessage.style.display = "none";

  // Disable submit button and show loading state
  submitBtn.disabled = true;
  submitBtn.textContent = "Sending...";

  // Simulate form submission (replace with actual form handling)
  setTimeout(() => {
    // Reset button
    submitBtn.disabled = false;
    submitBtn.textContent = "Send Message";

    // Show success message (in real implementation, handle actual submission)
    successMessage.style.display = "block";

    // Reset form
    this.reset();

    // Scroll to success message
    successMessage.scrollIntoView({ behavior: "smooth", block: "center" });
  }, 2000);
});

// FAQ toggle functionality
function toggleFAQ(button) {
  const faqItem = button.parentElement;
  const answer = faqItem.querySelector(".faq-answer");
  const isActive = button.classList.contains("active");

  // Close all other FAQ items
  document.querySelectorAll(".faq-question").forEach((q) => {
    q.classList.remove("active");
    q.parentElement.querySelector(".faq-answer").classList.remove("active");
  });

  // Toggle current item
  if (!isActive) {
    button.classList.add("active");
    answer.classList.add("active");
  }
}

// Form validation
document.querySelectorAll("input, select, textarea").forEach((field) => {
  field.addEventListener("blur", function () {
    if (this.hasAttribute("required") && !this.value.trim()) {
      this.style.borderColor = "#dc3545";
    } else {
      this.style.borderColor = "#ddd";
    }
  });
});

// Observe elements for animation
document.querySelectorAll(".contact-card, .faq-item").forEach((element) => {
  element.style.opacity = "0";
  element.style.transform = "translateY(30px)";
  element.style.transition = "opacity 0.6s ease, transform 0.6s ease";
  observer.observe(element);
});

// Auth Modal functionality
function openAuthModal(mode = "signup") {
  document.getElementById("authModal").style.display = "block";
  document.body.style.overflow = "hidden";
  switchForm(mode);
}

function closeAuthModal() {
  document.getElementById("authModal").style.display = "none";
  document.body.style.overflow = "auto";
  // Reset forms
  document.getElementById("signUpForm").reset();
  document.getElementById("signInForm").reset();
  document.getElementById("passwordStrength").textContent = "";
  document.getElementById("authSuccessMessage").style.display = "none";
}

function switchForm(mode) {
  const signupToggle = document.getElementById("signupToggle");
  const signinToggle = document.getElementById("signinToggle");
  const signupSection = document.getElementById("signupSection");
  const signinSection = document.getElementById("signinSection");
  const authTitle = document.getElementById("authTitle");
  const authSubtitle = document.getElementById("authSubtitle");

  if (mode === "signup") {
    signupToggle.classList.add("active");
    signinToggle.classList.remove("active");
    signupSection.classList.add("active");
    signinSection.classList.remove("active");
    authTitle.textContent = "Join FoodFusion";
    authSubtitle.textContent = "Start your culinary fusion journey today!";
  } else {
    signinToggle.classList.add("active");
    signupToggle.classList.remove("active");
    signinSection.classList.add("active");
    signupSection.classList.remove("active");
    authTitle.textContent = "Welcome Back";
    authSubtitle.textContent = "Sign in to continue your culinary journey!";
  }
}

// Close modal when clicking outside
window.onclick = function (event) {
  const modal = document.getElementById("authModal");
  if (event.target === modal) {
    closeAuthModal();
  }
};
