<?php

include 'connection.php';

$success = false;

if (isset($_POST["submitBtn"])) {
  $name = $_POST["name"];
  $email = $_POST["email"];
  $phone = $_POST["phone"];
  $subject = $_POST["subject"];
  $message = $_POST["message"];

  $stmt = $connection->prepare("INSERT INTO contact (name, phone, email, subject, message) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $name, $phone, $email, $subject, $message);

  if ($stmt->execute()) {
    $success = true;
    $name = $email = $phone = $subject = $message = "";
  }

  $stmt->close();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Contact Us</title>
</head>

<body>
  <?php include "cookie-consent.php" ?>

  <?php include "auth.php" ?>

  <?php include 'header.php' ?>

  <section class="hero">
    <div class="hero-content">
      <h1>Get In Touch</h1>
      <p>Have a question, recipe request, or feedback? We'd love to hear from you! Our team is here to help you on
        your culinary fusion journey.</p>
    </div>
  </section>

  <section>
    <div class="container">
      <h2 class="section-title">Send Us a Message</h2>
      <div class="form-container">
        <div class="contact-form">
          <form id="contactForm" method="post" action="contact.php">
            <div class="form-group">
              <label for="name">Name *</label>
              <input type="name" id="name" name="name" required>
            </div>

            <div class="form-group">
              <label for="email">Email Address *</label>
              <input type="email" id="email" name="email" required>
            </div>

            <div class="form-group">
              <label for="phone">Phone Number</label>
              <input type="tel" id="phone" name="phone">
            </div>

            <div class="form-group">
              <label for="subject">Subject *</label>
              <input type="text" id="subject" name="subject" required>
            </div>

            <div class="form-group">
              <label for="message">Message *</label>
              <textarea id="message" name="message"
                placeholder="Tell us more about your inquiry, recipe request, or feedback..." required></textarea>
            </div>

            <?php if ($success): ?>
              <p>
                <strong>Thank you!</strong> Your message has been sent successfully. We'll get back to you
                within 24 hours.
              </p>
            <?php else: ?>
              <input type="submit" value="Send Message" class="submit-btn" name="submitBtn" id="submitBtn" />
            <?php endif; ?>

          </form>
        </div>

        <div class="contact-info">
          <div class="contact-card">
            <div class="contact-icon">📧</div>
            <div class="contact-details">
              <h3>Email Us</h3>
              <p><a href="mailto:hello@foodfusion.com">hello@foodfusion.com</a></p>
              <p>We typically respond within 24 hours</p>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-icon">📞</div>
            <div class="contact-details">
              <h3>Call Us</h3>
              <p><a href="tel:+1-555-FUSION">+1 (555) FUSION-1</a></p>
              <p>Mon-Fri: 9AM-6PM EST</p>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-icon">📍</div>
            <div class="contact-details">
              <h3>Visit Us</h3>
              <p>123 Culinary Street<br>Fusion District, NY 10001</p>
              <p>Open for cooking classes & events</p>
            </div>
          </div>

          <div class="contact-card">
            <div class="contact-icon">💬</div>
            <div class="contact-details">
              <h3>Live Chat</h3>
              <p>Available on our website</p>
              <p>Mon-Fri: 9AM-9PM EST</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section>
    <div class="container">
      <h2 class="section-title">Frequently Asked Questions</h2>
      <p class="section-subtitle">Find quick answers to common questions about FoodFusion</p>

      <div class="faq-container">
        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            How do I request a specific fusion recipe?
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            <p>Simply use our contact form above and select "Recipe Request" as your inquiry type. Describe
              the cuisines you'd like to see combined and any specific ingredients or dietary
              requirements. Our chefs love creating custom fusion recipes!</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            Can I submit my own fusion recipe?
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            <p>We encourage our community to share their fusion creations. Send us your recipe through the
              contact form with "Collaboration" selected, and include photos if possible. We feature
              community recipes on our platform.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            How do I book a cooking class or event?
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            <p>Select "Event Inquiry" in the contact form or call us directly. We offer both in-person
              classes at our NYC location and virtual cooking sessions. Group bookings and private events
              are also available.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            Do you accommodate dietary restrictions?
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            <p>Yes! We create fusion recipes for various dietary needs including vegetarian, vegan,
              gluten-free, keto, and more. Just mention your requirements when contacting us.</p>
          </div>
        </div>

        <div class="faq-item">
          <button class="faq-question" onclick="toggleFAQ(this)">
            How can I collaborate with FoodFusion?
            <span class="faq-icon">▼</span>
          </button>
          <div class="faq-answer">
            <p>We're always open to collaborations with chefs, food bloggers, restaurants, and culinary
              enthusiasts. Contact us with "Collaboration" selected and tell us about your idea or
              proposal.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <?php include 'footer.php' ?>
</body>

</html>

<script src="javascript/index.js"></script>