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

    .legal-card ol {
        padding-left: 20px;
        margin-bottom: 20px;
    }

    .legal-card ol li {
        font-size: 0.95rem;
        line-height: 1.7;
        margin-bottom: 12px;
        font-weight: 600;
        color: #000000;
    }

    .legal-card ol li p {
        font-weight: 400;
        color: #555555;
        margin-top: 5px;
        margin-bottom: 0;
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
                    <h1>Terms and Conditions</h1>
                </div>
                
                <div class="last-updated">
                    <i class="bi bi-calendar3 me-1"></i> Last Updated: <?php echo date("d M Y"); ?>
                </div>

                <p>Welcome to <strong>PropertyPlus</strong>. These terms and conditions outline the rules and regulations for the use of our web application. By accessing this portal, we assume you accept these terms and conditions in full. Do not continue to use PropertyPlus if you do not agree to all of the terms and conditions stated on this page.</p>

                <ol>
                    <li>User Registration and Account Safety
                        <p>To list properties or view internal contact direct logs, users must maintain an active profile file mapping sequence. You are solely responsible for protecting your account credentials and validating data integrity inputs. The system treats all operations coming from your authenticated Phone ID as transactions authorized by you.</p>
                    </li>

                    <li>Subscription Tiers and Access Quotas
                        <p>PropertyPlus implements a strictly programmatic quota engine based on user subscription levels (Listing, Basic, Silver, Gold, Platinum). Each plan restricts the user to strict mathematical limits on:</p>
                        <p style="margin-left: 15px; font-weight: 400; color: #555555;">
                            - Total uploadable listings database size.<br>
                            - Distinct lead interaction tracking counters.<br>
                            - Monthly/yearly allowed property contact visibility clicks.
                        </p>
                        <p>Exceeding plan values will invoke system warnings and prevent additional records processing until upgrade modifications are configured.</p>
                    </li>

                    <li>Listing Guidelines and Verifications
                        <p>All property updates, structural data fields, pricing summaries, and visual asset representations are audited by system moderators. Uploading deceptive titles, pricing structures outside nominal ranges, or providing distorted screenshots of authorization certificates will lead to prompt rejection inside the verification workflow.</p>
                    </li>

                    <li>Document Upload and Compliance Tracking
                        <p>Users must submit matching physical certifications, localized identity papers, or corporate RERA validation sheets for full public presentation access. PropertyPlus administrators reserve the right to flag accounts and revoke visibility indexing if validation checks find inconsistencies, blurry image proofs, or expired file timestamps.</p>
                    </li>

                    <li>Prohibited Use and Behavior
                        <p>You agree not to bypass, reverse engineer, or exploit the programmatic plan verification queries or internal loop counters. Scraping member database entries, harvesting telephone nodes, or submitting programmatic batch forms through automated clients is strictly banned and triggers instant access termination.</p>
                    </li>

                    <li>Limitation of Liability
                        <p>PropertyPlus functions as a real-estate interaction portal designed to catalog regional options and facilitate professional inquiries. We do not inspect, guarantee, or take responsibility for physical properties or individual broker transactions. All negotiations, validation checks, and legal commitments are executed entirely at the user's risk.</p>
                    </li>
                </ol>

                <h4>Contact Support Workspace</h4>
                <p>If you require clarification on subscription allocations, profile locks, or compliance protocols, feel free to contact our support desk via our official inbox at <strong>movikev9@gmail.com</strong>.</p>

            </div>
        </div>
    </div>
</div>

<?php 
// Include the master layout footer
include 'includes/footer.php'; 
?>