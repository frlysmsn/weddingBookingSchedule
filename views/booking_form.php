<?php
if(!isset($_SESSION['user_id'])) {
    header('Location: index.php?page=login');
    exit;
}
?>

<div class="booking-container">
    <h2>Wedding Booking Form</h2>
    
    <form id="bookingForm" class="needs-validation" novalidate>
        <div class="form-section">
            <h3>Couple Information</h3>
            
            <!-- Groom Information -->
            <div class="form-group">
                <label>Groom's Information</label>
                <input type="text" name="groom_name" class="form-control" required 
                       data-parsley-required="true" placeholder="Full Name">
                <input type="email" name="groom_email" class="form-control" required 
                       data-parsley-type="email" placeholder="Email">
                <input type="tel" name="groom_phone" class="form-control" required 
                       data-parsley-pattern="^[0-9]{11}$" placeholder="Phone Number">
            </div>

            <!-- Bride Information -->
            <div class="form-group">
                <label>Bride's Information</label>
                <input type="text" name="bride_name" class="form-control" required 
                       data-parsley-required="true" placeholder="Full Name">
                <input type="email" name="bride_email" class="form-control" required 
                       data-parsley-type="email" placeholder="Email">
                <input type="tel" name="bride_phone" class="form-control" required 
                       data-parsley-pattern="^[0-9]{11}$" placeholder="Phone Number">
            </div>
        </div>

        <div class="form-section">
            <h3>Wedding Details</h3>
            <div id="calendar"></div>
            <input type="hidden" name="wedding_date" id="selected_date" required>
        </div>

        <div class="form-section">
            <h3>Required Documents</h3>
            <div id="documentUpload" class="dropzone">
                <div class="dz-message">
                    Drop files here or click to upload<br>
                    <small>Required: Baptismal, Confirmation, Marriage License</small>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Submit Booking</button>
    </form>
</div>
