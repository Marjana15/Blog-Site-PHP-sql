<?php 
include 'partials/header.php';
?>
<section class="form__section">
    <div class="container form__section-container">
        <h1>Contact Us</h1>
        <p>Feel free to reach out to us by filling out the form below. We'll respond as soon as possible!</p>

        <!-- Display Success/Error Message -->
        <?php if (isset($_SESSION['contact-message'])): ?>
            <div class="alert__message <?= $_SESSION['contact-message-type'] ?>">
                <p><?= $_SESSION['contact-message']; unset($_SESSION['contact-message'], $_SESSION['contact-message-type']); ?></p>
            </div>
        <?php endif; ?>

        <form action="contact-logic.php" method="POST">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <textarea name="message" rows="6" placeholder="Your Message" required></textarea>
            <button type="submit" name="submit" class="btn">Send Message</button>
        </form>
    </div>
</section>

<?php
include './partials/footer.php';
?>
