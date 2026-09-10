@extends('core.layouts.front')

@section('title', 'Privacy Policy')

@section(
    'meta_description',
    'Read the Restrotix Privacy Policy and learn how we collect, use, store, share and protect personal and business information.'
)

@section('content')

<style>
    /*
    |--------------------------------------------------------------------------
    | Restrotix - Privacy Policy
    |--------------------------------------------------------------------------
    | Simple legal document layout.
    | Styling is scoped only to this page.
    */

    .restrotix-legal-page {
        background: #ffffff;
        color: #374151;
        width: 100%;
        min-height: 100vh;
        padding: 55px 20px 80px;
    }

    .restrotix-legal-container {
        width: 100%;
        max-width: 1100px;
        margin: 0 auto;
    }

    .restrotix-legal-header {
        margin-bottom: 38px;
        text-align: center;
        text-decoration: underline;
    }

    .restrotix-legal-title {
        margin: 0 0 12px;
        color: #111827;
        font-size: 32px;
        line-height: 1.25;
        font-weight: 700;
        text-transform: uppercase;
    }

    .restrotix-legal-updated {
        margin: 0;
        color: #6b7280;
        font-size: 14px;
        line-height: 1.6;
    }

    .restrotix-legal-content {
        color: #374151;
        font-size: 15px;
        line-height: 1.8;
    }

    .restrotix-legal-content h2 {
        margin: 34px 0 12px;
        color: #111827;
        font-size: 18px;
        line-height: 1.5;
        font-weight: 700;
    }

    .restrotix-legal-content h2:first-of-type {
        margin-top: 30px;
    }

    .restrotix-legal-content h3 {
        margin: 24px 0 10px;
        color: #111827;
        font-size: 16px;
        line-height: 1.5;
        font-weight: 600;
    }

    .restrotix-legal-content p {
        margin: 0 0 15px;
        color: #374151;
        font-size: 15px;
        line-height: 1.8;
    }

    .restrotix-legal-content ul {
        margin: 6px 0 18px;
        padding-left: 26px;
        list-style-type: disc;
    }

    .restrotix-legal-content ol {
        margin: 6px 0 18px;
        padding-left: 26px;
    }

    .restrotix-legal-content li {
        margin: 5px 0;
        padding-left: 3px;
        color: #374151;
        font-size: 15px;
        line-height: 1.75;
    }

    .restrotix-legal-content strong {
        color: #1f2937;
        font-weight: 600;
    }

    .restrotix-legal-content a {
        color: #db0913;
        text-decoration: none;
        font-weight: 500;
    }

    .restrotix-legal-content a:hover {
        color: #bd0710;
        text-decoration: underline;
    }

    .restrotix-legal-footer {
        margin-top: 45px;
        padding-top: 25px;
        border-top: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 14px;
    }

    .restrotix-legal-footer p {
        margin: 0 0 8px;
        color: #6b7280;
        font-size: 14px;
    }

    @media (max-width: 768px) {
        .restrotix-legal-page {
            padding: 35px 18px 60px;
        }

        .restrotix-legal-title {
            font-size: 26px;
        }

        .restrotix-legal-content {
            font-size: 14px;
        }

        .restrotix-legal-content p,
        .restrotix-legal-content li {
            font-size: 14px;
            line-height: 1.75;
        }

        .restrotix-legal-content h2 {
            margin-top: 28px;
            font-size: 17px;
        }

        .restrotix-legal-content h3 {
            font-size: 15px;
        }
    }
</style>


<section class="restrotix-legal-page">

    <div class="restrotix-legal-container">


        {{-- =====================================================
            HEADER
        ====================================================== --}}
        <header class="restrotix-legal-header">

            <h1 class="restrotix-legal-title">
                Privacy Policy
            </h1>

           

        </header>


        {{-- =====================================================
            CONTENT
        ====================================================== --}}
        <div class="restrotix-legal-content">

            <p>
                Restrotix respects your privacy and is committed to handling
                personal information responsibly and securely.
            </p>

            <p>
                This Privacy Policy explains how Restrotix (“Restrotix,”
                “we,” “our,” or “us”) may collect, use, store, disclose,
                process, and protect information when you access or use our
                websites, applications, restaurant management software,
                point-of-sale services, marketplace services, dashboards,
                integrations, APIs, support services, and related products
                (collectively, the “Services”).
            </p>

            <p>
                By using Restrotix, you acknowledge the practices described
                in this Privacy Policy.
            </p>


            {{-- 1 --}}
            <h2>
                1. Information We Collect
            </h2>

            <p>
                The information we collect depends on how you interact with
                Restrotix and which Services you use.
            </p>


            <h3>
                Account Information
            </h3>

            <p>
                We may collect:
            </p>

            <ul>
                <li>Full name</li>
                <li>Business name</li>
                <li>Email address</li>
                <li>Phone number</li>
                <li>Username</li>
                <li>Account credentials</li>
                <li>Business address</li>
                <li>Branch information</li>
                <li>Job title or role</li>
                <li>User permissions</li>
                <li>Profile information</li>
            </ul>

            <p>
                Passwords are intended to be stored using appropriate
                security mechanisms rather than as readable plain-text
                passwords.
            </p>


            <h3>
                Business Information
            </h3>

            <p>
                Businesses using Restrotix may provide information such as:
            </p>

            <ul>
                <li>Restaurant or hotel details</li>
                <li>Branch locations</li>
                <li>Menu information</li>
                <li>Products and services</li>
                <li>Inventory records</li>
                <li>Supplier information</li>
                <li>Purchase orders</li>
                <li>Sales information</li>
                <li>Tax configuration</li>
                <li>Billing information</li>
                <li>Staff information</li>
                <li>Operational settings</li>
                <li>Reports and analytics data</li>
            </ul>


            <h3>
                Transaction and Order Information
            </h3>

            <p>
                When Restrotix is used for restaurant operations, we may
                process information relating to:
            </p>

            <ul>
                <li>Orders</li>
                <li>Bills and invoices</li>
                <li>Products and menu items</li>
                <li>Quantities</li>
                <li>Prices</li>
                <li>Discounts</li>
                <li>Taxes</li>
                <li>Payment method</li>
                <li>Payment status</li>
                <li>Order status</li>
                <li>Tables</li>
                <li>Kitchen orders</li>
                <li>Refunds or adjustments</li>
                <li>Transaction timestamps</li>
            </ul>


            <h3>
                Customer Information
            </h3>

            <p>
                Businesses using Restrotix may enter information relating to
                their own customers, including:
            </p>

            <ul>
                <li>Customer name</li>
                <li>Phone number</li>
                <li>Email address</li>
                <li>Delivery information</li>
                <li>Order history</li>
                <li>Loyalty information</li>
                <li>Customer preferences</li>
                <li>Notes supplied by the business</li>
            </ul>

            <p>
                In many circumstances, the restaurant or business using
                Restrotix determines what customer information is entered
                into the platform.
            </p>


            <h3>
                Supplier and Marketplace Information
            </h3>

            <p>
                Where marketplace or procurement features are used, we may
                process information including:
            </p>

            <ul>
                <li>Supplier name</li>
                <li>Business details</li>
                <li>Contact information</li>
                <li>Product listings</li>
                <li>Prices</li>
                <li>Quotations</li>
                <li>Purchase orders</li>
                <li>Delivery information</li>
                <li>Transaction records</li>
                <li>
                    Communications between marketplace participants
                </li>
            </ul>


            <h3>
                Payment Information
            </h3>

            <p>
                Payments may be processed through banks, payment gateways,
                digital wallets, financial institutions, or other payment
                providers.
            </p>

            <p>
                Depending on the payment method, Restrotix may receive
                information such as:
            </p>

            <ul>
                <li>Payment status</li>
                <li>Transaction reference</li>
                <li>Payment provider</li>
                <li>Amount</li>
                <li>Currency</li>
                <li>Payment timestamp</li>
            </ul>

            <p>
                Where payment credentials are entered directly into a
                third-party payment provider's systems, Restrotix may not
                receive or store the complete card, bank account, or wallet
                credentials.
            </p>


            <h3>
                Technical Information
            </h3>

            <p>
                When you use Restrotix, we may automatically collect
                technical information such as:
            </p>

            <ul>
                <li>IP address</li>
                <li>Browser type</li>
                <li>Device type</li>
                <li>Operating system</li>
                <li>Device identifiers</li>
                <li>Login time</li>
                <li>Session information</li>
                <li>Application version</li>
                <li>Error logs</li>
                <li>Security logs</li>
                <li>Activity logs</li>
                <li>Pages or features accessed</li>
                <li>
                    Approximate location derived from technical information
                    where applicable
                </li>
            </ul>


            <h3>
                Support Communications
            </h3>

            <p>
                If you contact Restrotix for support or enquiries, we may
                process:
            </p>

            <ul>
                <li>Your name</li>
                <li>Email address</li>
                <li>Phone number</li>
                <li>Support messages</li>
                <li>Enquiries</li>
                <li>Screenshots</li>
                <li>Attachments</li>
                <li>Technical logs</li>
                <li>Communication history</li>
            </ul>


            {{-- 2 --}}
            <h2>
                2. How We Use Information
            </h2>

            <p>
                We may use information to:
            </p>

            <ul>
                <li>Create and maintain Restrotix accounts</li>
                <li>Authenticate users</li>
                <li>Verify accounts</li>
                <li>Provide POS and billing functionality</li>
                <li>Manage restaurant operations</li>
                <li>Process orders</li>
                <li>Manage inventory</li>
                <li>Manage menus</li>
                <li>Manage branches and users</li>
                <li>Generate reports and analytics</li>
                <li>
                    Provide marketplace and supplier functionality
                </li>
                <li>
                    Facilitate purchase orders and procurement workflows
                </li>
                <li>Process or confirm payments</li>
                <li>Provide requested integrations</li>
                <li>Send operational notifications</li>
                <li>Provide support and onboarding</li>
                <li>Diagnose technical issues</li>
                <li>Improve platform reliability</li>
                <li>Develop and improve features</li>
                <li>Protect accounts and prevent fraud</li>
                <li>Detect unauthorized access</li>
                <li>Maintain audit and security logs</li>
                <li>Enforce our Terms of Use</li>
                <li>Comply with applicable laws</li>
                <li>
                    Respond to lawful government or regulatory requests
                </li>
            </ul>

            <p>
                We may also use aggregated or de-identified information for
                analytics, product improvement, system performance, and
                business insights where the information no longer reasonably
                identifies an individual.
            </p>


            {{-- 3 --}}
            <h2>
                3. Legal and Legitimate Grounds for Processing
            </h2>

            <p>
                Depending on the circumstances and applicable law, Restrotix
                may process personal information because:
            </p>

            <ul>
                <li>
                    It is necessary to provide requested Services
                </li>

                <li>
                    It is necessary to perform a contract
                </li>

                <li>
                    You or your organization have provided appropriate
                    consent
                </li>

                <li>
                    Processing is reasonably necessary for legitimate
                    business and security purposes
                </li>

                <li>
                    Processing is necessary to comply with legal obligations
                </li>

                <li>
                    Another lawful basis applies
                </li>
            </ul>

            <p>
                Where consent is required by applicable law, we will seek
                appropriate consent.
            </p>


            {{-- 4 --}}
            <h2>
                4. Restaurant and Business Customer Data
            </h2>

            <p>
                Businesses using Restrotix may upload or generate personal
                information concerning their customers, staff, suppliers,
                or business partners.
            </p>

            <p>
                In such circumstances, the business using Restrotix may
                determine the purpose for which that information is
                collected and used.
            </p>

            <p>
                The business is responsible for providing necessary privacy
                notices and obtaining consent or another lawful basis where
                required.
            </p>

            <p>
                Restrotix processes such information to provide the Services
                requested by that business and for associated security,
                support, legal, and operational purposes.
            </p>


            {{-- 5 --}}
            <h2>
                5. How We Share Information
            </h2>

            <p>
                <strong>
                    Restrotix does not sell personal information as a
                    business model.
                </strong>
            </p>

            <p>
                We may disclose information where reasonably necessary to
                the following categories of recipients.
            </p>


            <h3>
                Service Providers
            </h3>

            <p>
                We may use third-party service providers for:
            </p>

            <ul>
                <li>Cloud hosting</li>
                <li>Infrastructure</li>
                <li>Data storage</li>
                <li>Email</li>
                <li>SMS or messaging</li>
                <li>Notifications</li>
                <li>Customer support</li>
                <li>Analytics</li>
                <li>Cybersecurity</li>
                <li>Monitoring</li>
                <li>Backup services</li>
                <li>Payment processing</li>
            </ul>

            <p>
                These providers may process information only as reasonably
                necessary to provide their services and in accordance with
                applicable contractual and legal requirements.
            </p>


            <h3>
                Payment Providers
            </h3>

            <p>
                Information necessary for transactions may be shared with
                authorized banks, financial institutions, payment gateways,
                wallets, or other payment processors.
            </p>

            <p>
                Their handling of information may also be governed by their
                own privacy policies.
            </p>


            <h3>
                Marketplace Participants
            </h3>

            <p>
                When buyers and suppliers transact or communicate through
                Restrotix marketplace features, information reasonably
                necessary to complete the transaction may be shared between
                those parties.
            </p>

            <p>
                For example, a supplier may receive business, delivery,
                contact, order, or purchase-order information needed to
                fulfil an order.
            </p>


            <h3>
                Your Organization
            </h3>

            <p>
                Administrators and authorized users of your restaurant,
                branch, company, or organization may access information
                according to permissions configured for the account.
            </p>


            <h3>
                Legal Requirements
            </h3>

            <p>
                We may disclose information if reasonably necessary to:
            </p>

            <ul>
                <li>Comply with applicable law</li>
                <li>Respond to a lawful court order</li>
                <li>
                    Respond to an authorized government request
                </li>
                <li>Protect legal rights</li>
                <li>Investigate fraud</li>
                <li>Protect users</li>
                <li>Address security incidents</li>
                <li>Enforce agreements</li>
            </ul>


            <h3>
                Business Transactions
            </h3>

            <p>
                If Restrotix is involved in a merger, acquisition,
                restructuring, investment, financing, sale of business
                assets, or similar corporate transaction, relevant
                information may be transferred subject to appropriate legal
                safeguards.
            </p>


            {{-- 6 --}}
            <h2>
                6. Cookies and Similar Technologies
            </h2>

            <p>
                Restrotix may use cookies, local storage, session technologies,
                and similar mechanisms to:
            </p>

            <ul>
                <li>Keep users signed in</li>
                <li>Remember preferences</li>
                <li>Maintain secure sessions</li>
                <li>Protect accounts</li>
                <li>Understand website usage</li>
                <li>Diagnose errors</li>
                <li>Improve performance</li>
            </ul>

            <p>
                Some cookies are essential for the Services to function.
            </p>

            <p>
                Where legally required, users may be given appropriate
                choices regarding non-essential cookies.
            </p>

            <p>
                You can also manage certain cookie settings through your
                browser. Disabling essential cookies may prevent some
                Restrotix functions from working correctly.
            </p>


            {{-- 7 --}}
            <h2>
                7. Authentication and “Remember Me”
            </h2>

            <p>
                When you choose a “Remember Me” or similar feature, Restrotix
                may store an authentication token or related information on
                your device so that your session can persist.
            </p>

            <p>
                You should avoid using persistent login functionality on
                shared or public computers.
            </p>

            <p>
                You remain responsible for securing devices through which
                your Restrotix account can be accessed.
            </p>


            {{-- 8 --}}
            <h2>
                8. Data Security
            </h2>

            <p>
                Restrotix uses reasonable administrative, organizational,
                and technical safeguards designed to protect information
                against:
            </p>

            <ul>
                <li>Unauthorized access</li>
                <li>Unlawful disclosure</li>
                <li>Loss</li>
                <li>Misuse</li>
                <li>Alteration</li>
                <li>Destruction</li>
            </ul>

            <p>
                Depending on the Service, safeguards may include:
            </p>

            <ul>
                <li>Authentication controls</li>
                <li>Role-based permissions</li>
                <li>Encryption in transit</li>
                <li>Secure password handling</li>
                <li>Access restrictions</li>
                <li>Security monitoring</li>
                <li>Logging</li>
                <li>Backups</li>
                <li>Infrastructure protections</li>
            </ul>

            <p>
                However, no internet service, computer system, or method of
                electronic storage can be guaranteed to be completely
                secure.
            </p>

            <p>
                Users should use strong credentials and protect their
                accounts and devices.
            </p>


            {{-- 9 --}}
            <h2>
                9. Data Retention
            </h2>

            <p>
                We retain information for as long as reasonably necessary to:
            </p>

            <ul>
                <li>Provide the Services</li>
                <li>Maintain active customer accounts</li>
                <li>Fulfil contracts</li>
                <li>Resolve disputes</li>
                <li>Maintain business records</li>
                <li>
                    Meet tax, accounting, regulatory, or legal requirements
                </li>
                <li>Protect security</li>
                <li>Prevent fraud</li>
                <li>Enforce agreements</li>
            </ul>

            <p>
                Retention periods may differ depending on the type of
                information.
            </p>

            <p>
                When information is no longer reasonably required, we may
                delete, anonymize, or securely archive it as appropriate and
                permitted by law.
            </p>

            <p>
                Backups may retain information for a limited additional
                period before being overwritten or deleted.
            </p>


            {{-- 10 --}}
            <h2>
                10. Account Closure and Data Requests
            </h2>

            <p>
                Subject to applicable law, technical limitations,
                contractual requirements, and legitimate retention
                obligations, users may request:
            </p>

            <ul>
                <li>Access to certain personal information</li>
                <li>Correction of inaccurate information</li>
                <li>Update of account information</li>
                <li>Deletion of eligible personal information</li>
                <li>Closure of an account</li>
                <li>
                    Information about how personal data is being used
                </li>
                <li>
                    Withdrawal of consent where processing depends on
                    consent
                </li>
            </ul>

            <p>
                Requests may require identity or account verification.
            </p>

            <p>
                Certain records may need to be retained despite an
                account-deletion request where required for legal,
                accounting, security, transaction, fraud-prevention,
                or dispute-resolution purposes.
            </p>

            <p>
                For information controlled by a restaurant or business using
                Restrotix, individuals may need to contact that business
                directly.
            </p>


            {{-- 11 --}}
            <h2>
                11. Data Location and International Processing
            </h2>

            <p>
                Restrotix may use cloud infrastructure, hosting providers,
                technical vendors, or service providers located in Nepal or
                other jurisdictions.
            </p>

            <p>
                As a result, information may be processed or stored outside
                the country in which it was originally collected.
            </p>

            <p>
                Where required, Restrotix will take reasonable steps to apply
                appropriate safeguards to cross-border processing.
            </p>


            {{-- 12 --}}
            <h2>
                12. Communications
            </h2>

            <p>
                We may send service-related communications such as:
            </p>

            <ul>
                <li>OTP or verification messages</li>
                <li>Password reset messages</li>
                <li>Security alerts</li>
                <li>Billing notifications</li>
                <li>Order notifications</li>
                <li>Subscription notices</li>
                <li>Product or system updates</li>
                <li>Support messages</li>
                <li>Operational communications</li>
            </ul>

            <p>
                These communications may be necessary for the Services and
                cannot always be disabled while maintaining an active
                account.
            </p>

            <p>
                Where we send optional promotional or marketing
                communications, users may be able to opt out using the method
                provided in the message or by contacting Restrotix.
            </p>


            {{-- 13 --}}
            <h2>
                13. Analytics
            </h2>

            <p>
                We may use analytics technologies to understand how Restrotix
                is used, measure system performance, identify errors, improve
                usability, and develop new features.
            </p>

            <p>
                Where possible and appropriate, analytics may be aggregated
                or de-identified.
            </p>

            <p>
                Third-party analytics providers, if used, may independently
                process limited technical information according to their
                applicable privacy terms.
            </p>


            {{-- 14 --}}
            <h2>
                14. Third-Party Links and Integrations
            </h2>

            <p>
                Restrotix may contain links to or integrations with
                third-party websites, applications, payment providers,
                delivery services, accounting services, social platforms,
                APIs, or other external services.
            </p>

            <p>
                This Privacy Policy does not govern independent third-party
                services.
            </p>

            <p>
                You should review the applicable privacy policies of those
                providers before providing information directly to them.
            </p>


            {{-- 15 --}}
            <h2>
                15. Staff and User Monitoring
            </h2>

            <p>
                Business administrators may have access to operational
                activities performed through accounts belonging to their
                organization, including actions such as orders, billing,
                inventory changes, login activity, or workflow actions.
            </p>

            <p>
                Organizations using Restrotix are responsible for complying
                with applicable employment, privacy, and workplace-monitoring
                requirements regarding their staff.
            </p>


            {{-- 16 --}}
            <h2>
                16. Children
            </h2>

            <p>
                Restrotix is a business software platform and is not intended
                to be independently used by children who are not legally
                capable of entering into applicable business or service
                agreements.
            </p>

            <p>
                We do not intentionally design Restrotix to collect personal
                information directly from children for consumer profiling or
                targeted advertising.
            </p>

            <p>
                If you believe information concerning a child has been
                collected improperly, please contact us.
            </p>


            {{-- 17 --}}
            <h2>
                17. Automated Features and Analytics
            </h2>

            <p>
                Restrotix may use automated systems to produce operational
                analytics, alerts, recommendations, summaries, forecasts,
                or similar business insights.
            </p>

            <p>
                Such outputs may be based on information available through
                the platform and should be reviewed by the business before
                being relied upon for significant operational, financial,
                employment, or legal decisions.
            </p>


            {{-- 18 --}}
            <h2>
                18. Security Incidents
            </h2>

            <p>
                If Restrotix becomes aware of a security incident involving
                personal information, we will investigate and take reasonable
                measures appropriate to the nature and severity of the
                incident.
            </p>

            <p>
                Where notification is required under applicable law,
                Restrotix will take reasonable steps to notify affected
                parties or relevant authorities as required.
            </p>


            {{-- 19 --}}
            <h2>
                19. Changes to This Privacy Policy
            </h2>

            <p>
                We may update this Privacy Policy from time to time to
                reflect:
            </p>

            <ul>
                <li>Changes to Restrotix Services</li>
                <li>New technologies</li>
                <li>
                    Changes to how information is processed
                </li>
                <li>Legal or regulatory requirements</li>
                <li>Security practices</li>
                <li>Business operations</li>
            </ul>

            <p>
                The updated policy will display a revised “Last Updated”
                date.
            </p>

            <p>
                Where a material change requires additional notice or consent
                under applicable law, we will provide it through an
                appropriate method.
            </p>


            {{-- 20 --}}
            <h2>
                20. Nepal Privacy and Electronic Records
            </h2>

            <p>
                Restrotix intends to handle personal and electronic
                information consistently with applicable Nepalese laws and
                legal requirements, including applicable privacy, electronic
                transaction, and other relevant requirements.
            </p>

            <p>
                Nothing in this Privacy Policy is intended to limit rights
                that an individual cannot lawfully waive under applicable
                law.
            </p>


            {{-- 21 --}}
            <h2>
                21. Contact Us
            </h2>

            <p>
                For questions, concerns, account requests, or privacy-related
                requests, please contact Restrotix through the official
                support or enquiry channels available on our website.
            </p>

            <p>
                <strong>Website:</strong>

                <a href="{{ url('/') }}">
                    restrotix.com
                </a>
            </p>

            <p>
                When submitting a privacy request, please provide enough
                information for us to identify the relevant account and
                understand your request. We may need to verify your identity
                before processing certain requests.
            </p>


            {{-- =================================================
                BOTTOM
            ================================================== --}}
            <div class="restrotix-legal-footer">

                 <p class="restrotix-legal-updated">                
                    <strong>Last Updated:</strong> September 9, 2026
                </p>

            </div>

        </div>

    </div>

</section>

@endsection