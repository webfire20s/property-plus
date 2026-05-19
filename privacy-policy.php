<?php
// Include configuration or initialization scripts if needed
require_once 'config/db.php'; 
session_start();

// Include the master layout header
include 'includes/navbar.php'; 
?>

<style>
    body {
        background: #f7f7f7;
        font-family: 'Poppins', sans-serif;
        color: #555555;
    }

    .legal-container {
        padding: 140px 0 80px;
    }

    .legal-card {
        background: #ffffff;
        border-radius: 12px;
        border: 1px solid #ebebeb;
        padding: 50px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.02);
    }

    .section-header {
        border-left: 5px solid #2eca6a;
        padding-left: 15px;
        margin-bottom: 35px;
    }

    .section-header h1 {
        font-weight: 700;
        color: #000000;
        margin: 0;
        font-size: 2.2rem;
    }

    .legal-card h4 {
        font-weight: 700;
        color: #000000;
        margin-top: 30px;
        margin-bottom: 15px;
        font-size: 1.25rem;
    }

    .legal-card p {
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 15px;
    }

    .legal-card ul {
        padding-left: 20px;
        margin-bottom: 20px;
    }

    .legal-card ul li {
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 8px;
        list-style-type: square;
    }
    
    .last-updated {
        font-size: 0.85rem;
        color: #888888;
        margin-bottom: 30px;
        font-style: italic;
    }
</style>

<div class="container legal-container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="legal-card shadow-sm" data-aos="fade-up">
                
                <div class="section-header">
                    <h1>Privacy Policy</h1>
                </div>
                
                <div class="last-updated">
                    <i class="bi bi-calendar3 me-1"></i> Last Updated: <?php echo date("d M Y"); ?>
                </div>

                <p>Welcome to <strong>PropertyPlus</strong>. We are committed to protecting your personal information and your right to privacy. If you have any questions or concerns about our policy, or our practices with regards to your personal information, please contact us at movikev9@gmail.com.</p>
                
                <p>When you visit our website and use our services, you trust us with your personal information. We take your privacy very seriously. In this privacy policy, we seek to explain to you in the clearest way possible what information we collect, how we use it, and what rights you have in relation to it.</p>

                <h4>1. Information We Collect</h4>
                <p>We collect personal information that you voluntarily provide to us when registering on the portal, expressing an interest in obtaining information about us or our property listings, or otherwise contacting us.</p>
                <ul>
                    <li><strong>Account Credentials:</strong> Phone numbers (used as unique Login IDs) and secure passwords.</li>
                    <li><strong>Business Profile Data:</strong> Business names, State, District, verified RERA registration numbers, and GSTIN information.</li>
                    <li><strong>Property Details & Media:</strong> Uploaded images, text descriptions, transactional details (sale/rent), pricing, and mandatory legal validation documents submitted for property verification.</li>
                    <li><strong>Activity Logs:</strong> Data relating to your tier allocations, package choices, internal property inquiries, and transaction viewing counters.</li>
                </ul>

                <h4>2. How We Use Your Information</h4>
                <p>We process your information for purposes based on legitimate business interests, the fulfillment of our contract with you, compliance with our legal obligations, and/or your consent. We use the information collected to:</p>
                <ul>
                    <li>Facilitate account creation, user authentication, and secure login workflows.</li>
                    <li>Enforce structural subscription limits and track package performance parameters.</li>
                    <li>Perform necessary automated and manual regulatory compliance verifications on legal attachments and property documentation.</li>
                    <li>Enable network visibility metrics and listing directory features across tiered configurations.</li>
                </ul>

                <h4>3. Information Sharing and Disclosure</h4>
                <p>We only share information with your explicit authorization to fulfill core system requirements, to comply with laws, to provide you with services, or to protect your rights.</p>
                <ul>
                    <li><strong>With Other Verified Members:</strong> Depending on the targeted membership tiers (Basic, Silver, Gold, Platinum), certain profile metrics like phone listings, corporate titles, and geographic districts will be viewable inside the authenticated members index.</li>
                    <li><strong>Legal Requirements:</strong> We may disclose your information where we are legally required to do so in order to comply with applicable law, state real estate regulatory structures (RERA), or active judicial requests.</li>
                </ul>

                <h4>4. Data Security & Storage</h4>
                <p>We use reasonable organizational and technical security measures designed to protect the safety of any personal data we process. However, please also remember that we cannot guarantee that the internet itself is 100% secure. Although we will do our best to protect your personal information, transmission of personal data to and from our portal is at your own risk. You should only access our services within a secure server workspace environment.</p>

                <h4>5. Your Privacy Rights</h4>
                <p>You may review, change, or terminate your system access parameters at any time by updating your master file layout through your Profile Settings workspace console. If you wish to permanently remove or alter your analytical logs or corporate identification indices, please contact our administrative workspace directly.</p>

            </div>
        </div>
    </div>
</div>

<?php 
// Include the master layout footer
include 'includes/footer.php'; 
?>