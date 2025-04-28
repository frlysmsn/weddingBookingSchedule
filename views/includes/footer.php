    </main>
    <footer class="site-footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><?= SITE_NAME ?></h3>
                    <p>Making your special day perfect.</p>
                </div>
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="index.php">Home</a></li>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <li><a href="index.php?page=booking">Book Now</a></li>
                        <?php else: ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Contact</h3>
                    <p>Email: stritaparishwedding@gmail.com</p>
                    <p>Phone: +639456847868</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.js'></script>
    <script src='https://code.jquery.com/jquery-3.6.3.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/parsley.js/2.9.2/parsley.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11.7.1/dist/sweetalert2.all.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.js'></script>
    <script src='assets/js/main.js'></script>
</body>
</html> 